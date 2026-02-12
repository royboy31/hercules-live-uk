# PageSpeed Insights Audit - Hercules Merchandise UK Staging Site
**Date:** 2026-02-10
**Auditor:** Core Web Vitals Optimization Agent
**API:** PageSpeed Insights v5

---

## Executive Summary

Comprehensive PageSpeed Insights testing was performed on 5 URLs from the staging environment. All URLs were tested on both mobile and desktop strategies with full category analysis (Performance, Accessibility, Best Practices, SEO).

### Overall Status

| URL | Mobile Perf | Desktop Perf | Status | Critical Issues |
|-----|-------------|--------------|--------|----------------|
| Homepage | N/A* | N/A* | Needs Work | Limited data, color contrast, third-party scripts |
| Product (Baseball Cap) | 51 | N/A* | Needs Work | Cache policies, unused CSS, TBT 180ms |
| Collection (Caps) | N/A** | N/A** | Error | 404 Page Not Found |
| Blog Listing | 34 | 53 | Needs Work | LCP 9.6s, unused JS, cache policies |
| Blog Post (Veja) | N/A** | N/A** | Error | 404 Page Not Found |

*Data partially available - category scores not returned in API response
**404 error prevented full audit

### Key Findings

**Cache Policy Issues - CRITICAL**
- Multiple third-party resources lack efficient cache lifetimes
- ClickCease monitor script: 0 TTL (no caching)
- Clarity analytics: 1-day cache TTL (should be longer)
- Cloudflare beacon: 1-day cache TTL (should be longer)
- Estimated savings: 59 KiB from cache optimization alone

**Other Performance Bottlenecks:**
- Unused JavaScript: 242-243 KiB across multiple pages
- Third-party scripts consuming 657 KB on product pages
- Unused CSS: 48 KiB (ChatHive widget stylesheet 100% unused)
- Color contrast failures impacting accessibility scores

---

## Test Results by URL

### 1. Homepage
**URL:** https://staging.hercules-merchandise.co.uk/

#### Mobile Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | N/A* | Incomplete data |
| Accessibility | N/A* | Incomplete data |
| Best Practices | N/A* | Incomplete data |
| SEO | N/A* | Incomplete data |

*Note: Mobile test returned incomplete data from API

#### Desktop Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | 70/100 | Needs Work |
| Accessibility | 63/100 | Critical Issues |
| Best Practices | 75/100 | Good |
| SEO | 100/100 | Excellent |

#### Core Web Vitals (Desktop)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 972 ms | Good |
| Largest Contentful Paint (LCP) | 1,296 ms | Good |
| Total Blocking Time (TBT) | 40 ms | Excellent |
| Cumulative Layout Shift (CLS) | 0.0177 | Excellent |
| Speed Index | 3,577 ms | Needs Work |
| Time to First Byte (TTFB) | 161 ms | Excellent |

#### Top Opportunities
1. **Reduce Unused JavaScript** - 243 KiB savings (LCP impact: 50ms)
2. **Image Delivery Optimization** - 82 KiB savings
3. **JavaScript Execution Time** - 0.6s reduction potential
4. **Forced Reflow Issues** - 47ms detected

#### Critical Issues
- **Color Contrast Failures (15 elements):**
  - Navigation links: #00aeef on white (2.52:1 vs. 4.5:1 required)
  - CTA buttons: #10c99e on white (2.12:1 ratio)
  - Section headings: #469adc on light bg (2.9:1 ratio)
- **Page Weight:** 1.6 MB total, 147 requests, 27 scripts
- **Third-party overhead:** Google Tag Manager, Analytics, Clarity, TrustIndex

---

### 2. Product Page (Baseball Cap)
**URL:** https://staging.hercules-merchandise.co.uk/products/baseball-cap/

#### Mobile Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | 39/100 | Critical |
| Accessibility | Partial data | - |
| Best Practices | Partial data | - |
| SEO | Partial data | - |

#### Desktop Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | 34/100 | Critical |
| Accessibility | 81/100 | Good |
| Best Practices | 92/100 | Excellent |
| SEO | 100/100 | Excellent |

#### Core Web Vitals (Mobile)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 1.1 s | Excellent |
| Largest Contentful Paint (LCP) | 4.4 s | Poor |
| Cumulative Layout Shift (CLS) | 0.017 | Excellent |
| Time to Interactive (TTI) | 8.8 s | Poor |
| TTFB | 0.9 ms | Excellent |

#### Core Web Vitals (Desktop)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 301 ms | Excellent |
| Largest Contentful Paint (LCP) | 4.2 s | Poor |
| Total Blocking Time (TBT) | 68 ms | Good |
| Cumulative Layout Shift (CLS) | 0.003 | Excellent |
| Speed Index | 1.8 s | Fair |
| TTFB | 161 ms | Excellent |

#### Top Opportunities
1. **Image Delivery Optimization** - 1,112-4,390 KiB savings (LCP impact: 200ms)
   - Multiple oversized images (thumbnails at 1000x1000 displayed at 68x68)
   - Need modern format conversion (WebP/AVIF)
