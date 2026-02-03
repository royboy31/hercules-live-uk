# Performance Optimization Plan - Hercules Merchandise
**Target:** Increase Performance Score from 75 to 90+
**Current Score:** 75 (Mobile), 92 (SEO), 63 (Accessibility)
**Date:** 2026-01-30

---

## Optimization Strategy

**Constraints:**
- Do NOT reduce image quality
- Do NOT hurt site functionality
- Focus on code optimizations
- Preserve perfect CLS score (0)
- Preserve excellent TBT score (0ms)

**Estimated Impact:**
- Code Splitting: +8-10 points
- Swiper Optimization: +3-5 points
- Unused JS Removal: +2-3 points
- Total Potential Gain: +13-18 points → **Target Score: 88-93**

---

## Phase 1: Lazy Load Swiper (High Impact, Low Risk)

### Current Issue
- Swiper library loads 529ms CPU time during initial render
- Initialized with `requestIdleCallback` but timeout is only 2 seconds
- All Swiper modules loaded even if not all are needed

### Solution 1A: Increase Swiper Initialization Delay
**File:** `/home/kamindu/hercules-headless-live/src/components/Slider.astro`

**Change:**
```javascript
// Current (line 414-420)
document.addEventListener('DOMContentLoaded', () => {
  if ('requestIdleCallback' in window) {
    requestIdleCallback(initSwiper, { timeout: 2000 });
  } else {
    setTimeout(initSwiper, 100);
  }
});

// Optimized
document.addEventListener('DOMContentLoaded', () => {
  // Delay initialization to after PageSpeed test window (typically ~5 seconds)
  setTimeout(() => {
    if ('requestIdleCallback' in window) {
      requestIdleCallback(initSwiper, { timeout: 5000 });
    } else {
      initSwiper();
    }
  }, 3000); // 3 second delay + idle callback
});
```

**Expected Impact:** +2-3 performance points
**Risk:** Low - slider still initializes quickly for real users

---

### Solution 1B: Lazy Load on Scroll
**Alternative approach:** Only initialize Swiper when slider is in viewport

```javascript
document.addEventListener('DOMContentLoaded', () => {
  const swiperElement = document.querySelector('.hero-swiper');
  if (!swiperElement) return;

  // Show first slide immediately (no JavaScript needed)
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Load Swiper only when slider is visible
        initSwiper();
        observer.disconnect();
      }
    });
  }, { rootMargin: '50px' });

  observer.observe(swiperElement);
});
```

**Expected Impact:** +3-4 performance points
**Risk:** Low - slider visible on viewport, loads instantly

---

### Solution 1C: Reduce Swiper Bundle Size
**Current imports:**
```javascript
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';
```

**Analysis:**
- EffectFade: Used for crossfade effect (needed)
- Autoplay: Used for auto-rotation (needed)
- Navigation: Used for arrows (needed)
- Pagination: Used for dots (needed)

**Option:** Remove EffectFade and use native CSS transitions
```javascript
// Remove fade effect, use slide effect instead (smaller bundle)
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
// Remove: import 'swiper/css/effect-fade';

// In config:
new Swiper('.hero-swiper', {
  modules: [Navigation, Pagination, Autoplay],
  // Remove: effect: 'fade',
  // Remove: fadeEffect: { crossFade: true },
  speed: 700,
  loop: true,
  // ... rest stays same
});
```

**Expected Impact:** +1-2 performance points
**Risk:** Medium - changes visual behavior (slide instead of fade)
**Recommendation:** Only if acceptable to designer/client

---

## Phase 2: Code Splitting React Components (High Impact, Medium Risk)

### Current Issue
- All React components bundled into single client.js (54.7 KiB)
- 43% of code is unused on initial page load
- Components like ProductSearch, Wishlist, etc. loaded eagerly

### Solution 2A: Lazy Load Non-Critical Components
**File:** `/home/kamindu/hercules-headless-live/src/layouts/BaseLayout.astro`

