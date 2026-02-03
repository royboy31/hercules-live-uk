# Detailed Image Issues - hercules-merchandise.de

**Source:** PageSpeed Insights API
**Audit Date:** 2026-01-30
**Strategy:** Mobile

---

## Summary

**Total Potential Savings:** 33 KB (32,514 bytes)
**Number of Flagged Images:** 3
**Primary Issue:** Images oversized for display dimensions + poor compression

---

## Image 1: hercules-logo-mobile-2x.webp

### Current State
- **URL:** `https://hercules-merchandise.de/images/hercules-logo-mobile-2x.webp`
- **File Size:** 27.9 KB (28,608 bytes)
- **Actual Dimensions:** 490 x 219 pixels
- **Display Dimensions:** 245 x 109 pixels
- **Total Potential Savings:** 23.6 KB (24,137 bytes) - 84.5% reduction

### Issues Detected

#### Issue 1A: Oversized Image
- **Problem:** Image is 2x larger than display size (490x219 vs 245x109)
- **Wasted Bytes:** 21,457 bytes (21.5 KB)
- **Cause:** Serving high-resolution image without srcset
- **PageSpeed Message:** "This image file is larger than it needs to be (490x219) for its displayed dimensions (245x109). Use responsive images to reduce the image download size."

#### Issue 1B: Poor Compression
- **Problem:** WebP compression factor too low
- **Wasted Bytes:** 10,723 bytes (10.7 KB)
- **PageSpeed Message:** "Increasing the image compression factor could improve this image's download size."

### HTML Element
```html
<img id="mobile-logo-img" 
     src="/images/hercules-logo-mobile-2x.webp" 
     alt="Hercules Merchandise" 
     width="282" 
     height="126" 
     data-hq-src="/images/hercules-logo-mobile-2x.webp" 
     data-astro-cid-3ef6ksr2="">
```

### Recommended Fix

#### Option 1: Simple Resize + Compress
```bash
# Create optimized version at exact display size
magick images/hercules-logo-mobile-2x.webp \
  -resize 245x109 \
  -quality 85 \
  images/hercules-logo-mobile-optimized-v2.webp

# Expected result: ~4-5 KB
```

#### Option 2: Responsive Images (Better)
```bash
# 1x version (245x109)
magick images/hercules-logo-mobile-2x.webp \
  -resize 245x109 \
  -quality 85 \
  images/hercules-logo-mobile-1x.webp

# 2x version (490x218)
magick images/hercules-logo-mobile-2x.webp \
  -resize 490x218 \
  -quality 80 \
  images/hercules-logo-mobile-2x-optimized.webp

# Update HTML
<img srcset="/images/hercules-logo-mobile-1x.webp 1x,
             /images/hercules-logo-mobile-2x-optimized.webp 2x"
     src="/images/hercules-logo-mobile-1x.webp"
     alt="Hercules Merchandise"
     width="245"
     height="109">
```

**Expected Savings:** 23.6 KB → final size ~8-10 KB (both versions combined)

---

## Image 2: hercules-logo-mobile-optimized.webp

### Current State
- **URL:** `https://hercules-merchandise.de/images/hercules-logo-mobile-optimized.webp`
- **File Size:** 13.5 KB (13,846 bytes)
- **Total Potential Savings:** 9.2 KB (9,377 bytes) - 67.8% reduction

### Issues Detected

#### Issue 2A: Poor Compression
- **Problem:** WebP compression factor too low
- **Wasted Bytes:** 7,924 bytes (7.9 KB)
- **PageSpeed Message:** "Increasing the image compression factor could improve this image's download size."

### HTML Element
```html
<img id="mobile-logo-img" 
     src="/images/hercules-logo-mobile-2x.webp" 
     alt="Hercules Merchandise" 
     width="282" 
     height="126" 
     data-hq-src="/images/hercules-logo-mobile-2x.webp" 
     data-astro-cid-3ef6ksr2="">
```

**Note:** This appears to be an alternative/fallback version referenced by the same element.

### Recommended Fix

```bash
# Increase compression
magick images/hercules-logo-mobile-optimized.webp \
  -quality 80 \
  images/hercules-logo-mobile-optimized-compressed.webp

# Expected result: ~4-5 KB
```

**Expected Savings:** 9.2 KB → final size ~4-5 KB

---

## Image 3: hercules-logo-small.webp (LOW RESOLUTION)

### Current State
- **URL:** `https://hercules-merchandise.de/images/hercules-logo-small.webp`
- **Actual Dimensions:** 87 x 39 pixels (3,393 total pixels)
- **Expected Dimensions:** 131 x 59 pixels (7,729 total pixels)
- **Display Dimensions:** 87 x 39 CSS pixels

### Issues Detected

