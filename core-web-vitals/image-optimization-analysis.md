# Image Optimization Analysis - Personalisiertes Fussballtrikot

**URL:** https://hercules-merchandise.de/produkte/personalisiertes-fussballtrikot/
**Date:** 2026-01-31
**Focus:** Image-related opportunities and total resource size

---

## Summary: Mobile Optimizations Impact

### Optimizations Deployed:
1. Mobile-optimized design mockup: 27KB (was 128KB) → **-101KB saved**
2. 1x mobile logo: 5.6KB (was 13.8KB) → **-8.2KB saved**
3. Lazy loading for thumbnails → **Implemented**
4. Proper dimensions on related products → **CLS = 0 achieved**

### Results:
- **Performance Score:** 94 → **96** (+2 points) ✅
- **LCP:** 3.0s → **2.6s** (-0.4s, -13.3% improvement) ✅
- **Total Resource Size:** 2,459 KB (2.4 MB)
- **Image Resources:** 2,226 KB (90.5% of total page weight)

---

## Current Image Resource Breakdown

| Metric | Value |
|--------|-------|
| Total Image Requests | 68 |
| Total Image Size | 2,226 KB (2.2 MB) |
| Percentage of Page Weight | 90.5% |
| Average Image Size | 32.7 KB |

---

## Top 10 Largest Images

| # | Filename | Size | Notes |
|---|----------|------|-------|
| 1 | HerculesMerchandisefootballkit58-1.png | 273.6 KB | Largest single image |
| 2 | Hercules-Merchandise-Football-kit-111.png | 265.9 KB | Second largest |
| 3 | HerculesMerchandisefootballkit63-1.png | 201.6 KB | |
| 4 | Hercules_Merchandise_football_kit_72-1.png | 195.5 KB | |
| 5 | Hercules_Merchandise_football_kit_76-1.png | 190.0 KB | |
| 6 | Hercules_Merchandise_football_kit_120-2.png | 181.0 KB | |
| 7 | Hercules_Merchandise_football_kit_115-1.png | 162.3 KB | |
| 8 | Hercules_Merchandise_football_kit_80-1.png | 134.4 KB | |
| 9 | HerculesMerchandisefootballkit4-1.png | 89.1 KB | |
| 10 | Hercules_Merchandise_football_kit_84-1.png | 85.2 KB | |

**Top 10 Combined:** 1,778.6 KB (80% of all image weight from just 10 images)

---

## Remaining Image Issues

### 1. Unsized Images (3 found)
**Impact:** Contributes to potential layout shift
**Score:** 0.5/1.0 (needs improvement)

**Affected Images:**
1. `1642444228_ICONS_FOOTBALL-COLLAR-3-1.png`
2. `1642444237_ICONS_FOOTBALL-COLLAR-1-1.png`
3. `1642444246_ICONS_FOOTBALL-COLLAR-2-1.png`

**Fix:** Add explicit `width` and `height` attributes to these `<img>` tags.

```html
<!-- Before -->
<img src="...ICONS_FOOTBALL-COLLAR-3-1.png" alt="...">

<!-- After -->
<img src="...ICONS_FOOTBALL-COLLAR-3-1.png" alt="..." width="X" height="Y">
```

---

## Optimization Opportunities

### Immediate Actions (High Impact):

#### 1. Fix Unsized Images
- **Effort:** Low (add width/height attributes)
- **Impact:** Prevent layout shifts, improve CLS
- **Expected Result:** Perfect image sizing score (1.0)

#### 2. Optimize Large PNG Files
The top 10 images are all PNG format. Consider:
- **Convert to WebP:** 25-35% additional savings expected
- **Compress PNGs:** Use tools like pngquant or tinypng
- **Expected Savings:** 400-600 KB from top 10 images

Example optimization potential:
```
HerculesMerchandisefootballkit58-1.png (273.6 KB)
→ WebP conversion: ~180-200 KB (-70-90 KB)
→ PNG compression: ~220-240 KB (-30-50 KB)
```

#### 3. Implement Responsive Images
Use `srcset` for different screen sizes:
```html
<img
  src="image-800w.webp"
  srcset="image-400w.webp 400w,
          image-800w.webp 800w,
          image-1200w.webp 1200w"
  sizes="(max-width: 768px) 400px, 800px"
  width="800"
  height="600"
  alt="Football kit">
```

---

## Comparison: Before vs After Mobile Optimizations

| Metric | Baseline | Current | Change |
|--------|----------|---------|--------|
| **Performance Score** | 94/100 | 96/100 | +2 ✅ |
| **LCP** | 3.0s | 2.6s | -0.4s (-13.3%) ✅ |
| **Design Mockup Size** | 128 KB | 27 KB | -101 KB (-79%) ✅ |
| **Logo Size** | 13.8 KB | 5.6 KB | -8.2 KB (-59%) ✅ |
| **CLS** | ? | 0 | Perfect ✅ |
| **Lazy Loading** | Partial | Implemented | ✅ |

**Total Known Savings from Optimizations:** ~109 KB

---

## Next Steps for 100/100 Performance

### Priority 1: Push LCP Below 2.5s (currently 2.6s)
- Preload the LCP image (likely the main product image)
- Optimize critical rendering path
- Consider using a CDN with better geographical coverage

### Priority 2: Further Image Optimization
- Convert top 10 images to WebP format
- Implement responsive images with srcset
- Add proper width/height to 3 unsized images
- **Expected Additional Savings:** 400-600 KB
- **Expected LCP Improvement:** 2.6s → ~2.0-2.2s

### Priority 3: Reduce Unused JavaScript
- Remove 23 KB of unused JavaScript
- Code-split Astro client bundle
- **Expected Impact:** Minimal (0ms savings reported)

---

## Technical Recommendations

### WebP Conversion Script
```bash
# Convert all PNG product images to WebP
for img in *.png; do
  cwebp -q 85 "$img" -o "${img%.png}.webp"
done
```

### Image Sizing Script
```javascript
// Add dimensions to unsized images
const unsizedImages = [
  '1642444228_ICONS_FOOTBALL-COLLAR-3-1.png',
  '1642444237_ICONS_FOOTBALL-COLLAR-1-1.png',
  '1642444246_ICONS_FOOTBALL-COLLAR-2-1.png'
];

// Add width/height based on actual image dimensions
```

---

## Conclusion

**Mobile optimizations were highly successful!**

✅ Performance improved by 2 points (94 → 96)
✅ LCP improved by 13.3% (3.0s → 2.6s)
✅ Image payload reduced by ~109 KB
✅ Zero layout shift achieved (CLS = 0)
✅ Lazy loading successfully implemented

**Remaining work to reach 100/100:**
- Fix 3 unsized images (5 minutes)
- Convert top 10 PNGs to WebP (30 minutes)
- Preload LCP image (5 minutes)
- Remove 23 KB unused JavaScript (1 hour)

**Estimated final result:** 98-100/100 performance, LCP ~2.0-2.2s

**Status:** On track to achieve perfect Core Web Vitals scores! 🎯