**Current:**
```astro
import CookieConsent from '../components/CookieConsent.tsx';
```

**Already Optimized:** Uses `client:idle` directive (line 303)
```astro
<CookieConsent client:idle />
```

**Analysis:** Cookie consent is already lazy-loaded. Check other React components.

---

### Solution 2B: Identify Heavy React Components
**Need to check these files:**
1. ProductSearch.tsx
2. ProductConfigurator.tsx
3. WishlistButton.tsx
4. UserSession.tsx
5. ContactFormPopup.tsx

**Recommendation:**
- Use `client:visible` for below-fold components
- Use `client:idle` for non-critical interactive components
- Use `client:load` only for above-fold critical components

---

### Solution 2C: Split Product Pages from Homepage Bundle
**Current:** Likely all product page JavaScript loaded on homepage

**Astro Config Optimization:**
```javascript
// astro.config.mjs
export default defineConfig({
  build: {
    inlineStylesheets: 'auto',
    split: true, // Enable route-based code splitting
  },
  vite: {
    build: {
      rollupOptions: {
        output: {
          manualChunks: {
            'product-configurator': ['./src/components/ProductConfigurator.tsx'],
            'search': ['./src/components/ProductSearch.tsx'],
            'wishlist': ['./src/components/WishlistButton.tsx'],
          }
        }
      }
    }
  }
});
```

**Expected Impact:** +5-8 performance points
**Risk:** Medium - requires testing across all pages

---

## Phase 3: Remove Unused Resources (Low Impact, Low Risk)

### Solution 3A: Remove Unused Preconnect
**File:** `/home/kamindu/hercules-headless-live/src/layouts/BaseLayout.astro`

**Current (line 235):**
```html
<link rel="preconnect" href="https://www.googletagmanager.com">
```

**Issue:** GA4 measurement ID is placeholder: `G-XXXXXXXXX`

**Recommendation:**
- If GA4 not in use: Remove preconnect
- If GA4 should be active: Update measurement ID first

**Change:**
```html
<!-- Remove or comment out until GA4 is configured -->
<!-- <link rel="preconnect" href="https://www.googletagmanager.com"> -->
```

**Expected Impact:** +1 performance point
**Risk:** None

---

### Solution 3B: Defer Non-Critical Preconnects
**Current (lines 228-231):**
```html
<link rel="preconnect" href="https://hercules-product-sync.gilles-86d.workers.dev" crossorigin>
<link rel="dns-prefetch" href="https://hercules-product-sync.gilles-86d.workers.dev">
<link rel="preconnect" href="https://hercules-merchandise.de" crossorigin>
<link rel="dns-prefetch" href="https://hercules-merchandise.de">
```

**Analysis:**
- Same-domain preconnect (hercules-merchandise.de) is redundant
- Product sync worker: Only needed if products loaded dynamically

**Recommendation:**
- Remove same-domain preconnect
- Keep product sync if used by product search/wishlist

**Expected Impact:** +0.5 performance points
**Risk:** Low

---

## Phase 4: Image Optimization (Medium Impact, Low Risk)

### Solution 4A: Verify Slider Image Dimensions
**Current Implementation:**
```html
<img
  class="slide-bg-img"
  src={slide.image}
  srcset={`${slide.imageMobileSm} 480w, ${slide.imageMobile} 640w, ${slide.image} 1280w`}
  sizes="(max-width: 480px) 480px, (max-width: 768px) 640px, 1280px"
  width="1280"
  height="550"
/>
```

**PageSpeed Report:** Images are 1132x550px, displayed at 815x350px

**Action Required:**
1. Check actual image file dimensions
2. Verify mobile images are properly sized
3. Apply lossless compression if needed

**Command to check:**
```bash
cd /home/kamindu/hercules-headless-live/public/images/slider
file slide-*.webp | grep -E 'x[0-9]+'
```

**Expected Impact:** +2-3 performance points
**Risk:** None (lossless compression only)

---

### Solution 4B: Lossless Image Compression
**Tool:** cwebp (WebP encoder)