#### Issue 3A: Low Resolution for HiDPI
- **Problem:** Image natural dimensions too small for high-DPI displays
- **Current:** 87x39 pixels (1x density)
- **Expected:** 174x78 pixels minimum (2x density)
- **PageSpeed Message:** "Image natural dimensions should be proportional to the display size and the pixel ratio to maximize image clarity."

**Result:** Blurry logo on Retina/high-DPI screens (iPhone, MacBook, etc.)

### HTML Element
```html
<img width="50" 
     height="22" 
     alt="Hercules Merchandise DE" 
     src="/images/hercules-logo-small.webp">
```

**Note:** CSS sizes it to 87x39, but needs 2x version for sharp rendering on high-DPI displays.

### Recommended Fix

```bash
# Find source of small logo (likely a larger version exists)
# Create 2x version
magick images/hercules-logo-original.webp \
  -resize 174x78 \
  -quality 85 \
  images/hercules-logo-small-2x.webp

# Update HTML
<img srcset="/images/hercules-logo-small.webp 1x,
             /images/hercules-logo-small-2x.webp 2x"
     src="/images/hercules-logo-small.webp"
     alt="Hercules Merchandise DE"
     width="50"
     height="22">
```

**Expected Impact:** Sharp logo on all devices (no size savings, but quality improvement)

---

## Implementation Checklist

### 1. Backup Original Images
```bash
cd /home/kamindu/hercules-headless-live/public/images
cp hercules-logo-mobile-2x.webp hercules-logo-mobile-2x.webp.backup
cp hercules-logo-mobile-optimized.webp hercules-logo-mobile-optimized.webp.backup
cp hercules-logo-small.webp hercules-logo-small.webp.backup
```

### 2. Create Optimized Versions
```bash
# Mobile logo 1x
magick hercules-logo-mobile-2x.webp \
  -resize 245x109 \
  -quality 85 \
  hercules-logo-mobile-1x.webp

# Mobile logo 2x (optimized)
magick hercules-logo-mobile-2x.webp \
  -quality 80 \
  hercules-logo-mobile-2x-new.webp

# Compress mobile-optimized
magick hercules-logo-mobile-optimized.webp \
  -quality 80 \
  hercules-logo-mobile-optimized-new.webp

# Small logo 2x
magick hercules-logo-mobile-2x.webp \
  -resize 174x78 \
  -quality 85 \
  hercules-logo-small-2x.webp
```

### 3. Verify File Sizes
```bash
ls -lh hercules-logo-*.webp
```

**Expected sizes:**
- hercules-logo-mobile-1x.webp: ~4-5 KB
- hercules-logo-mobile-2x-new.webp: ~8-10 KB
- hercules-logo-mobile-optimized-new.webp: ~4-5 KB
- hercules-logo-small-2x.webp: ~2-3 KB

### 4. Update HTML/Components

**Find files using these images:**
```bash
cd /home/kamindu/hercules-headless-live
grep -r "hercules-logo-mobile" src/
grep -r "hercules-logo-small" src/
```

**Update image references to use srcset**

### 5. Test Locally
```bash
npm run dev
# Check that logos render correctly
# Verify file sizes in Network tab
```

### 6. Deploy
```bash
git add public/images/
git commit -m "Optimize logo images for PageSpeed (33KB savings)"
git push origin main
```

### 7. Re-test with PageSpeed
```bash
# Wait 5 minutes for deployment
curl "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https%3A%2F%2Fhercules-merchandise.de%2F&key=AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4&category=performance&strategy=mobile"
```

**Expected improvement:** 88 → 92+ performance score

---

## Additional Context

### Why These Issues Exist

1. **Oversized images:** Likely created at 2x resolution without implementing responsive images
2. **Poor compression:** Default WebP quality settings (usually 90-95) instead of optimized (80-85)
3. **No srcset:** HTML doesn't differentiate between 1x and 2x displays

### Why This Matters

1. **Performance:** 33 KB = 1-2 extra RTTs on mobile, delays LCP
2. **Speed Index:** Smaller images = faster visual progress = better Speed Index
3. **User Experience:** Slow page = bounce rate increase
4. **SEO:** Google uses Core Web Vitals as ranking signal

### Long-term Solution

1. **Build-time optimization:** Integrate image optimization into Astro build
2. **Automated srcset:** Generate 1x/2x versions automatically
3. **Modern formats:** Already using WebP (good!)
4. **CDN:** Consider Cloudflare Images for automatic optimization

---

## Files Generated

- `/core-web-vitals/homepage-mobile-audit.md` - Full audit report
- `/core-web-vitals/IMAGE-ISSUES-DETAILED.md` - This file
- `/core-web-vitals/QUICK-REFERENCE.md` - Quick action guide
- `/core-web-vitals/urls-inventory.md` - URL tracking
- `/core-web-vitals/raw-api-response.json` - Raw API data
- `/core-web-vitals/homepage-summary.json` - Structured summary

---

**Status:** Issues identified, ready for optimization
**Next:** Implement fixes and re-test
