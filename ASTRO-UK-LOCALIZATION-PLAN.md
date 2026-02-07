# Astro UK Site Localization Plan

## Objective
Make the Astro staging site (`hercules-uk-staging-e9z.pages.dev`) match 100% of the live UK WordPress site (`hercules-merchandise.co.uk`).

**Date:** 2026-02-06

---

## Section-by-Section Comparison

### 1. Top Bar

| Element | Astro (Current) | Live (Target) | Status |
|---------|-----------------|---------------|--------|
| Free Design Service | ✅ English | ✅ English | OK |
| Free Delivery | ✅ English | ✅ English | OK |
| 90%+ Made in Europe | ✅ English | ✅ English | OK |
| Sustainable Merch Link | `/collections/sustainability/` | `/collections/sustainable/` | **FIX URL** |
| Google Review Badge | "Google Bewertungen: 5 von 5 Sternen" | Image only (no German text) | **FIX** |

**Files to modify:**
- `src/components/TopBar.astro`
- `src/components/GoogleReviewsBadge.astro`

---

### 2. Header

| Element | Astro (Current) | Live (Target) | Action |
|---------|-----------------|---------------|--------|
| Search placeholder | "Produktsuche" | "Search products..." | **TRANSLATE** |
| My Account link text | "Mein Konto" | (icon only, tooltip: "My Account") | **TRANSLATE** |
| My Account URL | `/mein-konto/` | `/my-account/` | **FIX URL** |
| Wishlist link text | "Wunschliste" | (icon only) | **TRANSLATE** |
| Cart button text | "Warenkorb" | (icon only) | **TRANSLATE** |

**Files to modify:**
- `src/components/Header.astro`

---

### 3. Main Navigation Menu (Static Links)

| Astro (Current) | Live (Target) | URL Change |
|-----------------|---------------|------------|
| SPORTBEKLEIDUNG | SPORTSWEAR | `/kollektionen/personalisierte-sportbekleidung/` → `/collections/custom-printed-sportswear/` |
| SCHALS | SCARVES | `/kollektionen/personalisierte-fanschals/` → `/collections/custom-scarves/` |
| KOPFBEDECKUNG | HEADWEAR | `/kollektionen/kopfbedeckung/` → `/collections/headwear/` |
| MÜTZEN | BEANIES | `/kollektionen/personalisierte-mutzen/` → `/collections/custom-beanies/` |
| CONTACT | CONTACT US | URL should open popup |

**Files to modify:**
- `src/components/Header.astro`
- `src/components/MobileMenu.astro`
- `src/data/menu-data.ts`

---

### 4. Dropdown Menu - SPORTS

| Astro (Current) | Live (Target) | URL Change |
|-----------------|---------------|------------|
| Fußball | Football | `/kollektionen/fussball/` → `/collections/football/` |
| Rugby | Rugby | `/kollektionen/rugby/` → `/collections/rugby/` |
| Basketball | Basketball | OK |
| Laufen | Running | `/kollektionen/laufen/` → `/collections/running/` |
| Feldhockey | Field Hockey | `/kollektionen/feldhockey/` → `/collections/field-hockey/` |
| Volleyball | Volleyball | OK |
| Handball | Handball | OK |
| Radfahren | Cycling | `/kollektionen/radfahren/` → `/collections/cycling/` |
| Fitness | Fitness | OK |
| Golf | Golf | OK |
| e-Sport | eSports | `/kollektionen/esport/` → `/collections/esports/` |

**Files to modify:**
- `src/data/menu-data.ts` (primary - menu fetched from WP API or hardcoded)

---

### 5. Dropdown Menu - PRODUCTS

