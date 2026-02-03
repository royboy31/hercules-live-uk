# PageSpeed Insights Audit Report - Mobile
**Site:** https://staging.hercules-merchandise.de/
**Test Date:** 2026-01-30
**Strategy:** Mobile

---

## Executive Summary

**Performance Score: 87/100**

Status: Good - Close to optimization target (90+)

---

## Category Scores

| Category | Score | Status |
|----------|-------|--------|
| Performance | 87 | Good (needs 3 points) |
| Accessibility | 96 | Optimized |
| Best Practices | 96 | Optimized |
| SEO | 92 | Optimized |

---

## Core Web Vitals

| Metric | Value | Score | Status |
|--------|-------|-------|--------|
| **First Contentful Paint (FCP)** | 1.0 s (970 ms) | 1.0 | Pass |
| **Largest Contentful Paint (LCP)** | 3.4 s (3,376 ms) | - | Needs Improvement (target: <2.5s) |
| **Total Blocking Time (TBT)** | 0 ms | 1.0 | Pass |
| **Cumulative Layout Shift (CLS)** | 0.014 | 1.0 | Pass |
| **Speed Index** | 5.6 s (5,581 ms) | - | Needs Improvement |

**Critical Finding:** LCP is 3.4s, exceeding the recommended 2.5s threshold by 900ms.

---

## Image Issues - Complete Analysis

### Image Delivery Optimization
**Total Potential Savings: 159 KiB**

All images below are oversized and/or inefficiently compressed:

#### 1. slide-2-scarves.webp
- **Location:** /images/slider/slide-2-scarves.webp
- **Current Size:** 85,752 bytes (83.7 KB)
- **Wasted:** 62,544 bytes (61.1 KB)
- **Issue:** Oversized for display dimensions
- **Priority:** HIGH (largest savings)

#### 2. slide-1-teamwear.webp
- **Location:** /images/slider/slide-1-teamwear.webp
- **Current Size:** 50,844 bytes (49.7 KB)
- **Wasted:** 37,084 bytes (36.2 KB)
- **Issue:** Oversized for display dimensions
- **Priority:** HIGH (LCP element)
- **Note:** This is the Largest Contentful Paint element

#### 3. hercules-logo-mobile-2x.webp
- **Location:** /images/hercules-logo-mobile-2x.webp
- **Current Size:** 41,544 bytes (40.6 KB)
- **Wasted:** 37,075 bytes (36.2 KB)
- **Issue:** Oversized dimensions (564x252 vs displayed 245x109)
- **Priority:** HIGH

#### 4. slide-3-slides.webp
- **Location:** /images/slider/slide-3-slides.webp
- **Current Size:** 26,170 bytes (25.6 KB)
- **Wasted:** 19,087 bytes (18.6 KB)
- **Issue:** Dimension mismatch
- **Priority:** MEDIUM

#### 5. Witham-Town-FC.png
- **Location:** /images/logos/Witham-Town-FC.png
- **Current Size:** 13,295 bytes (13.0 KB)
- **Wasted:** 7,460 bytes (7.3 KB)
- **Issue:** PNG format instead of WebP
- **Priority:** MEDIUM (convert to WebP)

#### 6. hercules-logo-small.webp
- **Location:** /images/hercules-logo-small.webp
- **Issue:** Low resolution (served at exactly 87x39, no retina support)
- **Priority:** LOW (small file, but could be improved for retina displays)

---

## Other Major Opportunities

### 1. Reduce Unused JavaScript
**Potential Savings: 23 KiB**
- **File:** client.D_Es0amM.js
- **Total Size:** 54,683 bytes
- **Unused:** 23,584 bytes (43% of file)
- **Impact:** 150 ms potential LCP improvement
- **Recommendation:** Code split and lazy-load non-critical features

### 2. Reduce Unused CSS
**Potential Savings: 47 KiB**
- **File:** widget.chathive.app/assets/index-BBjK0QDd.css
- **Unused:** 48,583 bytes (100% unused in initial viewport)
- **Impact:** Minimal on LCP but reduces bundle size
- **Recommendation:** Load chat widget CSS only when widget is initialized

### 3. Main Thread Work
**Total Time: 1.4 seconds**

Breakdown:
- Other: 695.6 ms
- Style & Layout: 263.2 ms
- Script Evaluation: 240.0 ms
- Paint/Composite/Render: 134.1 ms
- Parse HTML: 36.2 ms

**Recommendation:** Optimize JavaScript execution and reduce layout complexity

---

## Optimization Priority Roadmap

### Critical (To reach 90+ score)
1. **Optimize LCP image (slide-1-teamwear.webp)**
   - Resize to actual display dimensions
   - Further compress WebP
   - Expected impact: -900ms LCP (3.4s → 2.5s)

2. **Optimize all slider images**
   - Implement responsive image srcset
   - Resize to appropriate dimensions
   - Lazy-load off-screen slides
   - Expected savings: 159 KiB total

3. **Code-split JavaScript**
   - Extract and defer non-critical code
   - Expected savings: 23 KiB

### High Priority (Additional improvements)
4. **Defer chat widget CSS**
   - Load asynchronously
   - Expected savings: 47 KiB

5. **Convert PNG to WebP**
   - Convert Witham-Town-FC.png
   - Expected savings: 7.5 KiB

### Medium Priority (Polish)
6. **Optimize main thread work**
   - Review and optimize JavaScript execution
   - Reduce style calculations

---

## Technical Details

**DOM Statistics:**
- Total elements: 1,327
- Maximum depth: 13 levels
- Maximum children on single element: 50

**Server Performance:**
- Server response time: 13 ms (excellent)

**Passing Audits:**
- Valid source maps
- Valid hreflang
- Crawlable links
- Descriptive link text
- Proper charset declaration
- Meta description present
- No layout traps
- No deprecated ARIA roles

---

## Expected Impact

If all critical optimizations are implemented:

**Estimated Performance Score: 93-96**

- LCP: 3.4s → 2.1s (below 2.5s threshold)
- Speed Index: 5.6s → 3.8s (estimated)
- Total bundle size reduction: ~230 KiB
- Main thread time reduction: ~200ms

---

## Next Steps

1. Optimize slider images (priority: slide-1-teamwear.webp)
2. Implement responsive image srcset for all slider images
3. Code-split and lazy-load non-critical JavaScript
4. Re-test with PageSpeed Insights
5. Verify 90+ performance score achieved

---

**Report Generated:** 2026-01-30
**Auditor:** PageSpeed Insights API v5
**API Key Used:** AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4
