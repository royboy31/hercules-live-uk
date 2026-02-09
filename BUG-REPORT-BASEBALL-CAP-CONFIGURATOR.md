# Bug Report: Baseball Cap Configurator Steps Disappear

**Date**: 2026-02-08
**Product**: Baseball Cap (ID: 10105)
**Environment**: staging.hercules-merchandise.co.uk
**Severity**: CRITICAL - Blocks product configuration
**Status**: IDENTIFIED - Needs Fix

---

## Issue Summary

The product configurator **completely disappears** when advancing from Step 4 (Cap side decoration) to Step 5 (Accessory), preventing users from completing their product configuration.

---

## Reproduction Steps

1. Navigate to: https://staging.hercules-merchandise.co.uk/products/baseball-cap/
2. Select any option in **Step 1: Cap front decoration** (e.g., "EMBROIDERY")
3. Select any option in **Step 2: Embroidery** (e.g., "2D EMBROIDERY - MAX 55 MM HIGH")
4. Select any option in **Step 3: Cap back decoration** (e.g., "NONE")
5. Select any option in **Step 4: Cap side decoration** (e.g., "NONE")
6. **BUG**: Entire configurator disappears - Steps 5 & 6 never shown

---

## Expected vs Actual Behavior

### Expected
- After selecting option in Step 4, Step 5 (Accessory) should expand
- User should see 2 accessory options: "Care label" and "Woven flag label"
- Configuration should continue to Step 6 (Choose your quantity)

### Actual
- After selecting option in Step 4, configurator is **completely removed from DOM**
- Heading changes from "CREATE YOUR PRODUCT — STEP 4 OF 6" to just "Baseball Caps"
- All step elements disappear (0 h2/h3 headings found)
- No error message shown to user
- Product page becomes non-functional

---

## Technical Analysis

### JavaScript Error

**Console Error:**
```
Uncaught TypeError: t.options.map is not a function
```

**Location**: Configurator JavaScript code
**Trigger**: Attempting to render step with malformed options data
**Effect**: Entire React/JavaScript component crashes and unmounts

### Root Cause

Multiple child addons have **empty options arrays** in WordPress database:

| Addon ID | Name | Parent | Visible If | Options Count | Status |
|----------|------|--------|------------|---------------|--------|
| 87 | Print | Cap back decoration (75) | Always | **0** | ❌ EMPTY |
| 272 | Sublimation patch | Cap back decoration (75) | Always | **0** | ❌ EMPTY |
| 275 | Print | Cap side decoration (79) | Always | **0** | ❌ EMPTY |

These empty addon configurations cause the JavaScript `.map()` function to fail when the configurator tries to iterate over `options`.

### DOM Investigation Results

**After error occurs:**
- `document.querySelectorAll('h2')` matching "STEP": **0 results**
- `document.querySelectorAll('h3')` matching step numbers: **0 results**
- Elements with "step" class: **0 results**
- Main configurator container: **Not found**
- Body text contains "STEP": **false**

**Conclusion**: Entire configurator component was removed from DOM by JavaScript error handler.

---

## Product Configuration Data

### WordPress Database
**Product ID**: 10105 (Baseball cap)
**Meta Key**: `_product_allowed_addon_ids`
**Allowed Addons**: `a:4:{i:0;i:72;i:1;i:75;i:2;i:79;i:3;i:80;}`

**Addons Used:**
- 72: Cap front decoration
- 75: Cap back decoration
- 79: Cap side decoration
- 80: Accessory

### Complete Addon Structure

```
Total addons configured: 12

Level 1 - Main Steps:
├─ Addon 72: Cap front decoration (5 options) ✅
│  ├─ Addon 73: Embroidery (4 options, visible if: Embroidery) ✅
│  └─ Addon 77: Sublimation patch (3 options, visible if: Sublimation patch) ✅
│
├─ Addon 75: Cap back decoration (4 options) ✅
│  ├─ Addon 271: Embroidery (4 options, visible if: Embroidery) ✅
│  ├─ Addon 87: Print (0 options, visible if: Always) ❌ EMPTY
│  └─ Addon 272: Sublimation patch (0 options, visible if: Always) ❌ EMPTY
│
├─ Addon 79: Cap side decoration (6 options) ✅
│  ├─ Addon 274: Embroidery (4 options, visible if: Embroidery) ✅
│  ├─ Addon 275: Print (0 options, visible if: Always) ❌ EMPTY
│  └─ Addon 276: Sublimation Patch (3 options, visible if: Sublimation patch) ✅
│
└─ Addon 80: Accessory (2 options) ✅
```

**Main Issue**: Addons 87, 272, and 275 have **empty options arrays** with `visible_if_option` set to "Always", causing them to render but fail when JavaScript tries to `.map()` over non-existent options.

---

## Comparison with Live Site

**User Note**: "live site works fine, it's the same product there"

**Investigation Result**: ❌ **The live site does NOT use the same configurator!**

The live production site (hercules-merchandise.co.uk) uses a **traditional WordPress product template** without the step-by-step configurator interface. It displays:
- Standard product images
- "DO YOU HAVE A QUESTION?" contact section
- No interactive step-by-step configuration

**Conclusion**: This bug is **specific to the Astro headless implementation** on the staging site. The issue does not exist on the live site because the live site doesn't use the React/JavaScript configurator that's failing on staging.