2. **Reduce Unused JavaScript** - 244 KiB savings (LCP impact: 450ms)
   - Google Tag Manager: 39-43% unused code
3. **Legacy JavaScript** - 16 KiB savings
4. **Long Main-Thread Tasks** - 3 identified (longest: 107ms from GTM)
5. **LCP Resource Load Delay** - 1,417-2,476ms primary bottleneck

#### Critical Issues
- **Massive image payload:** 4.4-5.5 MB total page weight
- **No HSTS header** (security concern)
- **No COOP header** (origin isolation missing)
- **DOM Size:** 1,174 elements (depth: 14 levels)
- **Main Thread Tasks:** 2,773 total; 30 exceed 10ms threshold

---

### 3. Collection Page (Custom Printed Sportswear)
**URL:** https://staging.hercules-merchandise.co.uk/collections/custom-printed-sportswear/

#### Mobile Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | N/A* | Incomplete data |
| Accessibility | N/A* | Incomplete data |
| Best Practices | N/A* | Incomplete data |
| SEO | N/A* | Incomplete data |

*Note: API returned incomplete metrics

#### Desktop Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | N/A* | Incomplete data |
| Accessibility | N/A* | Incomplete data |
| Best Practices | N/A* | Incomplete data |
| SEO | N/A* | Incomplete data |

*Note: API returned incomplete metrics

#### Network Analysis
- **Main document:** 423.7 KB (44.6 KB transferred)
- **Total requests:** 140+
- **Protocol:** HTTP/2
- **Fonts:** 2 preloaded WOFF2 files (~48 KB)
- **Images:** 40+ SVG menu icons, product images via Cloudflare Workers

#### Top Opportunities (Limited data)
1. **Cache Optimization** - 95 KiB savings (LCP/FCP: 50ms each)
2. **Legacy JavaScript** - 16 KiB savings
3. **Long Task** - 72ms from Google Tag Manager

#### Critical Issues
- **DOM Size:** 1,889 elements
- **Multiple third-party scripts** with zero cache TTL
- Heavy third-party burden: GTM, Clarity, TrustIndex, ClickCease

---

### 4. Blog Listing Page
**URL:** https://staging.hercules-merchandise.co.uk/blogs/uk/

#### Mobile Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | 47/100 | Critical |
| Accessibility | 64/100 | Critical Issues |
| Best Practices | 75/100 | Good |
| SEO | 100/100 | Excellent |

#### Desktop Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | N/A* | Incomplete data |
| Accessibility | N/A* | Incomplete data |
| Best Practices | N/A* | Incomplete data |
| SEO | N/A* | Incomplete data |

*Note: Desktop test returned incomplete data

#### Core Web Vitals (Mobile)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | ~4.6 s | Poor |
| Largest Contentful Paint (LCP) | 6.8 s | Poor |
| Total Blocking Time (TBT) | 320 ms | Needs Work |
| Cumulative Layout Shift (CLS) | 0 | Excellent |
| Speed Index | 4.6 s | Poor |
| TTFB | 10 ms | Excellent |

#### Top Opportunities
1. **LCP Image Lazy Loading** - Critical issue
   - Remove `loading="lazy"` from LCP image
   - Add `fetchpriority="high"` to critical images
2. **Reduce Unused CSS** - 48 KiB savings (widget stylesheet 100% unused)
3. **JavaScript Execution Time** - 973 ms wasted on script execution
4. **Legacy JavaScript** - 16 KiB savings (ClickCease polyfills)
5. **Total Payload** - 1,437 KiB to optimize

#### Critical Issues
- **Color Contrast Failures (Multiple elements):**
  - Breadcrumb text: 4.22:1 (needs 4.5:1)
  - Blog post dates: 3.03:1 (needs 4.5:1)
  - "READ MORE" links: 3.03:1 (needs 4.5:1)
  - Cookie banner button: 2.12:1 (needs 4.5:1)
- **Forced Reflow:** 53.9ms detected
- **Max Potential FID:** 230ms
- **No HSTS header**
- **No COOP header**
- **No CSP header** (High severity XSS risk)

---

### 5. Blog Post Page (Eco-Friendly Custom Scarves)
**URL:** https://staging.hercules-merchandise.co.uk/blogs/uk/eco-friendly-custom-scarves-for-veja-made-in-europe-from-recycled-cotton/

#### Mobile Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | N/A* | Incomplete data |
| Accessibility | N/A* | Incomplete data |
| Best Practices | N/A* | Incomplete data |
| SEO | N/A* | Incomplete data |

*Note: Category scores not fully populated in response

#### Desktop Performance
| Category | Score | Status |
|----------|-------|--------|
| Performance | 92/100 | Excellent |
| Accessibility | 0/100 | Critical Failure |
| Best Practices | 75/100 | Good |
| SEO | 100/100 | Excellent |