| Astro (Current) | Live (Target) | URL Change |
|-----------------|---------------|------------|
| Sportbekleidung | Sportswear | → `/collections/custom-printed-sportswear/` |
| Schals | Scarves | → `/collections/custom-scarves/` |
| Mützen | Beanies | → `/collections/custom-beanies/` |
| Kopfbedeckung | Headwear | → `/collections/headwear/` |
| Wimpel | Pennants | → `/collections/custom-pennants/` |
| Handtücher | Towels | → `/collections/towels/` |
| Flaggen | Flags | → `/collections/flags/` |
| Schuhwerk | Footwear | → `/collections/footwear/` |
| Taschen | Bags | → `/collections/bags/` |
| Textile | Textiles | → `/collections/textiles/` |
| Trinkflaschen | Drinkware | → `/collections/drinkware/` |
| Bälle | Balls | → `/collections/balls/` |
| Zubehör | Accessories | → `/collections/accessories/` |

**Files to modify:**
- `src/data/menu-data.ts`

---

### 6. Dropdown Menu - THEMES

| Astro (Current) | Live (Target) | URL Change |
|-----------------|---------------|------------|
| Sommer | Summer | → `/collections/summer/` |
| Winter | Winter | OK |
| Nachhaltigkeit | Sustainable | → `/collections/sustainable/` |
| Hergestellt in Europa | Made in Europe | → `/collections/made-in-europe/` |
| Mode | Fashion | → `/collections/fashion/` |
| Schulanfang | Back to School | → `/collections/back-to-school/` |
| Tifo | Tifo | OK |
| Weihnachten | Christmas | → `/collections/christmas/` |
| Kleine Preise | Small Prices | → `/collections/small-prices/` |
| Geschäft | Business | → `/collections/business/` |
| Werbegeschenke | Giveaways | → `/collections/giveaways/` |
| Kinder | Kids | → `/collections/kids/` |

**Files to modify:**
- `src/data/menu-data.ts`

---

### 7. Hero Slider

#### Slide 1 - Teamwear

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Heading | "DEIN TEAM. DEINE FARBEN. DEIN TRIKOT." | "YOUR TEAM. YOUR COLOURS. YOUR SHIRT." |
| Description | "Erwecken Sie die Identität Ihres Teams mit vollständig personalisierten Sport-Trikots zum Leben" | "Bring your team's identity to life with fully custom sports shirts." |
| Button | "ENTDECKEN SIE UNSERE MANNSCHAFTSBEKLEIDUNG" | "DISCOVER OUR TEAMWEAR" |
| URL | `/collections/personalisierte-sportbekleidung/` | `/collections/custom-printed-sportswear/` |

#### Slide 2 - Scarves

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Heading | "HEBEN SIE SICH AB IN IHREN FARBEN MIT STOLZ." | "STAND OUT IN YOUR COLOURS WITH PRIDE." |
| Description | "Erwecken Sie die Farben Ihres Teams unseren personalisierten Fanschals zum Leben." | "Bring your team's colours to life with our custom fan scarves." |
| Button | "ENTDECKEN SIE UNSERE SCHALS" | "DISCOVER OUR SCARVES" |
| URL | `/collections/personalisierte-fanschals/` | `/collections/custom-scarves/` |

#### Slide 3 - Slides/Slippers

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Heading | "UMKLEIDEKABINEN- KOMFORT, STREETSTYLE-STOLZ." | "CHANGING ROOM COMFORT, STREET STYLE PRIDE." |
| Description | "Personalisierte Badeschlappen in Ihren Vereinsfarben, perfekt für Turniertage und darüber hinaus" | "Custom slides with your club colours, perfect for match days and beyond." |
| Button | "ENTDECKEN SIE UNSERE VEREINSSCHUHE" | "DISCOVER OUR CLUB SLIDES" |
| URL | `/products/personalisierte-badeschlappen/` | `/products/custom-club-slides-and-slippers/` |

**Files to modify:**
- `src/components/Slider.astro`

---

### 8. Main H1 Title

| Astro (Current) | Live (Target) |
|-----------------|---------------|
| "Die Nummer 1 für personalisierte Sportartikel" | "The number 1 in custom sport merchandise" |

