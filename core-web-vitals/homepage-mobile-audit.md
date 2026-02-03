# PageSpeed Insights Audit - Homepage Mobile
## https://hercules-merchandise.de/

**Test Date:** 2026-01-30
**Strategy:** Mobile
**Device:** Moto G Power 2022
**Lighthouse Version:** 13.0.1

---

## Category Scores

| Category | Score | Status |
|----------|-------|--------|
| Performance | 97 | EXCELLENT |
| Accessibility | 96 | EXCELLENT |
| Best Practices | 96 | EXCELLENT |
| SEO | 92 | EXCELLENT |

---

## Core Web Vitals

| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 1.2 s | GOOD |
| Largest Contentful Paint (LCP) | 2.1 s | GOOD |
| Total Blocking Time (TBT) | 0 ms | EXCELLENT |
| Cumulative Layout Shift (CLS) | 0 | EXCELLENT |
| Speed Index | 3.8 s | GOOD |
| Time to Interactive (TTI) | 2.1 s | GOOD |

---

## Network Payload Summary

**Total Page Weight:** 809 KiB

| Resource Type | Requests | Transfer Size |
|---------------|----------|---------------|
| Images | 82 | 579.2 KB (71.6%) |
| Scripts | 16 | 115.6 KB (14.3%) |
| Fonts | 2 | 47.9 KB (5.9%) |
| Document | 1 | 39.7 KB (4.9%) |
| Stylesheets | 3 | 16.2 KB (2.0%) |
| Fetch | 6 | 10.8 KB (1.3%) |

---

## Remaining Optimization Opportunities

### 1. Reduce Unused CSS
**Estimated Savings:** 47 KiB
**Score:** 0.5

**Issue:**
- URL: `https://widget.chathive.app/assets/index-BBjK0QDd.css`
- Wasted: 48,583 bytes (47.4 KB)

**What This Means:**
The ChatHive widget CSS file is being loaded in full, but most of its rules are not being used on the page. This contributes to unnecessary network payload and parse time.

**Recommendation:**
- Consider lazy-loading the ChatHive widget
- Load the widget only when user interaction triggers it
- Use dynamic imports to defer loading until needed

---

### 2. Reduce Unused JavaScript
**Estimated Savings:** 23 KiB
**Score:** 0.5

**Issue:**
- URL: `https://hercules-merchandise.de/_astro/client.D_Es0amM.js`
- Wasted: 23,577 bytes (23.0 KB)

**What This Means:**
The main Astro client bundle contains JavaScript code that is not being executed on initial page load. This increases the bundle size and parse/compile time.

**Recommendation:**
- Implement code splitting to separate critical from non-critical JavaScript
- Use dynamic imports for features not needed immediately
- Consider splitting the bundle by route or component

---

### 3. Improve Image Delivery
**Estimated Savings:** 9 KiB
**Score:** 0.5

**Issue:**
- **File:** `hercules-logo-mobile-optimized.webp`
- **Full URL:** `https://hercules-merchandise.de/images/hercules-logo-mobile-optimized.webp`
- **Current Size:** 13.5 KB
- **Potential Savings:** 9.2 KB (68% reduction)

**What This Means:**
The mobile logo image can be further compressed without significant quality loss.

**Recommendation:**
- Re-compress the image using higher compression settings
- Target size: ~4.3 KB (similar to hercules-logo-small.webp)
- Use tools like `cwebp` with quality setting around 75-80

**Example command:**
```bash
cwebp -q 75 hercules-logo-mobile-optimized.webp -o hercules-logo-mobile-optimized-v2.webp
```

---

### 4. Serves Images with Low Resolution
**Score:** 0 (Binary - Failed)

**Issue:**
- **File:** `hercules-logo-small.webp`
- **Full URL:** `https://hercules-merchandise.de/images/hercules-logo-small.webp`
- **Location:** Cookie banner header (`.cmplz-cookiebanner > .cmplz-header > .cmplz-logo > img`)
- **Current Dimensions:** 87 x 39 pixels (actual image size)
- **Display Dimensions:** 87 x 39 pixels
- **Expected Dimensions for 2x DPR:** 131 x 59 pixels (for Retina displays)
- **Actual Pixels:** 3,393
- **Expected Pixels:** 7,729