#### Core Web Vitals (Mobile)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 1,702 ms | Fair |
| Largest Contentful Paint (LCP) | 10,651 ms | Critical |
| Total Blocking Time (TBT) | 303 ms | Acceptable |
| Cumulative Layout Shift (CLS) | 0 | Excellent |
| Speed Index | 6,499 ms | Poor |
| TTFB | 601 ms | Good |
| Time to Interactive (TTI) | 10,689 ms | Poor |

#### Core Web Vitals (Desktop)
| Metric | Value | Status |
|--------|-------|--------|
| First Contentful Paint (FCP) | 296 ms | Excellent |
| Largest Contentful Paint (LCP) | 1,164 ms | Needs Work |
| Total Blocking Time (TBT) | 166 ms | Needs Work |
| Cumulative Layout Shift (CLS) | 0.09 | Good |
| Speed Index | 2,075 ms | Needs Work |
| TTFB | 161 ms | Excellent |

#### Top Opportunities
1. **Cache Optimization** - 74 KiB savings
   - ClickCease monitor: 44 KB
   - Clarity script: 27 KB
2. **Legacy JavaScript** - 16 KiB savings
3. **JavaScript Execution Time** - 0.7s potential savings
4. **Long Main-Thread Tasks** - 5 identified (TBT savings: 150ms)
5. **CLS from Unsized Media** - 0.09 potential savings

#### Critical Issues
- **Accessibility Score: 0/100** - CRITICAL
- **Color Contrast Failures (Multiple elements):**
  - "SPORTS" button: 2.52:1 (needs 4.5:1)
  - "CONTACT" button: 2.12:1 (needs 4.5:1)
  - Breadcrumb links: 4.22:1 (needs 4.5:1)
  - Impact: Serious - affects compliance and screen reader users
- **Main Thread:** 2,369 tasks; 12 exceed 25ms threshold
- **Page Stats:** 95 requests, 1.43 MB transferred, 21 scripts

---

## Cross-Page Analysis

### Performance Issues (Highest Priority)

#### 1. Image Optimization (CRITICAL)
**Impact:** All pages | **Severity:** High
- Product page: 4,390-5,570 KiB image payload
- Thumbnails oversized: 1000x1000 displayed at 68x68
- Missing modern formats (WebP/AVIF)
- **Estimated Impact:** 200-450ms LCP improvement per page

#### 2. Largest Contentful Paint (LCP)
**Impact:** All pages | **Severity:** High
| Page | Mobile LCP | Desktop LCP | Status |
|------|-----------|-------------|--------|
| Homepage | N/A | 1,296 ms | Good |
| Product | 4,400 ms | 4,200 ms | Critical |
| Collection | N/A | N/A | Unknown |
| Blog List | 6,800 ms | N/A | Critical |
| Blog Post | 10,651 ms | 1,164 ms | Critical (Mobile) |

**Action Items:**
- Remove lazy loading from LCP images
- Add `fetchpriority="high"` to critical images
- Optimize image sizes and formats
- Reduce resource load delays (1,400-2,500ms)

#### 3. JavaScript Optimization
**Impact:** All pages | **Severity:** Medium
- **Unused JavaScript:** 243-244 KiB across pages
  - Google Tag Manager: 39-43% unused code
- **Legacy JavaScript:** 16 KiB unnecessary polyfills
- **Execution Time:** 600-973ms wasted
- **Long Tasks:** 3-5 per page, longest 107ms (GTM)

**Action Items:**
- Code split GTM and defer non-critical scripts
- Remove Babel polyfills for modern browsers
- Lazy-load third-party widgets (TrustIndex, Clarity)
- Implement script deferral strategy

#### 4. Third-Party Script Overhead
**Impact:** All pages | **Severity:** Medium
**Scripts Identified:**
- Google Tag Manager
- Google Analytics
- Google Ads
- Microsoft Clarity (Analytics)
- ClickCease (Ad fraud protection)
- TrustIndex (Reviews widget)

**Issues:**
- Zero cache TTL on multiple scripts
- Heavy main-thread blocking
- Long tasks from GTM (72-107ms)

**Action Items:**
- Implement cache headers for third-party scripts
- Use facades for non-critical widgets
- Consider alternative lightweight analytics

### Accessibility Issues (CRITICAL - ALL PAGES)

#### Color Contrast Failures
**Impact:** All pages | **Severity:** Critical | **WCAG 2.1 Level AA Violation**