**Files to modify:**
- `src/pages/index.astro`

---

### 9. Top Performers Section

**Section title:** Both have "TOP PERFORMERS IN THE SPOTLIGHT" ✅

#### Products to Update:

| Astro (Current) | Live (Target) | URL Change |
|-----------------|---------------|------------|
| PERSONALISIERTER HD-FUSSBALLSCHAL | CUSTOM HD FOOTBALL SCARF | `/products/personalisierter-fussballschal` → `/products/custom-football-scarf/` |
| PERSONALISIERTE STANDARD-MÜTZE | STANDARD BEANIE HAT | `/products/custom-football-beanie-hats` → same |
| PERSONALISIERTER FISCHERHUT | CUSTOM BUCKET HAT | `/products/personalisierter-fischerhut` → `/products/custom-made-bucket-hats/` |
| BASECAP | BASEBALL CAP | `/products/baseball-cap` → same |
| PERSONALISIERTE BADESCHLAPPEN | CUSTOM CLUB SLIDES | `/products/personalisierte-badeschlappen` → `/products/custom-club-slides-and-slippers/` |
| INDIVIDUELL GEDRUCKTER WIMPEL | CUSTOM PRINTED PENNANT | `/products/individuell-gedruckter-wimpel` → `/products/custom-printed-pennants/` |
| PERSONALISIERTE HANDTÜCHER | CUSTOM PRINTED TOWEL | `/products/personalisierte-handtucher` → `/products/custom-sublimated-towels/` |
| PERSONALISIERTES FUSSBALLTRIKOT | CUSTOM FOOTBALL SHIRT | `/products/personalisiertes-fussballtrikot` → `/products/custom-football-shirts/` |

**Note:** Live site has "EXPLORE MORE" buttons, Astro has "DISCOVER MORE" - consider aligning.

**Files to modify:**
- `src/components/TopPerformer.astro`
- `src/data/homepage-products.json` (if products are defined there)

---

### 10. Why Choose Us Section

| Element | Astro (Current) | Live (Target) | Status |
|---------|-----------------|---------------|--------|
| Title | "WHY CHOOSE US?" | "WHY CHOOSE US" | Minor (remove ?) |
| Excellent Service | ✅ English | ✅ English | OK |
| Free Designs | ✅ English | ✅ English | OK |
| European Production | ✅ English | ✅ English | OK |

**Files to modify:**
- `src/components/WhyChooseUs.astro` (minor title fix)

---

### 11. Design Service Section

| Element | Astro (Current) | Live (Target) | Status |
|---------|-----------------|---------------|--------|
| Title | ✅ Same | ✅ Same | OK |
| Description | ✅ Same | ✅ Same | OK |
| Button | "REQUEST FREE DESIGN" | "ASK FOR YOUR FREE DESIGN" | **UPDATE** |

**Files to modify:**
- `src/components/DesignService.astro`

---

### 12. About Hercules Section

| Element | Astro (Current) | Live (Target) | Status |
|---------|-----------------|---------------|--------|
| Title | ✅ Same | ✅ Same | OK |
| Content | ✅ Very similar | ✅ Same | Minor wording |
| Button | "CONTACT US NOW" | "CONTACT US NOW" | OK |

**Files to modify:**
- `src/components/HerculesMerchandise.astro` (minor wording if needed)

---

### 13. Trust Logos Section

| Element | Status |
|---------|--------|
| Title "THEY TRUST US" | ✅ OK |
| Logos | ✅ Same logos | OK |

---

### 14. Customer Reviews Section

| Element | Status |
|---------|--------|
| Title "WHAT CUSTOMERS SAY ABOUT US" | ✅ OK |

---

### 15. Sticky Header

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Menu toggle | "Menü Umschalter" | (icon only) |
| All Categories button | "ALLE KATEGORIEN" | "ALL CATEGORIES" |
| Search placeholder | "Produktsuche" | "Search products..." |
| My Account | "Mein Konto" | (icon) |
| Wishlist | "Wunschliste" | (icon) |
| Cart | "Warenkorb" | (icon) |

