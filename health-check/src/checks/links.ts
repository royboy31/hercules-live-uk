import type { CheckResult, SiteConfig } from '../types.js';
import { result, fetchWithTimeout, fetchJson } from './helpers.js';
import { TIMEOUTS } from '../config/sites.js';

const CAT = '12-Links';
const CONCURRENCY = 10;
const MAX_LISTED = 25;
const MAX_PAGES = 500;

interface LinkRef {
  target: string;      // normalized path (or absolute URL for cross-host)
  source: string;      // page URL the link was found on
}

/** Run fn over items with a concurrency cap. Errors resolve to null. */
async function mapLimit<T, R>(items: T[], limit: number, fn: (item: T) => Promise<R>): Promise<(R | null)[]> {
  const results: (R | null)[] = new Array(items.length).fill(null);
  let next = 0;
  async function worker() {
    while (next < items.length) {
      const i = next++;
      try {
        results[i] = await fn(items[i]);
      } catch {
        results[i] = null;
      }
    }
  }
  await Promise.all(Array.from({ length: Math.min(limit, items.length) }, worker));
  return results;
}

/** All page URLs from the sitemap. Tries sitemap-0.xml, then sitemap.xml (following one index level). */
async function getSitemapPages(site: SiteConfig): Promise<string[]> {
  const locs = (xml: string) =>
    (xml.match(/<loc>\s*([^<]+?)\s*<\/loc>/g) || []).map(m => m.replace(/<\/?loc>|\s/g, ''));

  for (const path of ['/sitemap-0.xml', '/sitemap.xml']) {
    try {
      const res = await fetchWithTimeout(site.url + path, TIMEOUTS.api);
      if (res.status !== 200) continue;
      let urls = locs(await res.text());
      // sitemap index → fetch each child sitemap
      if (urls.length > 0 && urls.every(u => u.endsWith('.xml'))) {
        const children = await mapLimit(urls, CONCURRENCY, async u => {
          const r = await fetchWithTimeout(u, TIMEOUTS.api);
          return r.status === 200 ? locs(await r.text()) : [];
        });
        urls = children.filter(Boolean).flat() as string[];
      }
      if (urls.length > 0) return [...new Set(urls)];
    } catch { /* try next candidate */ }
  }
  return [];
}

