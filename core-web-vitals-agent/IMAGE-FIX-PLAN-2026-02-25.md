# Image Fix Plan — UK Product Page
**Date:** 2026-02-25
**Priority:** High — Est. 2,590 KiB savings per page load

---

## Problem Summary

Lighthouse flags **2,590 KiB** wasted on image delivery for the UK product page. The root cause is in the **UK Product Sync Worker** (`workers/product-sync/src/index.ts`), not the frontend Astro page.

The frontend code (image loading strategy, `upgradeMainImage()`, thumbnail click handler) is **identical** between DE and UK. The bug is in how the Worker serves images when KV cache keys are missing.

---

## How the Image Strategy Works (Correct Flow)

Both DE and UK use a two-phase progressive image loading strategy:

**Phase 1 — Fast LCP (Worker WebP):**
- `<img id="main-product-image" src={workerImage(0)} fetchpriority="high" loading="eager">`
- Serves from Cloudflare KV via Worker: small, fast, cached WebP

**Phase 2 — High Quality Upgrade (WordPress original):**
- After `load` event + 1500ms delay, `upgradeMainImage()` runs
- Swaps `src` to `data-full-src` = full WordPress URL (original PNG)
- Preloads first, swaps on `onload` — no visible flicker

**Thumbnails:**
- `src={workerThumb(index)}` = `/image/{slug}/{index}?size=thumb` from Worker (should be 100×100 WebP, ~3–8 KB)
- On click: immediately shows Worker 361px image, then upgrades to WordPress original

---

## Root Cause — UK Worker Image Endpoint

### What DE Worker does when `?size=thumb` is not in KV

```
1. Check Cloudflare edge cache (caches.default) — returns immediately on hit
2. Look up :thumb KV key — miss
3. Fall back to :full KV key — miss
4. Proxy from WordPress, trying size variants in order:
      image-100x100.png.webp  ← WebP first (smallest)
      image-100x100.png
      image-150x150.png.webp
      image-150x150.png
      image-83x83.png.webp
      image-83x83.png
      image-300x300.png.webp
      image-300x300.png
      original.png             ← last resort
5. Cache result back to KV via ctx.waitUntil(cacheImageInKV(...))
6. Cache result to edge cache via ctx.waitUntil(edgeCache.put(...))
7. Return correctly-sized small image
```

### What UK Worker does when `?size=thumb` is not in KV

```
1. NO edge cache check   ← missing
2. Look up :thumb KV key — miss
3. Fall back to :full KV key — HIT → serves full 300×300 PNG (80–326 KB)  ← BUG
4. If nothing in KV → 302 redirect to full 1000×1000 WordPress PNG          ← BUG
```

### Why the :thumb KV keys are empty in UK

UK product images are uploaded as 1000×1080 PNGs. The sync worker tries to cache:
- `image-100x100.png` → if file exceeds `MAX_THUMB_SIZE = 30 KB`, it is rejected
- `image-83x83.png` → fallback, also may not exist or exceed limit

UK WordPress does not have WebP sidecar files (no ShortPixel or similar plugin), so:
- `image-100x100.png.webp` → 404
- `image-83x83.png.webp` → 404

Result: No `:thumb` KV keys are populated. Every thumbnail request falls back to the full image.

DE WordPress has WebP sidecar files. A 100×100 WebP is ~3–8 KB (well under 30 KB) → gets cached properly → thumbnails served correctly.

---

## Files to Change

Only **one file** needs to change:

```
workers/product-sync/src/index.ts
```

The frontend (`src/pages/products/[slug].astro`) is correct and identical to DE. Do not touch it.

---

## Fix 1 — Port Edge Cache to UK Worker

**Location:** UK worker `/image/` endpoint, around line 2532

**DE has this at the top of the `/image/` handler (DE line 2483–2489):**
```typescript
// Check Cloudflare edge cache first — serves without invoking Worker at all on cache hit
const edgeCache = caches.default;
const cacheKey = new Request(request.url, { method: 'GET' });
const edgeCached = await edgeCache.match(cacheKey);
if (edgeCached) {
  return edgeCached;
}
```

**UK currently has:** nothing — no edge cache at all.

Add this block immediately after `if (url.pathname.startsWith('/image/')) {` in the UK worker.

Also need to declare `edgeCache` and `cacheKey` variables (they are used later in Fix 2).

---

## Fix 2 — Port Proxy Fallback Logic to UK Worker

**Location:** UK worker, the `if (!base64Image)` block (~line 2578)

**Current UK code (broken):**
```typescript
if (!base64Image) {
  // Image not cached - try to get original URL from product data and redirect
  const product = await env.PRODUCTS_KV.get<SyncedProduct>(`product:slug:${slug}`, 'json');
  if (product && product.images && product.images[imageIndex]) {
    const originalUrl = product.images[imageIndex].src;
    if (originalUrl) {
      // If resizing requested, use Cloudflare cdn-cgi Image Resizing
      if (requestedWidth || requestedFormat) {
        // ... cdn-cgi logic ...
      }
      return Response.redirect(originalUrl, 302);  // ← sends user to 1000px PNG!
    }
  }
  return new Response('Image not found', { status: 404 });
}
```