**Implication**: This is a **blocking issue** for migrating to the Astro headless version, as the configurator is completely non-functional for products with empty child addon options.

---

## Affected Data

### Empty Addon Details

**Addon 87 - Print (child of Cap back decoration)**
```php
meta_value: a:2:{
  s:7:"options";a:0:{}  // EMPTY ARRAY
  s:17:"visible_if_option";s:0:""
}
```

**Addon 272 - Sublimation patch (child of Cap back decoration)**
```php
meta_value: a:1:{
  s:17:"visible_if_option";s:0:""  // Missing options key entirely!
}
```

**Addon 275 - Print (child of Cap side decoration)**
```php
meta_value: a:2:{
  s:17:"visible_if_option";s:0:"";
  s:7:"options";a:0:{}  // EMPTY ARRAY
}
```

---

## Recommended Fixes

### Option 1: Fix WordPress Data (Preferred)

**For each empty addon, either:**

**A) Add placeholder options:**
```sql
UPDATE wp_1202943_postmeta
SET meta_value = 'a:2:{s:7:"options";a:1:{i:0;a:3:{s:4:"name";s:9:"No option";s:5:"image";s:0:"";s:11:"price_table";a:0:{}}}s:17:"visible_if_option";s:0:"";}'
WHERE post_id = 10105 AND meta_key = '_product_addon_options';
```

**B) Set visibility condition to prevent rendering:**
```sql
-- Change visible_if_option from empty to a specific condition
-- Or remove from allowed addons list
```

**C) Remove from allowed addons:**
```sql
UPDATE wp_1202943_postmeta
SET meta_value = 'a:4:{i:0;i:72;i:1;i:75;i:2;i:79;i:3;i:80;}'
WHERE post_id = 10105 AND meta_key = '_product_allowed_addon_ids';
-- Ensure empty addons (87, 272, 275) are not in child addon lists
```

### Option 2: Fix JavaScript Code

**Add null/empty checking before .map():**

```javascript
// Before (causes error)
const options = addon.options.map(opt => ...);

// After (handles empty arrays)
const options = Array.isArray(addon.options) && addon.options.length > 0
  ? addon.options.map(opt => ...)
  : [];
```

**Location**: Product configurator JavaScript (likely in theme or plugin files)
**Files to check**:
- Theme JS: `wp-content/themes/[theme]/assets/js/configurator.js`
- Plugin JS: `wp-content/plugins/pearl-wc-steps/assets/js/`

### Option 3: Combined Approach (Recommended)

1. **Immediate**: Fix JavaScript to handle empty arrays gracefully
2. **Data cleanup**: Review and fix all empty addon configurations in WordPress
3. **Validation**: Add WordPress admin validation to prevent empty addon options

---

## Steps to Fix (WordPress Admin)

1. **Login to WordPress Admin**: https://staging.hercules-merchandise.co.uk/wp-admin/
2. **Navigate to Products** → Edit "Baseball cap"
3. **Find Product Addons section** (likely custom meta boxes or ACF fields)
4. **Review each addon**:
   - Addon 87: Print (child of Cap back decoration) - Add options or disable
   - Addon 272: Sublimation patch (child of Cap back decoration) - Add options or disable
   - Addon 275: Print (child of Cap side decoration) - Add options or disable
5. **Either**:
   - Add at least one option to each addon, OR
   - Set `visible_if_option` to a specific condition, OR
   - Remove from the allowed addons list
6. **Test**: Clear cache and test configurator flow

---

## Testing Checklist

After implementing fix:

- [ ] Load baseball cap product page
- [ ] Complete Step 1 (Cap front decoration)
- [ ] Complete Step 2 (Embroidery sub-options)
- [ ] Complete Step 3 (Cap back decoration)
- [ ] **Complete Step 4 (Cap side decoration)**
- [ ] **Verify Step 5 (Accessory) appears**
- [ ] **Verify Step 6 (Choose quantity) appears**
- [ ] Verify "CREATE QUOTE" button becomes enabled
- [ ] Verify "ADD TO CART" button becomes enabled
- [ ] Test with different option combinations
- [ ] Check browser console for JavaScript errors
- [ ] Test on different browsers (Chrome, Firefox, Safari)
- [ ] Test on mobile devices

---

## Related Files

### Investigation Files Created
- `/tmp/staging-baseball-cap-config.json` - Full addon configuration from API
- `/tmp/baseball-cap-addon-options.txt` - Raw database addon options data

### Code Files to Review
- Product configurator JavaScript (location TBD)
- `wp-content/mu-plugins/pearl-rest-api-meta.php` - Exposes addon data via API
- Theme product template files

---

## Additional Notes

- **User cannot complete purchase** until this is fixed
- **Same issue likely affects other products** with empty child addons
- **Live site reportedly works** - need to compare configurations
- JavaScript error handling could be improved to show user-friendly error instead of breaking entire UI

---

## Next Actions

1. [ ] Compare staging vs live WordPress addon configurations
2. [ ] Identify correct fix approach (data vs code)
3. [ ] Implement fix in staging
4. [ ] Test thoroughly
5. [ ] Deploy to production if not already fixed there
6. [ ] Add validation to prevent future empty addon configurations

---

**Reported by**: Claude Code Analysis
**Report Date**: 2026-02-08
**Priority**: CRITICAL
**Estimated Fix Time**: 30-60 minutes (data fix) or 2-4 hours (code fix)