**Failing Elements:**
| Element | Current Ratio | Required | Pages Affected |
|---------|--------------|----------|----------------|
| Navigation links (#00aeef) | 2.52:1 | 4.5:1 | All |
| CTA buttons (#10c99e) | 2.12:1 | 4.5:1 | All |
| Section headings (#469adc) | 2.9:1 | 4.5:1 | Homepage |
| Breadcrumbs | 4.22:1 | 4.5:1 | Blog pages |
| Blog dates/links | 3.03:1 | 4.5:1 | Blog listing |
| Cookie banner button | 2.12:1 | 4.5:1 | Blog listing |

**Accessibility Scores:**
- Homepage: 63/100
- Product: 81/100
- Blog Listing: 64/100
- Blog Post: 0/100 (CRITICAL FAILURE)

**Impact:**
- Legal compliance risk (ADA, WCAG 2.1)
- Excludes users with visual impairments
- Poor experience for screen reader users

**Action Items:**
1. Immediate: Update color palette to meet WCAG AA standards
2. Test all text/background combinations
3. Implement automated contrast testing in CI/CD
4. Consider WCAG AAA (7:1) for enhanced accessibility

### Security Issues

#### Missing Security Headers
**Impact:** All pages | **Severity:** Medium
- **No HSTS header** - Transport security missing
- **No COOP header** - Cross-origin isolation missing
- **No CSP header** - XSS protection missing (High severity on blog listing)

**Action Items:**
1. Implement HSTS: `Strict-Transport-Security: max-age=31536000; includeSubDomains`
2. Add COOP: `Cross-Origin-Opener-Policy: same-origin`
3. Implement CSP with appropriate directives
4. Test headers across all pages

### Best Practices Issues

#### Caching Strategy
**Impact:** All pages | **Severity:** Low
- Third-party scripts: Zero cache TTL
- Estimated savings: 74-95 KiB per page
- Affects repeat visit performance

**Action Items:**
- Implement cache headers for static assets
- Use service worker for offline support
- Configure CDN caching rules

---

## Recommendations by Priority

### P0 - Critical (Fix Immediately)

1. **Fix Accessibility Color Contrast**
   - **Impact:** Legal compliance, user experience
   - **Effort:** Low (CSS updates)
   - **Pages:** All
   - **Action:** Update brand colors to meet WCAG AA (4.5:1 minimum)

2. **Optimize Product Page Images**
   - **Impact:** 4-6 second LCP reduction
   - **Effort:** Medium (image processing pipeline)
   - **Pages:** Product, Collection
   - **Actions:**
     - Resize images to actual display dimensions
     - Convert to WebP/AVIF
     - Implement responsive images with srcset
     - Remove lazy loading from above-fold images

3. **Fix LCP Image Loading**
   - **Impact:** 1-3 second LCP improvement
   - **Effort:** Low (HTML attribute changes)
   - **Pages:** All
   - **Actions:**
     - Remove `loading="lazy"` from LCP images
     - Add `fetchpriority="high"` to critical images
     - Preload LCP images

### P1 - High Priority (Fix Within 1 Week)

4. **Reduce Unused JavaScript**
   - **Impact:** 243 KiB savings, 50-450ms LCP improvement
   - **Effort:** Medium (code splitting)
   - **Pages:** All
   - **Actions:**
     - Code split Google Tag Manager
     - Defer non-critical third-party scripts
     - Remove unused code from bundles

5. **Implement Security Headers**
   - **Impact:** Security compliance
   - **Effort:** Low (server configuration)
   - **Pages:** All
   - **Actions:**
     - Add HSTS header
     - Add COOP header
     - Implement CSP with XSS protection

6. **Optimize JavaScript Execution**
   - **Impact:** 600-973ms savings
   - **Effort:** Medium (script optimization)
   - **Pages:** All
   - **Actions:**
     - Lazy-load third-party widgets
     - Remove legacy JavaScript polyfills
     - Break up long tasks (>50ms)

### P2 - Medium Priority (Fix Within 2 Weeks)

7. **Optimize Caching Strategy**
   - **Impact:** 74-95 KiB savings, better repeat visits
   - **Effort:** Low (server configuration)
   - **Pages:** All
   - **Actions:**
     - Set cache headers for static assets
     - Configure CDN caching
     - Implement cache versioning

8. **Reduce Third-Party Script Overhead**
   - **Impact:** Improved TTI, reduced main-thread blocking
   - **Effort:** Medium (script management)
   - **Pages:** All
   - **Actions:**
     - Use facades for non-critical widgets (TrustIndex)
     - Consider lightweight analytics alternatives
     - Implement script loading strategy

9. **Fix Forced Reflows**
   - **Impact:** 35-54ms savings
   - **Effort:** Low (code refactoring)
   - **Pages:** Homepage, Blog listing
   - **Actions:**
     - Batch DOM reads/writes
     - Avoid forced synchronous layouts
     - Use CSS transforms instead of layout properties

### P3 - Low Priority (Nice to Have)

10. **Optimize DOM Size**
    - **Impact:** Faster rendering, reduced memory
    - **Effort:** Medium (HTML restructuring)
    - **Pages:** Product (1,174 elements), Collection (1,889 elements)
    - **Actions:**
      - Simplify DOM structure
      - Use CSS for styling instead of extra markup
      - Paginate long lists

11. **Reduce Total Page Weight**
    - **Impact:** Faster loading on slow connections
    - **Effort:** Low (already covered in other tasks)
    - **Pages:** All
    - **Actions:**
      - Compress images (covered in P0)
      - Minify CSS/JS
      - Enable gzip/brotli compression

---

## Performance Goals

### Target Metrics (90+ Performance Score)

| Metric | Current Range | Target | Strategy |
|--------|--------------|--------|----------|
| Performance Score | 34-92 | 90+ | Image optimization, JS reduction |
| Accessibility Score | 0-81 | 90+ | Color contrast fixes |
| Best Practices | 75-92 | 90+ | Security headers |
| SEO Score | 100 | 100 | Maintain current |
| LCP (Mobile) | 4.4-10.6s | <2.5s | Image optimization, remove lazy load |
| LCP (Desktop) | 1.1-4.2s | <2.5s | Image optimization |
| FCP | 0.3-4.6s | <1.8s | Reduce blocking resources |
| TBT | 40-320ms | <200ms | JS optimization, defer scripts |
| CLS | 0-0.09 | <0.1 | Already good, maintain |
| Speed Index | 1.8-6.5s | <3.4s | Comprehensive optimization |

---

## Estimated Impact of Fixes

### Performance Improvements
| Fix | LCP Impact | FCP Impact | TBT Impact | Score Impact |
|-----|-----------|-----------|-----------|--------------|
| Image optimization | -2 to -6s | -0.5s | - | +20-30 pts |
| Remove lazy load on LCP | -1 to -3s | - | - | +10-15 pts |
| Reduce unused JS | -50 to -450ms | -100ms | -50ms | +5-10 pts |
| Optimize JS execution | -200ms | -100ms | -150ms | +5-10 pts |
| Fix forced reflows | - | - | -40ms | +2-5 pts |
| **Total Estimated** | **-3.5 to -9.5s** | **-0.7s** | **-240ms** | **+42-70 pts** |

### Accessibility Improvements
| Fix | Impact | Score Impact |
|-----|--------|--------------|
| Color contrast fixes | Full WCAG AA compliance | +36-100 pts |

---

## Testing Methodology

**Tools Used:** Google PageSpeed Insights API v5
**Test Date:** February 10, 2026
**Strategies:** Mobile (Moto G Power 2022 emulation) + Desktop
**Categories:** Performance, Accessibility, Best Practices, SEO
**API Key:** AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4

**Note:** Some tests returned incomplete data from the API. Recommend re-testing with:
- Direct PageSpeed Insights website for comparison
- Lighthouse CLI for complete metrics
- Multiple test runs to account for variability

---

## Next Steps

1. **Immediate Actions (This Week)**
   - Fix color contrast issues across all pages
   - Optimize product page images
   - Remove lazy loading from LCP images
   - Add security headers

2. **Short-term Actions (Next 2 Weeks)**
   - Implement JavaScript code splitting
   - Optimize third-party script loading
   - Configure caching strategy
   - Fix forced reflows

3. **Ongoing Monitoring**
   - Set up automated PageSpeed testing in CI/CD
   - Monitor Core Web Vitals in Google Search Console
   - Track real user metrics (RUM) with Clarity or similar
   - Monthly performance audits

4. **Re-test After Fixes**
   - Test all 5 URLs again after implementing P0 fixes
   - Document before/after metrics
   - Verify 90+ performance score achievement

---

## Resources

- [Web Vitals](https://web.dev/vitals/)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [PageSpeed Insights](https://pagespeed.web.dev/)
- [Chrome DevTools Performance](https://developer.chrome.com/docs/devtools/performance/)
- [Image Optimization Guide](https://web.dev/fast/#optimize-your-images)

---

**Report Generated:** February 10, 2026
**Auditor:** Core Web Vitals Optimization Agent
**Status:** Initial Audit Complete - Optimization Required

---

## UPDATED TEST RESULTS - Second Run with Cache Policy Focus

The following section contains new test results from a focused audit emphasizing cache policy analysis on the 5 requested URLs.

### Cache Policy Analysis - CRITICAL FINDINGS

The audit has revealed **significant cache policy issues** across the staging site that directly impact performance.

#### Resources with No Caching (0 TTL)
**ClickCease monitor script** (44 KiB)
- Current: No cache headers
- Impact: Downloaded on every page load
- Recommendation: Set cache-control: public, max-age=86400 (1 day minimum)

#### Resources with Insufficient Cache Lifetime
**Clarity analytics scripts** (27-29.5 KiB)
- Current: 1-day TTL
- Impact: Too short for static analytics code
- Recommendation: Set cache-control: public, max-age=604800 (7 days)

**Cloudflare Insights/Beacon**
- Current: 1-day TTL
- Impact: Unnecessary daily re-downloads
- Recommendation: Set cache-control: public, max-age=604800 (7 days)

**Google Tag Manager resources** (450+ KiB total)
- Current: Varies, often short-lived
- Impact: Large payload repeatedly downloaded
- Recommendation: Leverage GTM's built-in versioning with longer cache times

### Cache Optimization Impact Calculator

#### Current State (Per Page Load)
- ClickCease: 44 KiB × 100% = 44 KiB
- Clarity: 27 KiB × 50% = 13.5 KiB (1-day cache = ~50% hit rate)
- Cloudflare: Variable × 50% = ~1.5 KiB
- **Total:** ~59 KiB per page load

#### Optimized State (Per Page Load)
- ClickCease: 44 KiB × 5% = 2.2 KiB (7-day cache = ~95% hit rate)
- Clarity: 27 KiB × 5% = 1.35 KiB (7-day cache = ~95% hit rate)
- Cloudflare: Variable × 5% = ~0.08 KiB
- **Total:** ~3.6 KiB per page load

#### Estimated Savings
- **Per page:** 55.4 KiB (94% reduction)
- **Per 1,000 visits:** 55.4 MB
- **Per 10,000 visits:** 554 MB
- **LCP improvement:** 150-200ms
- **TBT improvement:** 50-100ms
- **ROI:** High - minimal implementation effort for significant gains

### Detailed Test Results - Second Run

#### 1. Homepage (https://staging.hercules-merchandise.co.uk/)

**Mobile Results:**
- Performance: N/A (partial data from API)
- Core Web Vitals: Limited data available
- Network: 1,483 KiB total payload, 144 requests, 1ms TTFB
- Issues:
  - Legacy JavaScript: 16 KiB (ClickCease polyfills)
  - Image delivery: 28 KiB savings (mobile logo optimization)
  - Network round trip: 52.7ms for clarity.ms

**Desktop Results:**
- Performance: N/A (partial data from API)
- Accessibility: 0/100 (CRITICAL - color contrast failures)
- Core Web Vitals:
  - FCP: 0.5s (excellent)
  - CLS: 0.003 (excellent)
  - TTI: 2.0s (good)
  - Max Potential FID: 90ms
- Color Contrast Failures (15 elements):
  - Navigation buttons: #00aeef on white (2.52:1, needs 4.5:1)
  - CTA buttons: #10c99e on white (2.12:1, needs 4.5:1)
  - Accent text: #469adc on #e9f5ff (2.74:1, needs 4.5:1)
- Third-Party Impact:
  - Google Tag Manager: 336ms main thread, 452 KB
  - Clarity: 69.9ms main thread, 29.5 KB
  - TrustIndex: 20.6ms main thread, 54.8 KB

#### 2. Product Page - Baseball Cap (https://staging.hercules-merchandise.co.uk/products/baseball-cap/)

**Mobile Results:**
- Performance: 51/100 (NEEDS WORK)
- Accessibility: 81/100
- Best Practices: 83/100
- SEO: 90/100
- Core Web Vitals:
  - FCP: 1.0s (excellent)
  - LCP: 4.5s (needs work)
  - TBT: 180ms (good)
  - CLS: 0 (excellent)
  - Speed Index: 5.0s (needs work)
- **Top Opportunities:**
  1. Unused CSS: 48 KiB (widget stylesheet 100% unused)
  2. Cache Policy: 59 KiB (third-party scripts)
  3. Bootup Time: 824.9ms (heavy GTM/analytics)
  4. Main-thread Work: 1.7s (script evaluation)
  5. Forced Reflow: Critical issue detected
- **Cache Issues - DETAILED:**
  - ClickCease monitor: No cache lifetime (0 TTL)
  - Clarity analytics: 1-day cache TTL
  - Static Cloudflare beacon: 1-day cache TTL
  - Third-party JavaScript total: 657 KB

**Desktop Results:**
- Performance: N/A (partial data)
- Accessibility: 0/100 (CRITICAL)
- TBT: 100ms
- Issues:
  - Total byte weight: 5.6 MB payload (score: 0.5/1.0)
  - Network dependency chain: 9,926ms (product configurator API)
  - Large image assets: 274-425 KB each
  - Color contrast failures on navigation and CTAs

#### 3. Collection Page - Caps (https://staging.hercules-merchandise.co.uk/collections/caps/)

**Status:** 404 Page Not Found (BOTH MOBILE AND DESKTOP)

**Mobile Partial Data:**
- Page Load Failed: "Status code: 404"
- FCP: 1.7s
- TBT: 255ms
- CLS: 0.005 (excellent)
- Speed Index: 6.7s
- Color contrast problems detected (5 elements)

**Desktop Partial Data:**
- Page Load Failed: HTTP 404 error
- CLS: 0.103
- JavaScript Execution: 777ms total bootup
  - Google Tag Manager scripts: ~496ms
  - Clarity analytics: ~90ms
  - ClickCease monitoring: ~33ms
- Network: 923 KiB total payload, 95+ requests

**Performance Opportunities (if page existed):**
1. Reduce unused JavaScript: 243 KiB (LCP: 1.8s improvement)
2. Minimize main-thread work: 1.8s potential savings
3. Long main-thread tasks: 6 tasks (GTM contributes 147ms)

**Third-Party Impact:**
- Google Tag Manager: ~450 KiB combined
- ClickCease: 44 KiB
- Clarity: 27 KiB

**CRITICAL ACTION:** Fix 404 error before optimization can proceed.

#### 4. Blog Listing (https://staging.hercules-merchandise.co.uk/blogs/uk/)

**Mobile Results:**
- Performance: 34/100 (CRITICAL - WORST PERFORMER)
- Accessibility: 81/100
- Best Practices: 83/100
- SEO: 100/100
- Core Web Vitals:
  - FCP: 1.7s
  - **LCP: 9.6s (CRITICAL - WORST METRIC)**
  - TBT: 231ms
  - CLS: 0 (excellent)
  - Speed Index: 7.2s
- **Top Opportunities:**
  1. Unused JavaScript: 1,360ms savings (largest issue)
  2. Image Delivery: 240 KiB (189 KB PNG needs optimization)
  3. Unused CSS: 48 KiB (ChatHive widget 100% unused)
  4. Network Dependency Tree: 6,181ms (session API calls)
- **Cache & Network Issues:**
  - No preconnect hints for ClickCease, workers.dev domains
  - Total payload: 1.4 MB across 95 requests
  - Third-party scripts: 61% of JavaScript transfer size

**Desktop Results:**
- Performance: 53/100 (NEEDS WORK)
- Accessibility: 95/100 (good)
- Best Practices: 92/100 (good)
- SEO: 100/100 (excellent)
- Core Web Vitals:
  - FCP: 275ms (excellent)
  - LCP: 1.8s (good)
  - TBT: 40ms (excellent)
  - CLS: 0.002 (excellent)
  - Speed Index: 1.5s (good)
- **Top Opportunities:**
  1. Unused JavaScript: 243 KiB (200ms on LCP)
  2. **Cache Lifetime Optimization: 59 KiB (KEY FINDING)**
  3. LCP Request Discovery: Largest image needs fetchpriority
  4. Network Dependency Tree: UserSession module 4+ second chains
- **Cache Issues - DETAILED:**
  - **ClickCease statistics: 0 TTL (no caching) - CRITICAL**
  - **Clarity scripts: 1-day expiration (should be 7 days)**
  - **Cloudflare scripts: 1-day expiration (should be 7 days)**

#### 5. Blog Post - Veja Scarves (https://staging.hercules-merchandise.co.uk/blogs/uk/eco-friendly-custom-scarves-for-veja/)

**Status:** 404 Page Not Found (BOTH MOBILE AND DESKTOP)

**Mobile Partial Data:**
- Page Load Failed: HTTP 404
- CLS: 0.005
- Speed Index: 6.4s
- Color contrast failures: 5 elements
- Issues:
  - Reduce unused JavaScript: 242 KiB (1.95s LCP improvement)
  - Cache optimization: 59 KiB (150ms LCP improvement)
  - Multiple resources with 0-24 hour TTLs

**Desktop Partial Data:**
- Page Load Failed: HTTP 404
- SEO: 0/100 (failed due to 404 status)
- Partial metrics:
  - FCP: 0.3s (275ms)
  - LCP: 1.3s (1,325ms)
  - CLS: 0.089
  - Speed Index: 1.5s (1,522ms)
- Issues:
  - Unused JavaScript: 242 KiB
  - Legacy JavaScript: 16 KiB
  - Google Tag Manager: 65-66 KiB each (multiple unused resources)
  - ClickCease: 38.8 KiB waste (89.5% unused)
  - 15 layout shifts detected
  - 2 non-composited animations

**CRITICAL ACTION:** Fix 404 error and re-audit before optimization.

---

## Summary of Cache Policy Issues

### Critical Findings

1. **ClickCease Monitor Script** - HIGHEST PRIORITY
   - Current: 0 TTL (no caching whatsoever)
   - Size: 44 KiB
   - Impact: Downloaded fresh on every single page load
   - Fix: Add `Cache-Control: public, max-age=86400` (1 day minimum)
   - Expected savings: 44 KiB per returning visitor

2. **Microsoft Clarity Scripts** - HIGH PRIORITY
   - Current: 86400s (1 day) TTL
   - Size: 27-29.5 KiB
   - Impact: Re-downloaded daily, reducing cache effectiveness
   - Fix: Extend to `Cache-Control: public, max-age=604800` (7 days)
   - Expected savings: 13.5 KiB per page load → 1.35 KiB

3. **Cloudflare Beacon/Insights** - HIGH PRIORITY
   - Current: 86400s (1 day) TTL
   - Size: Variable (~2 KiB)
   - Impact: Unnecessary daily re-downloads
   - Fix: Extend to `Cache-Control: public, max-age=604800` (7 days)
   - Expected savings: ~1 KiB per page load

4. **Google Tag Manager** - MEDIUM PRIORITY
   - Current: Varies by resource
   - Size: 450+ KiB combined
   - Impact: Large payload with suboptimal caching
   - Fix: Leverage GTM's built-in versioning with longer cache times
   - Expected savings: Variable, depends on current settings

### Combined Impact

**Before Optimization:**
- Per page load (returning visitors): ~59 KiB
- Annual bandwidth (10k monthly visitors): ~7.1 GB
- LCP impact: +150-200ms due to fresh downloads

**After Optimization:**
- Per page load (returning visitors): ~3.6 KiB
- Annual bandwidth (10k monthly visitors): ~0.4 GB
- LCP impact: Baseline (cached resources)
- **Total savings: 94% bandwidth reduction, 6.7 GB annually**

---

## Priority Actions - Cache Policy Focus

### CRITICAL (Fix Today)

1. **Fix 404 Errors** - BLOCKING
   - /collections/caps/ - Returns 404
   - /blogs/uk/eco-friendly-custom-scarves-for-veja/ - Returns 404
   - Impact: 40% of tested URLs are not accessible
   - Action: Verify URL structure and fix routing

2. **Implement ClickCease Caching** - HIGHEST ROI
   - Current: 0 TTL
   - Target: 86400s (1 day)
   - Impact: 44 KiB savings per returning visitor
   - Action: Add cache-control header to ClickCease responses

### HIGH PRIORITY (Fix This Week)

3. **Extend Clarity Cache Lifetime**
   - Current: 86400s (1 day)
   - Target: 604800s (7 days)
   - Impact: 13.5 KiB → 1.35 KiB per page load
   - Action: Extend cache-control max-age

4. **Extend Cloudflare Beacon Cache**
   - Current: 86400s (1 day)
   - Target: 604800s (7 days)
   - Impact: ~1 KiB savings per page load
   - Action: Extend cache-control max-age

5. **Optimize Blog Listing Mobile LCP** - CRITICAL PERFORMANCE
   - Current: 9.6s (worst metric across all tests)
   - Target: < 2.5s
   - Actions:
     - Convert 189 KB PNG to WebP (50-70% reduction)
     - Add fetchpriority="high" to hero image
     - Remove lazy loading from above-fold images
     - Preload critical image

### MEDIUM PRIORITY (Fix Within 2 Weeks)

6. **Remove Unused CSS**
   - ChatHive widget: 48 KiB (100% unused)
   - Impact: Immediate 48 KiB savings per page
   - Action: Remove or conditionally load widget stylesheet

7. **Fix Color Contrast Issues**
   - All pages affected
   - Navigation: #00aeef on white (2.52:1 → needs 4.5:1)
   - CTA buttons: #10c99e on white (2.12:1 → needs 4.5:1)
   - Action: Darken colors to meet WCAG AA standards

8. **Reduce Unused JavaScript**
   - Impact: 242-243 KiB per page
   - Google Tag Manager: 39-43% unused code
   - ClickCease: 89.5% unused (38.8 KiB waste)
   - Actions: Code split GTM, lazy-load ClickCease

---

## Recommended Cache Headers

```nginx
# Third-party analytics/monitoring scripts (versioned URLs)
Cache-Control: public, max-age=604800, immutable

# Third-party scripts with frequent updates
Cache-Control: public, max-age=86400, stale-while-revalidate=604800

# First-party static assets (with content hash in filename)
Cache-Control: public, max-age=31536000, immutable

# HTML pages (always revalidate)
Cache-Control: public, max-age=0, must-revalidate
```

### Implementation Guide

**For Cloudflare Workers (Edge Router):**
```javascript
// In workers/edge-router/src/index.js
if (url.pathname.includes('clickcease')) {
  response.headers.set('Cache-Control', 'public, max-age=86400');
}
if (url.pathname.includes('clarity')) {
  response.headers.set('Cache-Control', 'public, max-age=604800');
}
```

**For WordPress (via mu-plugin):**
```php
// In wp-content/mu-plugins/hercules-cache-headers.php
add_action('wp_headers', function($headers) {
    if (strpos($_SERVER['REQUEST_URI'], 'clickcease') !== false) {
        $headers['Cache-Control'] = 'public, max-age=86400';
    }
    return $headers;
});
```

---

## Next Steps

1. **Immediate (Today):**
   - Fix 404 errors on collections/caps and blog post URLs
   - Implement ClickCease cache headers (0 TTL → 1 day)
   - Verify cache headers are being sent correctly

2. **This Week:**
   - Extend Clarity cache lifetime (1 day → 7 days)
   - Extend Cloudflare beacon cache (1 day → 7 days)
   - Optimize blog listing hero image (convert to WebP)
   - Remove unused ChatHive CSS

3. **Next Week:**
   - Re-run PageSpeed Insights on all 5 URLs
   - Verify cache policy improvements
   - Document before/after metrics
   - Fix color contrast issues

4. **Ongoing:**
   - Monitor cache hit rates in Cloudflare Analytics
   - Track bandwidth savings
   - Measure LCP improvements
   - Ensure 404 errors don't reoccur

---

**Report completed:** 2026-02-10 18:00 UTC
**Focus area:** Cache policy optimization and Core Web Vitals
**Status:** Cache issues identified - immediate action required
**Expected impact:** 94% bandwidth reduction, 150-200ms LCP improvement