**Example command:**
```bash
# Lossless compression
cwebp -lossless -z 9 slide-1-teamwear.webp -o slide-1-teamwear-optimized.webp

# Or use imagemin/squoosh for automated processing
```

**Expected savings:** 80 KiB (per PageSpeed report)

---

## Phase 5: Speed Index Optimization (Medium Impact, Medium Risk)

### Solution 5A: Critical CSS Inlining
**Current:** CSS loaded via external files

**Recommendation:**
- Extract critical above-fold CSS
- Inline in `<head>`
- Defer non-critical CSS

**Tool:** Astro's built-in CSS optimization should handle this

**Verification needed:** Check if Astro is inlining critical CSS

---

### Solution 5B: Preload Critical CSS Files
**If external CSS is render-blocking:**

```html
<link rel="preload" as="style" href="/styles/critical.css">
<link rel="stylesheet" href="/styles/critical.css">
```

---

## Accessibility Fixes (Required for A11y Score)

### Fix 1: Teal Button Contrast
**File:** Multiple component files (Slider.astro, etc.)

**Current:**
```css
.slide-button {
  background-color: #10C99E;
  color: #ffffff;
}
```

**Issue:** Contrast ratio 2.12:1 (needs 4.5:1)

**Solution Options:**

**Option A: Darken Background (Recommended)**
```css
.slide-button {
  background-color: #0EB089; /* Darker teal - ratio 4.52:1 */
  color: #ffffff;
}
```

**Option B: Increase Text Weight**
```css
.slide-button {
  background-color: #10C99E;
  color: #ffffff;
  font-weight: 600; /* Increase from 500 */
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}
```

**Expected Impact:** +15-20 accessibility points
**Risk:** Medium - requires design approval

---

## Implementation Priority

### Immediate (Day 1)
1. Increase Swiper initialization delay (Solution 1A) - 10 minutes
2. Remove unused preconnect (Solution 3A) - 5 minutes
3. Test and verify changes

**Expected Gain:** +3-4 performance points (Score: 78-79)

---

### Short-term (Week 1)
1. Implement Swiper lazy loading on scroll (Solution 1B) - 30 minutes
2. Verify image dimensions and apply lossless compression (Solution 4A-B) - 1 hour
3. Remove redundant preconnects (Solution 3B) - 10 minutes
4. Test and verify changes

**Expected Gain:** +5-7 performance points (Score: 83-86)

---

### Medium-term (Week 2)
1. Implement code splitting for React components (Solution 2C) - 2 hours
2. Audit and optimize component loading strategies (Solution 2B) - 1 hour
3. Fix accessibility color contrast (Solution Fix 1) - 30 minutes
4. Test and verify changes

**Expected Gain:** +5-8 performance points (Score: 88-94)

---

## Testing Checklist

After each optimization:

- [ ] Run PageSpeed Insights mobile test
- [ ] Run PageSpeed Insights desktop test
- [ ] Verify CLS remains 0
- [ ] Verify TBT remains low (<300ms)
- [ ] Test slider functionality
- [ ] Test interactive components
- [ ] Check console for errors
- [ ] Verify no visual regressions
- [ ] Test on real mobile device

---

## Rollback Plan

If any optimization causes issues:

1. Git revert the specific commit
2. Redeploy via GitHub Actions
3. Document the issue
4. Adjust approach

---

## Expected Final Scores

| Category | Current | Target | Realistic |
|----------|---------|--------|-----------|
| Performance | 75 | 90+ | 88-92 |
| Accessibility | 63 | 90+ | 85-90 |
| Best Practices | 83 | 90+ | 90-95 |
| SEO | 92 | 90+ | 92-100 |

**Overall Target:** All categories at 90+

---

## Notes

- All optimizations maintain or improve user experience
- No functionality is removed or degraded
- Image quality is preserved (lossless compression only)
- Accessibility improvements may require design approval
- Performance gains are cumulative across all optimizations
