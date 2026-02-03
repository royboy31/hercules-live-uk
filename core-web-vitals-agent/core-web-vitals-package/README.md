# Core Web Vitals Optimization Agent

Professional PageSpeed Insights auditor for optimizing website performance to achieve 90+ scores across all pages.

## 🚀 Quick Start

### 1. Installation

Copy the `core-web-vitals.md` file to your Claude Code agents directory:

```bash
# On Linux/Mac
cp core-web-vitals.md ~/.claude/agents/

# On Windows
copy core-web-vitals.md %USERPROFILE%\.claude\agents\
```

### 2. Usage with Claude Code

Once installed, you can use the agent with Claude Code by invoking the Task tool:

```
Launch the core-web-vitals agent to audit my website and optimize performance
```

Or more specifically:

```
Use the core-web-vitals agent to:
- Audit all pages on https://yourdomain.com
- Create performance reports
- Optimize pages to achieve 90+ scores
```

### 3. What the Agent Does

The agent will automatically:
1. ✅ Create a `core-web-vitals/` folder in your project
2. ✅ Fetch your sitemap and extract all URLs
3. ✅ Test each URL with Google PageSpeed Insights API
4. ✅ Generate detailed reports with metrics:
   - Performance Score (0-100)
   - Accessibility Score
   - Best Practices Score
   - SEO Score
   - Core Web Vitals (LCP, FCP, TBT, CLS)
5. ✅ Provide optimization recommendations
6. ✅ Track progress in `urls-inventory.md`

## 📊 Reports Generated

After running the agent, you'll find these files in `core-web-vitals/`:

### `urls-inventory.md`
Master tracking sheet with all URLs and their scores:

```
| URL | Perf | A11y | BP | SEO | Status | Last Tested |
|-----|------|------|----|----|--------|-------------|
| https://example.com/ | 92 | 95 | 96 | 100 | ✅ | 2026-01-07 |
| https://example.com/about | 85 | 90 | 92 | 98 | 🟡 | 2026-01-07 |
```

**Status Legend:**
- ✅ Optimized (All 90+)
- 🟡 Good (At least one <90)
- 🔴 Needs Work (Any <70)
- ⏳ Pending (not tested)

### `detailed-reports/[page-name].md`
Individual detailed reports for each page with:
- Full PageSpeed Insights metrics
- Opportunities for improvement (with estimated savings)
- Diagnostics and issues
- Before/after comparisons when optimized

### `optimization-log.md`
Running log of all optimizations performed:
- What was changed
- Before/after scores
- Files modified
- Results achieved

## 🔑 API Key

The PageSpeed Insights API key is already integrated in the agent file:

```
AIzaSyCEBwlUlOrIxXcRLHCiUVInsAsDTunskC4
```

**Important:** This API key is included and ready to use. No additional configuration needed!

## 📝 Example Commands

### Full Site Audit
```
Audit my entire website with the core-web-vitals agent
```

### Test Specific Pages
```
Use core-web-vitals agent to test these URLs:
- https://tulum.immo/
- https://tulum.immo/villa-to-rent/
- https://tulum.immo/for-sale/
```

### Optimization Mode
```
Launch core-web-vitals agent to optimize all pages scoring below 90
```

## 🎯 Target Scores

The agent aims for:
- **Performance:** 90+
- **Accessibility:** 90+
- **Best Practices:** 90+
- **SEO:** 90+

## 🛠️ Common Optimizations

The agent knows how to:
- Optimize and compress images (WebP conversion, resizing)
- Remove unused CSS and JavaScript
- Implement lazy-loading for images and scripts
- Add resource hints (preconnect, preload)
- Minimize main-thread work
- Reduce server response times
- Implement efficient caching strategies

## 📦 Output Structure

After running, your project will have:

```
your-project/
├── core-web-vitals/
│   ├── urls-inventory.md          # Master tracking sheet
│   ├── optimization-log.md        # Log of all changes
│   └── detailed-reports/          # Individual page reports
│       ├── homepage.md
│       ├── about-page.md
│       └── contact-page.md
```

## 🔄 Re-running Tests

The agent can be re-run anytime to:
- Test new pages added to your sitemap
- Re-test after making manual changes
- Verify optimizations are still effective
- Track performance over time

## 💡 Tips

1. **Run during off-peak hours** - More consistent results
2. **Test both mobile and desktop** - Agent tests both automatically
3. **Focus on high-traffic pages first** - Biggest impact
4. **Review recommendations** - Agent provides specific, actionable fixes
5. **Track progress** - Inventory file shows improvement over time

## 🆘 Support

If you encounter issues:
1. Check that the agent file is in `~/.claude/agents/`
2. Ensure you have internet access for API calls
3. Verify your project has a sitemap at `/sitemap.xml` or `/sitemap-index.xml`
4. Check API rate limits (wait 10 seconds between requests)

## 📄 License

This agent is provided for use with Claude Code. The PageSpeed Insights API key is shared for legitimate web performance optimization purposes.

---

**Ready to achieve 90+ scores?** Install the agent and launch it with Claude Code! 🚀