/** Normalize an href to a root-relative path if it's internal; null if external/non-page. */
function internalPath(href: string, origin: string): string | null {
  if (!href || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return null;
  if (href.startsWith('#')) return null;
  if (href.startsWith(origin)) return href.slice(origin.length) || '/';
  if (href.startsWith('//')) return null;
  if (href.startsWith('/')) return href;
  return null; // external or relative-to-page (Astro emits root-relative links)
}

export async function checkLinks(site: SiteConfig): Promise<CheckResult[]> {
  const results: CheckResult[] = [];
  const s = site.id;
  const origin = site.url.replace(/\/$/, '');

  // ── 12.1 Discover pages ──
  const sitemapPages = await getSitemapPages(site);
  if (sitemapPages.length === 0) {
    results.push(result('12.1', CAT, s, 'Link crawl: sitemap pages found', 'fail', 'No sitemap URLs found — cannot crawl'));
    return results;
  }
  // WP-served pages aren't in the Astro sitemap; crawl them too (they may 403 from CI IPs — skipped below)
  const wpPages = [site.paths.cart, site.paths.checkout, site.paths.quoteGenerator]
    .map(p => origin + (p.endsWith('/') ? p : p + '/'));
  let pages = [...new Set([...sitemapPages, ...wpPages])];
  let capNote = '';
  if (pages.length > MAX_PAGES) {
    capNote = ` (capped at ${MAX_PAGES} of ${pages.length})`;
    pages = pages.slice(0, MAX_PAGES);
  }

  // ── Crawl pages, harvest internal links ──
  const linkSources = new Map<string, Set<string>>(); // target path → source pages
  const hashLinks: LinkRef[] = [];
  const noSlashLinks: LinkRef[] = [];
  let pagesFetched = 0;
  let pagesSkipped = 0;
  let totalLinks = 0;

  // trailing-slash rule applies to clean page paths under these prefixes
  const slashPrefixes = [...new Set([
    site.paths.products, site.paths.collections, '/blogs', '/blog', '/collections', '/products',
  ])].map(p => p.replace(/\/$/, ''));

  await mapLimit(pages, CONCURRENCY, async (page) => {
    let html: string;
    let status: number;
    try {
      const res = await fetchWithTimeout(page, TIMEOUTS.page);
      status = res.status;
      html = await res.text();
    } catch {
      pagesSkipped++;
      return;
    }
    if (status !== 200) { pagesSkipped++; return; }
    pagesFetched++;

    // anchors only — <link rel=…> head tags (feeds, canonical, hreflang) are not visitor-facing links
    for (const m of html.matchAll(/<a\s[^>]*?href=["']([^"']+)["']/gi)) {
      const href = m[1];
      if (href.includes('#/')) { hashLinks.push({ target: href, source: page }); totalLinks++; continue; }
      const path = internalPath(href, origin);
      if (!path) continue;
      totalLinks++;
      const clean = path.split('#')[0];
      if (!clean) continue;
      const noQuery = clean.split('?')[0];
      if (slashPrefixes.some(p => new RegExp(`^${p}/[a-z0-9\\-_]+$`, 'i').test(noQuery))) {
        noSlashLinks.push({ target: clean, source: page });
      }
      if (!linkSources.has(clean)) linkSources.set(clean, new Set());
      linkSources.get(clean)!.add(page);
    }
  });

  results.push(result('12.1', CAT, s, 'Link crawl: pages scanned', pagesFetched > 0 ? 'pass' : 'fail',
    `${pagesFetched}/${pages.length} pages${capNote}, ${totalLinks} internal links, ${linkSources.size} unique targets` +
    (pagesSkipped > 0 ? `, ${pagesSkipped} pages skipped (non-200/blocked)` : '')));

  // ── Status-check each unique target once ──
  const targets = [...linkSources.keys()];
  const broken: { target: string; code: string; source: string }[] = [];
  const redirected: { target: string; code: string; location: string; source: string }[] = [];
  let firewallSkipped = 0;

  await mapLimit(targets, CONCURRENCY, async (target) => {
    const url = target.startsWith('http') ? target : origin + target;
    let res: Response | null = null;
    for (let attempt = 0; attempt < 2 && !res; attempt++) {
      try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), TIMEOUTS.api);
        try {
          res = await fetch(url, {
            signal: controller.signal,
            headers: { 'User-Agent': 'HerculesHealthCheck/1.0' },
            redirect: 'manual',
          });
        } finally {
          clearTimeout(timer);
        }
      } catch { /* retry once */ }
    }
    const firstSource = [...(linkSources.get(target) || [])][0] || '?';
    if (!res) {
      broken.push({ target, code: 'no response', source: firstSource });
      return;
    }
    if (res.status >= 300 && res.status < 400) {
      redirected.push({ target, code: String(res.status), location: res.headers.get('location') || '?', source: firstSource });
    } else if (res.status === 403) {
      firewallSkipped++; // WP firewall blocking CI IPs — not a link problem
    } else if (res.status >= 400) {
      broken.push({ target, code: String(res.status), source: firstSource });
    }
  });

  const fmt = (items: { target: string; source: string; code?: string; location?: string }[]) =>
    items.slice(0, MAX_LISTED).map(i => {
      const blogTag = i.source.includes('/blog') ? '[blog] ' : '';
      const extra = i.code ? ` (${i.code}${i.location ? ` → ${i.location}` : ''})` : '';
      return `${blogTag}${i.target}${extra} ← ${i.source}`;
    }).join('; ') + (items.length > MAX_LISTED ? `; … and ${items.length - MAX_LISTED} more` : '');

  // ── 12.2 Broken links ──
  results.push(broken.length === 0
    ? result('12.2', CAT, s, 'No broken internal links (404/5xx)', 'pass',
        `${targets.length} targets checked${firewallSkipped ? `, ${firewallSkipped} skipped (firewall 403)` : ''}`)
    : result('12.2', CAT, s, 'No broken internal links (404/5xx)', 'fail',
        `${broken.length} broken link target(s)`, fmt(broken)));

  // ── 12.3 Legacy "#/" links ──
  results.push(hashLinks.length === 0
    ? result('12.3', CAT, s, 'No legacy "#/" links', 'pass', 'Clean')
    : result('12.3', CAT, s, 'No legacy "#/" links', 'fail',
        `${hashLinks.length} legacy hash link(s)`, fmt(hashLinks)));

  // ── 12.4 Redirected links (wasted hop) ──
  results.push(redirected.length === 0
    ? result('12.4', CAT, s, 'No redirected internal links', 'pass', 'No wasted redirect hops')
    : result('12.4', CAT, s, 'No redirected internal links', 'warn',
        `${redirected.length} link target(s) redirect`, fmt(redirected)));

  // ── 12.5 Missing trailing slash ──
  const uniqueNoSlash = [...new Map(noSlashLinks.map(l => [l.target + '\t' + l.source, l])).values()];
  results.push(uniqueNoSlash.length === 0
    ? result('12.5', CAT, s, 'No missing-trailing-slash links', 'pass', 'Clean')
    : result('12.5', CAT, s, 'No missing-trailing-slash links', 'warn',
        `${uniqueNoSlash.length} link(s) missing trailing slash`, fmt(uniqueNoSlash)));

  // ── 12.6 Every KV blog post has a page in the sitemap ──
  if (site.isHeadless) {
    try {
      const posts: any[] = await fetchJson(`${site.syncWorkerUrl}/posts`);
      const slugs = (Array.isArray(posts) ? posts : []).map((p: any) => p.slug).filter(Boolean);
      const missing = slugs.filter(slug => !sitemapPages.some(u => u.includes(`/${slug}/`) || u.endsWith(`/${slug}`)));
      results.push(missing.length === 0
        ? result('12.6', CAT, s, 'All KV blog posts in sitemap', 'pass', `${slugs.length} posts verified`)
        : result('12.6', CAT, s, 'All KV blog posts in sitemap', 'warn',
            `${missing.length}/${slugs.length} KV posts missing from sitemap (stale build?)`, missing.slice(0, MAX_LISTED).join(', ')));
    } catch (e: any) {
      results.push(result('12.6', CAT, s, 'All KV blog posts in sitemap', 'warn', `Could not fetch posts: ${e.message}`));
    }
  }

  return results;
}
