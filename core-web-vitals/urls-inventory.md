# URL Inventory - Hercules UK Staging Site

Last Updated: February 10, 2026

## Test Results Summary

| URL | Perf (M) | Perf (D) | A11y | BP | SEO | Status | Priority Issues |
|-----|----------|----------|------|----|----|--------|-----------------|
| [Homepage](https://staging.hercules-merchandise.co.uk/) | N/A | 70 | 63 | 75 | 100 | Critical | A11y: Color contrast (15 elements), Perf: 243KB unused JS |
| [Product - Baseball Cap](https://staging.hercules-merchandise.co.uk/products/baseball-cap/) | 39 | 34 | 81 | 92 | 100 | Critical | Perf: LCP 4.2-4.4s, Images 4,390KB, No HSTS |
| [Collection - Sportswear](https://staging.hercules-merchandise.co.uk/collections/custom-printed-sportswear/) | N/A | N/A | N/A | N/A | N/A | Incomplete | Limited data from API, 140+ requests |
| [Blog Listing](https://staging.hercules-merchandise.co.uk/blogs/uk/) | 47 | N/A | 64 | 75 | 100 | Critical | A11y: Color contrast, Perf: LCP 6.8s, lazy-load on LCP image |
| [Blog Post - Veja Scarves](https://staging.hercules-merchandise.co.uk/blogs/uk/eco-friendly-custom-scarves-for-veja-made-in-europe-from-recycled-cotton/) | N/A | 92 | 0 | 75 | 100 | Critical | A11y: FAILED (0/100), Perf (M): LCP 10.6s |

## Column Legend
- **Perf (M)** = Performance Score (Mobile)
- **Perf (D)** = Performance Score (Desktop)
- **A11y** = Accessibility
- **BP** = Best Practices
- **SEO** = SEO Score

## Status Legend
- Critical = Major issues requiring immediate attention (Performance <50 or Accessibility <70)
- Needs Work = Some issues to address (Any score <90)
- Good = Minor improvements possible (All scores 90+)
- Incomplete = API returned partial data

---

## Current Status Summary

**Tested:** 5 URLs
**Optimized (90+):** 0 URLs
**Good (70-89):** 0 URLs
**Needs Work (<70):** 5 URLs
**Incomplete:** 1 URL (partial data)

---

## Cross-Page Critical Issues

### 1. Accessibility - Color Contrast (ALL PAGES)
**Severity:** Critical | **WCAG 2.1 Violation**

Failing elements across site:
- Navigation links (#00aeef): 2.52:1 (needs 4.5:1)
- CTA buttons (#10c99e): 2.12:1 (needs 4.5:1)
- Breadcrumbs: 4.22:1 (needs 4.5:1)
- Blog dates/links: 3.03:1 (needs 4.5:1)

**Action Required:** Update brand color palette to meet WCAG AA standards

### 2. Performance - Image Optimization (Product/Blog Pages)
**Severity:** Critical

- Product page: 4.4-5.5 MB image payload
- Thumbnails oversized: 1000x1000 displayed at 68x68
- LCP times: 4.2-10.6 seconds (target: <2.5s)

**Action Required:** Implement responsive images, modern formats, proper sizing

### 3. Performance - JavaScript Overhead (ALL PAGES)
**Severity:** High

- Unused JavaScript: 243-244 KiB
- GTM: 39-43% unused code
- Long tasks: 72-107ms from third-party scripts

**Action Required:** Code splitting, defer non-critical scripts

### 4. Security - Missing Headers (ALL PAGES)
**Severity:** Medium

- No HSTS header
- No COOP header
- No CSP header (High severity on blog listing)

**Action Required:** Configure security headers on server

---

## Detailed Reports

Individual detailed reports available in:
- `/home/kamindu/hercules-headless-uk/core-web-vitals/staging-audit-2026-02-10.md` (comprehensive audit)

---

## Testing Notes

- Test Date: February 10, 2026
- API: PageSpeed Insights v5
- Some tests returned incomplete data (marked as N/A)
- Recommend re-testing with Lighthouse CLI for complete metrics
- Mobile emulation: Moto G Power 2022

---

## Next Actions

1. Fix accessibility color contrast (P0 - Critical)
2. Optimize product page images (P0 - Critical)
3. Remove lazy loading from LCP images (P0 - Critical)
4. Implement security headers (P1 - High)
5. Reduce unused JavaScript (P1 - High)
6. Re-test all URLs after fixes

---

*Report generated: February 10, 2026*