**Files to modify:**
- `src/components/StickyHeader.astro`

---

### 16. Mobile Menu

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| All category labels | German | English |
| All URLs | German slugs | English slugs |
| Back button | "Back" | Same |
| Close button | "Close menu" | Same |

**Files to modify:**
- `src/components/MobileMenu.astro`

---

### 17. Cookie Consent

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Title | "Cookie-Einstellungen" | "Cookie Settings" |
| Description | German text | English text |
| Accept button | "Alle akzeptieren" | "Accept All" |
| Necessary button | "Nur notwendige" | "Necessary Only" |
| Settings button | "Einstellungen anpassen" | "Customize Settings" |

**Files to modify:**
- `src/components/CookieConsent.tsx`

---

### 18. Chat Widget (Chathive)

| Element | Astro (Current) | Live (Target) |
|---------|-----------------|---------------|
| Close button | "Schließen" | "Close" |
| Greeting | German greeting | English greeting |

**Note:** This is likely configured in Chathive dashboard, not in code.

**Action:** Update Chathive widget configuration for UK site.

---

### 19. Footer

| Element | Astro (Current) | Live (Target) | Action |
|---------|-----------------|---------------|--------|
| Newsletter heading | Check | "INTERESTED?" | Verify |
| Newsletter text | Check | "Be first to see our latest sports merchandise..." | Verify |
| Submit button | Check | "SUBMIT" | Verify |
| About section links | German URLs | English URLs | **FIX** |
| Types of Sport links | German labels/URLs | English | **FIX** |
| Products links | German labels/URLs | English | **FIX** |
| Reseller section | Check | English | Verify |
| Phone number | Check | "(+44) 0203 9664881" | Verify |
| Legal links | German? | English | **FIX** |

**Files to modify:**
- `src/components/Footer.astro`

---

## Summary: Files to Modify

### High Priority (German text visible to users)

1. **`src/components/Slider.astro`** - Hero slider text and URLs
2. **`src/components/Header.astro`** - Search, account, wishlist, cart labels
3. **`src/components/StickyHeader.astro`** - Same as header
4. **`src/components/MobileMenu.astro`** - All mobile menu labels
5. **`src/data/menu-data.ts`** - All menu categories and URLs
6. **`src/components/TopPerformer.astro`** - Product names
7. **`src/components/CookieConsent.tsx`** - Cookie consent text
8. **`src/components/GoogleReviewsBadge.astro`** - German alt text
9. **`src/pages/index.astro`** - H1 title

### Medium Priority (Minor text differences)

10. **`src/components/Footer.astro`** - Footer labels and URLs
11. **`src/components/DesignService.astro`** - Button text
12. **`src/components/TopBar.astro`** - Sustainable link URL

### Low Priority (Already mostly correct)

13. **`src/components/WhyChooseUs.astro`** - Minor title
14. **`src/components/HerculesMerchandise.astro`** - Minor wording

### External Configuration

15. **Chathive Widget** - Configure in Chathive dashboard for English

---

## URL Mapping Reference

### Collection URLs (German → English)