**What This Means:**
The cookie banner logo appears at 87x39px but should be 131x59px to account for high-DPI (Retina) displays common on mobile devices. On devices with 2x pixel density, this image will appear blurry.

**HTML Element:**
```html
<img width="50" height="22"
     alt="Hercules Merchandise DE"
     src="/images/hercules-logo-small.webp">
```

**Recommendation:**
1. Create a higher resolution version at 174 x 78 pixels (2x the display size)
2. Use `srcset` to serve appropriate resolution:
```html
<img width="50" height="22"
     alt="Hercules Merchandise DE"
     src="/images/hercules-logo-small.webp"
     srcset="/images/hercules-logo-small.webp 1x,
             /images/hercules-logo-small-2x.webp 2x">
```

---

## Slider Images Analysis

### Mobile Slider Images Loaded
The site correctly loads mobile-optimized slider images:

| Image | Size | Status |
|-------|------|--------|
| slide-1-teamwear-mobile-sm.webp | 7.7 KB | OPTIMIZED |
| slide-2-scarves-mobile-sm.webp | 12.3 KB | OPTIMIZED |
| slide-3-slides-mobile-sm.webp | 3.5 KB | OPTIMIZED |

**Result:** The responsive image loading is working correctly - mobile versions are being served to mobile devices.

---

## Logo Images Loaded

### Main Logos
| Image | Size | Purpose |
|-------|------|---------|
| hercules-logo-original1-2x.webp | 27.0 KB | Desktop header (2x) |
| hercules-logo-mobile-optimized.webp | 13.5 KB | Mobile header |
| hercules-logo-original1.webp | 13.5 KB | Standard header |
| hercules-logo-small.webp | 3.4 KB | Cookie banner (LOW RES ISSUE) |

**Issue Found:** Multiple logo variants are being loaded (3-4 different versions). Consider optimizing to load only the necessary variant for the current viewport.

### Client Logos (Partial List)
All client logos are well-optimized WebP format, ranging from 1.8 KB to 9.7 KB each. No issues detected.

---

## JavaScript Execution

| Metric | Value |
|--------|-------|
| JavaScript Execution Time | 0.3 s |
| Main Thread Work | 1.3 s |

**Status:** EXCELLENT - Very low JavaScript execution time indicates efficient code.

---

## Summary

### What's Working Well
1. Excellent overall performance score (97/100)
2. All Core Web Vitals are in the "Good" range
3. Zero layout shift (CLS = 0)
4. Zero blocking time (TBT = 0ms)
5. Responsive images working correctly for sliders
6. Client logos are well-optimized
7. Minimal JavaScript execution time

### Priority Issues to Address

#### HIGH Priority
1. **Cookie banner logo resolution** - Users with Retina displays see blurry logo
   - Impact: Visual quality, brand perception
   - Effort: Low (create 2x image, update HTML)
   - Fix: Create 174x78px version and use srcset

#### MEDIUM Priority
2. **Logo image optimization** - Further compress mobile logo
   - Impact: 9.2 KB savings
   - Effort: Low (re-compress with higher settings)
   - Fix: Use cwebp with quality 75-80

3. **Multiple logo loading** - Too many logo variants loaded
   - Impact: Unnecessary network requests
   - Effort: Medium (refactor image loading logic)
   - Fix: Load only the necessary variant per viewport

#### LOW Priority
4. **Unused CSS** - ChatHive widget CSS
   - Impact: 47.4 KB (but third-party widget)
   - Effort: Medium (lazy-load widget)
   - Fix: Defer ChatHive loading until user interaction

5. **Unused JavaScript** - Astro client bundle
   - Impact: 23.0 KB
   - Effort: High (code splitting refactor)
   - Fix: Implement dynamic imports and route-based splitting

---

## Next Steps

1. Fix cookie banner logo resolution (IMMEDIATE)
2. Re-compress mobile logo (QUICK WIN)
3. Audit logo loading strategy (MEDIUM TERM)
4. Consider lazy-loading ChatHive widget (MEDIUM TERM)
5. Implement code splitting for Astro bundle (LONG TERM)

---

**Overall Assessment:** The site is performing EXCELLENTLY with a 97/100 score. The remaining issues are minor optimizations that would push the score closer to 100, but the current performance is already delivering a fast, smooth user experience.
