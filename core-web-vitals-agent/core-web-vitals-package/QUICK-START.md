# Quick Start Guide - Core Web Vitals Agent

## Installation (30 seconds)

### Step 1: Copy the agent file

```bash
# On Linux/Mac
cp core-web-vitals.md ~/.claude/agents/

# On Windows
copy core-web-vitals.md %USERPROFILE%\.claude\agents\
```

### Step 2: That's it! ✅

The API key is already included in the agent. No additional setup needed.

## Usage

Open Claude Code in your project and say:

```
Launch the core-web-vitals agent to audit my website
```

The agent will:
1. Scan your sitemap
2. Test all pages with PageSpeed Insights
3. Create detailed reports in `core-web-vitals/` folder
4. Provide optimization recommendations
5. Track progress to 90+ scores

## Example Commands

**Full audit:**
```
Use core-web-vitals agent to audit tulum.immo
```

**Test specific pages:**
```
Test these URLs with core-web-vitals agent:
- https://tulum.immo/
- https://tulum.immo/villa-to-rent/
```

**Optimize mode:**
```
Launch core-web-vitals agent to optimize all pages below 90
```

## What You'll Get

After running, you'll have:

```
your-project/
└── core-web-vitals/
    ├── urls-inventory.md       # All URLs with scores
    ├── optimization-log.md     # What was changed
    └── detailed-reports/       # Individual page reports
```

## Scores Explained

- **90-100:** ✅ Excellent
- **70-89:** 🟡 Good (needs minor improvements)
- **0-69:** 🔴 Poor (needs optimization)

**Target:** All pages should score 90+ in all 4 categories:
1. Performance
2. Accessibility
3. Best Practices
4. SEO

---

**Need help?** Check README.md for detailed documentation.