```
/kollektionen/fussball/ → /collections/football/
/kollektionen/rugby/ → /collections/rugby/
/kollektionen/basketball/ → /collections/basketball/
/kollektionen/laufen/ → /collections/running/
/kollektionen/feldhockey/ → /collections/field-hockey/
/kollektionen/volleyball/ → /collections/volleyball/
/kollektionen/handball/ → /collections/handball/
/kollektionen/radfahren/ → /collections/cycling/
/kollektionen/fitness/ → /collections/fitness/
/kollektionen/golf/ → /collections/golf/
/kollektionen/esport/ → /collections/esports/
/kollektionen/personalisierte-sportbekleidung/ → /collections/custom-printed-sportswear/
/kollektionen/personalisierte-fanschals/ → /collections/custom-scarves/
/kollektionen/personalisierte-mutzen/ → /collections/custom-beanies/
/kollektionen/kopfbedeckung/ → /collections/headwear/
/kollektionen/personalisierte-wimpel/ → /collections/custom-pennants/
/kollektionen/handtucher/ → /collections/towels/
/kollektionen/flaggen/ → /collections/flags/
/kollektionen/schuhwerk/ → /collections/footwear/
/kollektionen/taschen/ → /collections/bags/
/kollektionen/personalisierte-textilien/ → /collections/textiles/
/kollektionen/trinkflaschen/ → /collections/drinkware/
/kollektionen/balle/ → /collections/balls/
/kollektionen/zubehor/ → /collections/accessories/
/kollektionen/sommer/ → /collections/summer/
/kollektionen/winter/ → /collections/winter/
/kollektionen/nachhaltigkeit/ → /collections/sustainable/
/kollektionen/hergestellt-in-europa/ → /collections/made-in-europe/
/kollektionen/mode/ → /collections/fashion/
/kollektionen/schulanfang/ → /collections/back-to-school/
/kollektionen/tifo/ → /collections/tifo/
/kollektionen/weihnachten/ → /collections/christmas/
/kollektionen/kleine-preise/ → /collections/small-prices/
/kollektionen/business/ → /collections/business/
/kollektionen/werbegeschenke/ → /collections/giveaways/
/kollektionen/kinder/ → /collections/kids/
```

### Product URLs (German → English)

```
/products/personalisierter-fussballschal → /products/custom-football-scarf/
/products/personalisierter-fischerhut → /products/custom-made-bucket-hats/
/products/personalisierte-badeschlappen → /products/custom-club-slides-and-slippers/
/products/individuell-gedruckter-wimpel → /products/custom-printed-pennants/
/products/personalisierte-handtucher → /products/custom-sublimated-towels/
/products/personalisiertes-fussballtrikot → /products/custom-football-shirts/
```

### Other URLs

```
/mein-konto/ → /my-account/
/wishlist/ → /wishlist/ (OK)
/contact-us → /contact-us/ (OK, verify popup behavior)
```

---

## Implementation Order

### Phase 1: Core Homepage Content
1. [ ] Fix `src/components/Slider.astro` - All 3 slides
2. [ ] Fix `src/pages/index.astro` - H1 title
3. [ ] Fix `src/components/TopPerformer.astro` - Product names and URLs

### Phase 2: Navigation
4. [ ] Fix `src/data/menu-data.ts` - All category names and URLs
5. [ ] Fix `src/components/Header.astro` - Labels and URLs
6. [ ] Fix `src/components/StickyHeader.astro` - Labels
7. [ ] Fix `src/components/MobileMenu.astro` - All labels

### Phase 3: Supporting Components
8. [ ] Fix `src/components/CookieConsent.tsx` - All text
9. [ ] Fix `src/components/GoogleReviewsBadge.astro` - Alt text
10. [ ] Fix `src/components/Footer.astro` - All links and labels
11. [ ] Fix `src/components/DesignService.astro` - Button text
12. [ ] Fix `src/components/TopBar.astro` - Sustainable URL

### Phase 4: Testing & Verification
13. [ ] Build and deploy
14. [ ] Cross-check all pages against live site
15. [ ] Test all links work correctly
16. [ ] Verify mobile responsiveness

### Phase 5: External Services
17. [ ] Update Chathive widget configuration for English

---

## Notes

- The menu data might be fetched from WordPress API at build time. If so, the UK WordPress needs to have English menu configured, OR we need to hardcode the menu in `menu-data.ts`.
- Product names in Top Performers section might also come from WordPress/WooCommerce. Check if they're hardcoded or fetched.
- Some URLs on the live site end with `/` and some don't - be consistent.

---

*Plan created: 2026-02-06*
