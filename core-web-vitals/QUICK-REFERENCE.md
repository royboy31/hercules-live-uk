# Quick Reference - PageSpeed Insights Audit Results

**Site:** https://hercules-merchandise.de/
**Test Date:** 2026-01-30
**Strategy:** Mobile

---

## Scores at a Glance

| Category | Score | Status |
|----------|-------|--------|
| Performance | **88** | 2 points shy of 90 |
| Accessibility | **96** | Excellent |
| Best Practices | **96** | Excellent |
| SEO | **92** | Excellent |

---

## Core Web Vitals

| Metric | Value | Status | Target |
|--------|-------|--------|--------|
| FCP | 1.2s | GOOD | <1.8s |
| LCP | 3.0s | NEEDS IMPROVEMENT | <2.5s |
| TBT | 0ms | EXCELLENT | <200ms |
| CLS | 0 | EXCELLENT | <0.1 |
| Speed Index | **6.6s** | POOR | <3.4s |
| TTI | 3.0s | GOOD | <3.8s |

---

## Priority Fixes (Ranked by Impact)

### 1. SPEED INDEX - 6.6s (Target: <3.4s)
**Impact:** CRITICAL - This is killing the performance score
**Root Causes:**
- Network dependency chains
- Non-optimized images
- Forced reflows

**Actions:**
- Implement all image optimizations below
- Preload critical resources
- Fix forced reflows in JavaScript

---

### 2. IMAGE DELIVERY - 33 KB Savings Available
**Impact:** HIGH - Will improve LCP and Speed Index

#### Fix 1: hercules-logo-mobile-2x.webp
```
Current: 27.9 KB (490x219 pixels)
Display: 245x109 pixels
Savings: 23.6 KB (84.5%)

Issues:
- Oversized by 2x (wastes 21.5 KB)
- Poor compression (wastes 10.7 KB)

Action:
1. Resize to 245x109 pixels
2. Increase WebP compression (q=80-85)
3. Expected final size: ~4-5 KB
```

#### Fix 2: hercules-logo-mobile-optimized.webp
```
Current: 13.5 KB
Savings: 9.2 KB (67.8%)

Issues:
- Poor compression (wastes 7.9 KB)

Action:
1. Increase WebP compression
2. Expected final size: ~4-5 KB
```

#### Fix 3: hercules-logo-small.webp (LOW RESOLUTION)
```
Current: 87x39 pixels (1x)
Expected: 131x59 pixels (1.5x) or 174x78 (2x)
Display: 87x39 CSS pixels

Issue:
- Blurry on high-DPI screens (Retina, etc.)

Action:
1. Create 2x version at 174x78 pixels
2. Use srcset: <img srcset="logo-small.webp 1x, logo-small-2x.webp 2x">
```

---

### 3. UNUSED JAVASCRIPT - 23 KB
**Impact:** MEDIUM

```
File: /_astro/client.D_Es0amM.js
Current: 54.4 KB
Unused: 23 KB (42%)

Actions:
1. Code-split the client bundle
2. Dynamic imports for non-critical features
3. Defer non-essential scripts
```

---

### 4. ACCESSIBILITY - Color Contrast (7 elements)
**Impact:** HIGH (legal/compliance)

**Failed Elements:**
1. `.slide-button` - Insufficient contrast
2. `.product-button` - Insufficient contrast
3. `.contact-trigger-button` - Insufficient contrast
4. `.highlight` (2 instances) - Insufficient contrast
5. `.cmplz-btn.cmplz-accept` - Cookie consent button

**Requirements:**
- Normal text: 4.5:1 minimum
- Large text: 3:1 minimum

**Actions:**
1. Use Chrome DevTools Contrast Checker
2. Adjust button/highlight colors
3. Test with WCAG tools
4. Re-audit

---

### 5. SEO - robots.txt Invalid
**Impact:** LOW (but easy fix)

```
Error: Line 29
Content-Signal: search=yes,ai-train=no
Message: "Unknown directive"

Action:
Remove this line from robots.txt
```

---

## Image Optimization Commands

### Resize and Compress Logo (ImageMagick)
```bash
# Resize mobile logo to exact display size
magick hercules-logo-mobile-2x.webp \
  -resize 245x109 \
  -quality 85 \
  hercules-logo-mobile-optimized-new.webp

# Create 2x version of small logo
magick hercules-logo-small.webp \
  -resize 174x78 \
  -quality 85 \
  hercules-logo-small-2x.webp

# Compress optimized version
magick hercules-logo-mobile-optimized.webp \
  -quality 80 \
  hercules-logo-mobile-optimized-compressed.webp
```

---

## Expected Results After Fixes

| Fix | Expected Improvement |
|-----|---------------------|
| All image optimizations | +3-4 performance points |
| Unused JS reduction | +1-2 performance points |
| Speed Index improvement | +1-2 performance points |
| **TOTAL** | **93-96 performance score** |

---

## What's Already Excellent

- Server response time: 10ms
- Total Blocking Time: 0ms
- Cumulative Layout Shift: 0
- JavaScript execution: 0.2s
- Main thread work: 1.1s
- Accessibility: 96/100
- Best Practices: 96/100

---

## Next Actions

1. **Immediate** (Today):
   - Resize and compress logo images
   - Create 2x version of small logo
   - Fix robots.txt

2. **Short-term** (This Week):
   - Code-split JavaScript bundle
   - Fix color contrast issues
   - Preload LCP image
   - Re-test and verify 90+

3. **Long-term**:
   - Implement srcset across all images
   - Automate image optimization in build
   - Monitor performance continuously

---

## Files

- **Full Report:** `/core-web-vitals/homepage-mobile-audit.md`
- **Raw API Data:** `/core-web-vitals/raw-api-response.json`
- **Summary JSON:** `/core-web-vitals/homepage-summary.json`
- **Inventory:** `/core-web-vitals/urls-inventory.md`

---

## API Credentials

**PageSpeed Insights API Key:**
```
AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4
```

**Re-test Command:**
```bash
curl "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https%3A%2F%2Fhercules-merchandise.de%2F&key=AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4&category=performance&category=accessibility&category=best-practices&category=seo&strategy=mobile"
```

---

**Mission:** Get from 88 to 90+ performance score by optimizing images.
**Target:** 90+ on all categories (Performance, Accessibility, Best Practices, SEO)
**Status:** 88/90 - Almost there!