**DE code (correct — to port to UK):**
```typescript
if (!base64Image) {
  // Image not cached - fetch from WordPress and proxy directly (no redirect)
  const product = await env.PRODUCTS_KV.get<SyncedProduct>(`product:slug:${slug}`, 'json');
  if (product && product.images && product.images[imageIndex]) {
    const originalUrl = product.images[imageIndex].src;
    if (originalUrl) {
      // If resizing requested, use Cloudflare cdn-cgi Image Resizing
      if (requestedWidth || requestedFormat) {
        const options: string[] = ['fit=contain', 'quality=85'];
        if (requestedWidth) options.push(`width=${requestedWidth}`);
        if (requestedFormat === 'webp') options.push('format=webp');
        try {
          const originalUrlObj = new URL(originalUrl);
          const cdnCgiUrl = `${originalUrlObj.origin}/cdn-cgi/image/${options.join(',')}${originalUrlObj.pathname}`;
          const resizedResponse = await fetch(cdnCgiUrl);
          if (resizedResponse.ok) {
            const headers = new Headers(resizedResponse.headers);
            headers.set('Cache-Control', 'public, max-age=86400');
            headers.set('Access-Control-Allow-Origin', '*');
            return new Response(resizedResponse.body, { status: 200, headers });
          }
        } catch (e) {
          // Fall through to direct proxy below
        }
      }

      // Proxy image directly: try WebP of sized variants first, then original
      // For thumbnail requests, try smaller WordPress crops first
      const sizeVariants = requestedSize === 'thumb'
        ? ['-100x100', '-150x150', '-83x83', '-300x300', '']
        : ['-361x361', '-300x300', ''];

      for (const size of sizeVariants) {
        const sizedUrl = size
          ? originalUrl.replace(/(\.[^.]+)$/, `${size}$1`)
          : originalUrl;
        const webpUrl = /\.(png|jpe?g)$/i.test(sizedUrl) ? sizedUrl + '.webp' : sizedUrl;

        // Try WebP first
        try {
          const res = await fetch(webpUrl, { headers: { 'User-Agent': 'Hercules-Product-Sync/1.0' } });
          if (res.ok) {
            const imageBuffer = await res.arrayBuffer();
            ctx.waitUntil(cacheImageInKV(env.PRODUCTS_KV, imageBuffer, 'image/webp', originalUrl, kvKey, imageIndex, requestedSize || 'full'));
            const proxyWebpResponse = new Response(imageBuffer, {
              headers: {
                'Content-Type': 'image/webp',
                'Cache-Control': 'public, max-age=86400',
                'Access-Control-Allow-Origin': '*',
              },
            });
            ctx.waitUntil(edgeCache.put(cacheKey, proxyWebpResponse.clone()));
            return proxyWebpResponse;
          }
        } catch {}

        // Fall back to original format for this size variant
        try {
          const res = await fetch(sizedUrl, { headers: { 'User-Agent': 'Hercules-Product-Sync/1.0' } });
          if (res.ok) {
            const imageBuffer = await res.arrayBuffer();
            const contentType = res.headers.get('content-type') || 'image/png';
            ctx.waitUntil(cacheImageInKV(env.PRODUCTS_KV, imageBuffer, contentType, originalUrl, kvKey, imageIndex, requestedSize || 'full'));
            const proxyFallbackResponse = new Response(imageBuffer, {
              headers: {
                'Content-Type': contentType,
                'Cache-Control': 'public, max-age=86400',
                'Access-Control-Allow-Origin': '*',
              },
            });
            ctx.waitUntil(edgeCache.put(cacheKey, proxyFallbackResponse.clone()));
            return proxyFallbackResponse;
          }
        } catch {}
      }
    }
  }
  return new Response('Image not found', { status: 404 });
}
```

---

## Fix 3 — Add Edge Cache to KV Hit Path

After the `if (!base64Image)` block, when an image IS found in KV, DE also stores it in the edge cache before returning. Check the DE worker around line 2684 and port this to UK too.

**DE (line ~2684):**
```typescript
ctx.waitUntil(edgeCache.put(cacheKey, kvResponse.clone()));
return kvResponse;
```

This ensures that after the first KV fetch, subsequent requests are served from edge cache without a KV lookup.

---

## What This Fixes

| Issue | Before | After |
|-------|--------|-------|
| Thumbnail images (68×68 display) | 80–326 KB (full KV image served) | 3–15 KB (100×100 or 150×150 WebP/PNG proxied) |
| Missing KV thumb → redirect | 302 to 1000px WP PNG | Proxy from WP, smallest available size |
| Edge cache | None — every request hits Worker | Cloudflare edge cache hit — Worker not invoked |
| Result caching after proxy | Not cached — re-fetches from WP every time | Cached to KV + edge cache via waitUntil |

---

## Expected Lighthouse Improvement

- **Est. savings:** ~2,500 KiB (from 2,590 KiB flagged)
- **Desktop performance:** Should recover from 84 → ~95+ (once 404 header is also fixed)
- **Mobile thumbnail load time:** Significant reduction

---

## Deployment Steps

1. Edit `workers/product-sync/src/index.ts` with both fixes above
2. Deploy UK product-sync worker to production:
```bash
cd /home/kamindu/hercules-headless-uk/workers/product-sync
CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
npx wrangler deploy --env production
```
3. Test a product page thumbnail request directly:
```bash
curl -I "https://hercules-product-sync-uk-production.gilles-86d.workers.dev/image/custom-football-shirts/1?size=thumb"
# Expect: Content-Type: image/webp or image/png, small Content-Length
```
4. Run PageSpeed Insights on the product page and verify image savings

---

## Reference

- **DE worker (correct):** `/home/kamindu/hercules-headless-live/workers/product-sync/src/index.ts` lines 2482–2689
- **UK worker (to fix):** `/home/kamindu/hercules-headless-uk/workers/product-sync/src/index.ts` lines 2532–2680
- **No frontend changes needed** — `src/pages/products/[slug].astro` is correct

---

*Created: 2026-02-25*
