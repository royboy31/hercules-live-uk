# Core Web Vitals Audit - Product Page (Baseline #2)

**URL:** https://hercules-merchandise.de/produkte/personalisiertes-fussballtrikot/
**Strategy:** Mobile
**Date:** 2026-01-31 23:11:26
**Lighthouse Version:** 13.0.1

## Category Scores

- **Performance:** 94/100
- **Accessibility:** 96/100
- **Best Practices:** 96/100
- **SEO:** 92/100

## Comparison with Previous Audit

| Metric | Previous | Current | Change |
|--------|----------|---------|--------|
| Performance Score | 100/100 | 94/100 | **-6 points** |

> **Note:** Performance score decreased from 100 to 94. This audit establishes the baseline before deploying new optimizations.

## Core Web Vitals

- **First Contentful Paint (FCP):** 1.1 s
- **Largest Contentful Paint (LCP):** 3.0 s
- **Total Blocking Time (TBT):** 0 ms
- **Cumulative Layout Shift (CLS):** 0
- **Speed Index:** 3.0 s

## Resource Summary

- **Total Resource Size:** Total size was 2,509 KiB
- **Network Requests:** 104

## Image-Related Issues

### Image elements do not have explicit `width` and `height`
**Score:** 0.5

**Affected Images:** 3

1. `1642444228_ICONS_FOOTBALL-COLLAR-3-1.png`
2. `1642444237_ICONS_FOOTBALL-COLLAR-1-1.png`
3. `1642444246_ICONS_FOOTBALL-COLLAR-2-1.png`

**All image optimization audits passed!**

## Optimization Opportunities

| Opportunity | Time Savings | Byte Savings |
|-------------|--------------|--------------|
| Reduce unused JavaScript | 0.15s | 22.8 KB |

## Summary

The page maintains a strong performance score of 94/100, though down from the previous perfect 100/100.

### Key Findings:

1. **LCP:** 3.0 s - Good
2. **Total Resource Size:** 2,509 KiB - Moderate payload size
3. **Main Issue:** Image elements missing explicit width/height (3 images)
4. **JavaScript Opportunity:** Reduce unused JavaScript (23 KiB, 0.15s savings)
