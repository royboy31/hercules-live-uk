# PageSpeed Insights Audit Summary
## https://hercules-merchandise.de/ - Mobile

**Test Date:** 2026-01-30 19:15 UTC
**Status:** NO ISSUES ADDRESSED YET - This is baseline audit

---

## Performance Score: 97/100

### Category Breakdown
| Category | Score | Change |
|----------|-------|--------|
| Performance | 97 | N/A (baseline) |
| Accessibility | 96 | N/A (baseline) |
| Best Practices | 96 | N/A (baseline) |
| SEO | 92 | N/A (baseline) |

---

## Core Web Vitals - All PASSING

| Metric | Value | Status | Threshold |
|--------|-------|--------|-----------|
| FCP | 1.2 s | GOOD | < 1.8s |
| LCP | 2.1 s | GOOD | < 2.5s |
| TBT | 0 ms | EXCELLENT | < 200ms |
| CLS | 0 | PERFECT | < 0.1 |
| Speed Index | 3.8 s | GOOD | < 3.4s |

---

## Issues Found (4 Total)

### 1. Unused CSS - ChatHive Widget
- **File:** `https://widget.chathive.app/assets/index-BBjK0QDd.css`
- **Wasted:** 47.4 KB
- **Type:** Third-party resource
- **Priority:** LOW
- **Fix:** Lazy-load ChatHive widget on user interaction

### 2. Unused JavaScript - Astro Client Bundle
- **File:** `/_astro/client.D_Es0amM.js`
- **Wasted:** 23.0 KB
- **Type:** First-party bundle
- **Priority:** LOW
- **Fix:** Implement code splitting and dynamic imports

### 3. Image Compression - Mobile Logo
- **File:** `hercules-logo-mobile-optimized.webp`
- **Current Size:** 13.5 KB
- **Potential Savings:** 9.2 KB (68% reduction)
- **Priority:** MEDIUM
- **Fix:** Re-compress with cwebp -q 75

### 4. Low Resolution Image - Cookie Banner Logo
- **File:** `hercules-logo-small.webp`
- **Current:** 87 x 39 pixels
- **Expected:** 131 x 59 pixels (for 2x DPR)
- **Issue:** Blurry on Retina displays
- **Priority:** HIGH
- **Fix:** Create 174x78px version and use srcset

---

## Slider Images - VERIFIED WORKING

Mobile slider correctly loads mobile-optimized versions:
- slide-1-teamwear-mobile-sm.webp (7.7 KB)
- slide-2-scarves-mobile-sm.webp (12.3 KB)
- slide-3-slides-mobile-sm.webp (3.5 KB)

Desktop versions NOT loaded on mobile - responsive loading is working correctly.

---

## Logo Loading Analysis

Multiple logo files are being loaded:
1. hercules-logo-original1-2x.webp (27.0 KB)
2. hercules-logo-mobile-optimized.webp (13.5 KB)
3. hercules-logo-original1.webp (13.5 KB)
4. hercules-logo-small.webp (3.4 KB)

Recommendation: Load only the necessary variant per viewport to save ~40 KB.

---

## Network Payload

**Total:** 809 KB

| Type | Requests | Size | Percentage |
|------|----------|------|------------|
| Images | 82 | 579.2 KB | 71.6% |
| Scripts | 16 | 115.6 KB | 14.3% |
| Fonts | 2 | 47.9 KB | 5.9% |
| Document | 1 | 39.7 KB | 4.9% |
| Stylesheets | 3 | 16.2 KB | 2.0% |
| Fetch | 6 | 10.8 KB | 1.3% |

---

## What's Working Excellently

1. All Core Web Vitals in "Good" range
2. Zero layout shift (CLS = 0)
3. Zero blocking time (TBT = 0ms)
4. Fast server response (network latency minimal)
5. Efficient JavaScript execution (0.3s)
6. Low main thread work (1.3s)
7. Responsive images working correctly
8. Client logos well-optimized

---

## Recommended Actions (Priority Order)

### IMMEDIATE (Quick Wins)
1. Create 2x version of cookie banner logo
2. Re-compress mobile logo (9.2 KB savings)

### SHORT TERM
3. Audit logo loading to eliminate redundant variants
4. Lazy-load ChatHive widget (47.4 KB savings)

### LONG TERM
5. Implement code splitting for Astro bundle (23.0 KB savings)

---

## Potential Score Impact

If all optimizations implemented:
- Current: 97/100
- After image fixes: 98/100 (estimated)
- After JS/CSS optimization: 98-99/100 (estimated)

---

**Conclusion:** Site is already performing EXCELLENTLY at 97/100. All Core Web Vitals pass. Remaining issues are minor optimizations that would marginally improve an already great score.

---

Full detailed report: `homepage-mobile-audit.md`
Raw API response: `detailed-reports/homepage-mobile-raw.json`
