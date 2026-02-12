# Hercules Merchandise UK - Comprehensive QA Checklist

**Site URL:** https://9a668c27.hercules-uk-staging-e9z.pages.dev/
**Date Created:** 2026-02-09
**Purpose:** Complete quality assurance testing of all site elements, pages, and functionalities

---

## 🎯 QUICK START CHECKLIST

### Critical Functionality (Test First)
- [ ] Site loads without errors
- [ ] Mobile/Desktop responsive switching works
- [ ] Contact forms submit successfully
- [ ] Product search functions
- [ ] Cart functionality works
- [ ] Wishlist functionality works
- [ ] Navigation menus open/close properly

---

## 📱 MOBILE HEADER (< 768px)

### Top Bar - Dark Blue (#253461)
- [ ] **Google Reviews Badge**
  - [ ] Badge displays correctly (rating + review count)
  - [ ] Shows "4.9" rating
  - [ ] Shows "135+ Reviews" text
  - [ ] Badge is clickable and links to Google Maps
  - [ ] Opens in new tab with correct link
  - [ ] Badge scales properly on different mobile sizes
  - [ ] Stars display correctly (5 gold stars)

### Logo & Icons Row
- [ ] **Logo**
  - [ ] Hercules logo displays correctly
  - [ ] Logo size: 140px width on mobile
  - [ ] Logo is sharp/crisp (2x retina)
  - [ ] Logo links to homepage (/)
  - [ ] Logo scales on very small screens

- [ ] **Search Button** (White bg, dark blue border)
  - [ ] Button displays with search icon
  - [ ] Icon size: 22x22px
  - [ ] Border: 1px solid #253461
## 🔝 TOP BAR (Desktop Only - Above Main Header)

### Container & Layout
- [ ] **Display**
  - [ ] Visible on desktop (> 768px)
  - [ ] Hidden on mobile (<= 768px)
  - [ ] Background: #253461 (dark blue)
  - [ ] Padding: 4px 0 8px 0

- [ ] **Container**
  - [ ] Max width: 1280px
  - [ ] Centered on page
  - [ ] Display: flex with space-between
  - [ ] Aligns service list left, reviews right

### Service Features (Left Side)
- [ ] **Service List Container**
  - [ ] Display: flex horizontal
  - [ ] Gap: 50px between items (margin: 0 25px each)
  - [ ] White text color (#ffffff)
  - [ ] Font: Jost, 14px, weight 500

- [ ] **Service Item 1: Free Design Service**
  - [ ] Icon displays (palette/design icon)
  - [ ] Icon size: 23x23px
  - [ ] Icon color: #23C3FF (cyan blue)
  - [ ] Text: "Free Design Service"
  - [ ] Icon + text aligned horizontally
  - [ ] Gap between icon and text: 11px

- [ ] **Service Item 2: Free Delivery**
  - [ ] Icon displays (truck icon)
  - [ ] Icon size: 23x23px
  - [ ] Icon color: #23C3FF
  - [ ] Text: "Free Delivery"
  - [ ] Same styling as item 1

- [ ] **Service Item 3: 90%+ Made in Europe**
  - [ ] Icon displays (trophy/award icon)
  - [ ] Icon size: 23x23px
  - [ ] Icon color: #23C3FF
  - [ ] Text: "90%+ Made in Europe"
  - [ ] On tablet (< 900px): text changes to just "Made in Europe"
  - [ ] Hidden on smaller screens (< 768px)

- [ ] **Service Item 4: Sustainable Merch Available**
  - [ ] Icon displays (leaf/eco icon)
  - [ ] Icon size: 23x23px
  - [ ] Icon color: #10A380 (green - different from others)
  - [ ] Text: "Sustainable Merch Available"
  - [ ] Item is a link (href="/collections/sustainable/")
  - [ ] Link hover works
  - [ ] Hidden on tablet (< 1024px)

### Google Reviews Badge (Right Side)
- [ ] **Badge Component**
  - [ ] Dynamic GoogleReviewsBadge displays
  - [ ] Width: 154px (aspect ratio 306/36)
  - [ ] Shows rating (4.9)
  - [ ] Shows stars (5 gold stars)
  - [ ] Shows review count (135+ Reviews)
  - [ ] All elements visible and crisp
  - [ ] Badge is clickable
  - [ ] Click behavior works:
    - [ ] If on homepage: scrolls to review section
    - [ ] If on other page: redirects to /#review-section
  - [ ] Smooth scroll animation works
  - [ ] URL hash updates correctly (#review-section)

### Responsive Behavior
- [ ] **Desktop (> 1200px)**
  - [ ] All 4 service items visible
  - [ ] Full spacing: 50px gaps
  - [ ] Font size: 14px

- [ ] **Tablet (< 1200px)**
  - [ ] Gap reduced: 30px (margin: 0 15px)
  - [ ] Font size: 13px
  - [ ] Icon size: 20x20px
  - [ ] All items still visible

- [ ] **Tablet (< 1024px)**
  - [ ] 4th item (Sustainable) hidden
  - [ ] Only 3 service items + reviews badge

- [ ] **Small Tablet (< 900px)**
  - [ ] 3rd item text shortened to "Made in Europe"

- [ ] **Mobile (<= 768px)**
  - [ ] Entire top bar hidden completely

---

## 📌 STICKY HEADER - DETAILED (Desktop Only)

### Activation & Behavior
- [ ] **Display**
  - [ ] Hidden on mobile (<= 768px) always
  - [ ] Visible on desktop (> 769px)
  - [ ] Position: fixed, top: 0
  - [ ] z-index: 1000 (above other content)

- [ ] **Scroll Trigger**
  - [ ] Hidden when at top of page
  - [ ] Appears when scrolled down ~150-200px
  - [ ] Transform: translateY(-100%) to translateY(0)
  - [ ] Transition: 0.3s ease
  - [ ] Box shadow: 0px 9px 10px rgba(0,0,0,0.05)

- [ ] **Visual State**
  - [ ] Background: white
  - [ ] Padding: 10px 0
  - [ ] Max width: 1280px, centered
  - [ ] Smooth slide-down animation

### Layout Structure (3 Sections)
- [ ] **Overall Layout**
  - [ ] Display: flex
  - [ ] 3 sections: Menu (25%), Search (50%), Icons (25%)
  - [ ] Aligned horizontally
  - [ ] All sections proper width

### Section 1: Menu (25% Width - Left)
- [ ] **Hamburger Button**
  - [ ] Size: 48x48px
  - [ ] Background: #253461 (dark blue)
  - [ ] Border radius: 16px
  - [ ] Icon: hamburger menu (☰)
  - [ ] Icon size: 24x24px
  - [ ] Icon color: white
  - [ ] Click toggles dropdown menu
  - [ ] Icon changes to X when active
  - [ ] Transition smooth

- [ ] **"ALL CATEGORIES" Text Button**
  - [ ] Text: "ALL CATEGORIES"
  - [ ] Font: Jost, 15px, weight 500, uppercase
  - [ ] Color: #253461
  - [ ] Hover color: #00AEEF
  - [ ] Triangle indicator next to text
  - [ ] Triangle rotates 180deg when menu open
  - [ ] Click also toggles dropdown menu
  - [ ] Gap between hamburger and text: 15px

- [ ] **Dropdown Navigation Menu**
  - [ ] Appears below sticky header when activated
  - [ ] Position: absolute, top: 100%
  - [ ] Background: white
  - [ ] Box shadow: 0 4px 20px rgba(0,0,0,0.15)
  - [ ] Border radius: 8px
  - [ ] Opacity transition: 0 to 1 (0.3s)
  - [ ] Min height: 500px
  - [ ] Flex direction: column

  - [ ] **Logo Header** (Top section)
    - [ ] Full width at top
    - [ ] Hercules logo displays
    - [ ] Logo size: 55px height (auto width)
    - [ ] Padding: 25px
    - [ ] Border bottom: 1px solid #E5E7EB
    - [ ] Logo centered or left-aligned

  - [ ] **Content Area** (Two columns)
    - [ ] Display: flex row
    - [ ] Left column + Right column

  - [ ] **Left Column: Main Menu** (240px width)
    - [ ] Width: 240px fixed
    - [ ] Border right: 1px solid #E5E7EB
    - [ ] Background: white
    - [ ] Display: flex column
    
    - [ ] **Sports Menu Item**
      - [ ] Button text: "Sports"
      - [ ] Font: Jost, 18px, weight 600, uppercase
      - [ ] Color: #253461
      - [ ] Height: 65px
      - [ ] Padding: 22px 25px
      - [ ] Border bottom: 1px solid #F0F0F0
      - [ ] Arrow icon on right (16x16px)
      - [ ] Hover: color #00AEEF, background #F5F5F5
      - [ ] Active state when submenu shown
      - [ ] Hover shows Sports submenu in right column
    
    - [ ] **Products Menu Item**
      - [ ] Same structure as Sports
      - [ ] Text: "Products"
      - [ ] Shows Products submenu on hover
    
    - [ ] **Themes Menu Item**
      - [ ] Same structure as Sports
      - [ ] Text: "Themes"
      - [ ] Shows Themes submenu on hover

  - [ ] **Right Column: Submenu Display** (Flexible width, min 500px)
    - [ ] Flex: 1 (takes remaining space)
    - [ ] Min width: 500px
    - [ ] Display: flex column
    
    - [ ] **Submenu Title**
      - [ ] Displays active submenu name: "SPORTS", "PRODUCTS", or "THEMES"
      - [ ] Font: Jost, 22px, weight 600, uppercase
      - [ ] Color: #253461
      - [ ] Padding: 20px 25px
      - [ ] Changes when different menu item hovered
    
    - [ ] **Submenu Items Container**
      - [ ] Padding: 0 25px 25px 25px
      - [ ] Grid layout: 3 columns x 5 rows
      - [ ] Grid auto-flow: column (fills columns first)
      - [ ] Row gap: 0, Column gap: 0
    
    - [ ] **Individual Submenu Items**
      - [ ] Display: flex with icon + text
      - [ ] Height: 55px
      - [ ] Padding: 0 25px
      - [ ] Icon size: 28x28px
      - [ ] Gap between icon and text: 15px
      - [ ] Font: Jost, 17px, weight 500
      - [ ] Color: #253461
      - [ ] Hover: color #469ADC, background #F5F5F5
      - [ ] Text: nowrap (doesn't wrap)
      - [ ] Links work correctly

  - [ ] **Sports Submenu**
    - [ ] All sport categories listed (Football, Rugby, Basketball, etc.)
    - [ ] Icons display correctly
    - [ ] Grid layout working (3 cols x 5 rows)
    - [ ] All links functional

  - [ ] **Products Submenu**
    - [ ] All product categories listed (Scarves, Beanies, etc.)
    - [ ] Icons display correctly
    - [ ] Grid layout working
    - [ ] All links functional

  - [ ] **Themes Submenu**
    - [ ] All theme categories listed (Made in Europe, Sustainable, etc.)
    - [ ] Icons display correctly
    - [ ] Grid layout working
    - [ ] All links functional

- [ ] **Dropdown Close Behavior**
  - [ ] Click hamburger again closes menu
  - [ ] Click "ALL CATEGORIES" again closes menu
  - [ ] Click outside menu closes menu
  - [ ] ESC key closes menu (if implemented)
  - [ ] Transition smooth on close

### Section 2: Search (50% Width - Center)
- [ ] **Search Input**
  - [ ] ProductSearch component displays
  - [ ] Width: 50% of header
  - [ ] Border: 1px solid #253461
  - [ ] Border radius: 15px (or consistent)
  - [ ] Placeholder: "Search products..."
  - [ ] Placeholder color: #253461
  - [ ] Font size: 16px
  - [ ] Search icon visible
  - [ ] Typing triggers search
  - [ ] Results dropdown appears
  - [ ] Results styled correctly
  - [ ] All search functionality working (same as main header)

### Section 3: Icons (25% Width - Right)
- [ ] **Icons Container**
  - [ ] Display: flex row
  - [ ] Justify: flex-end (right-aligned)
  - [ ] Gap: 8px between icons
  - [ ] All icons same size: 40x40px

- [ ] **Contact/Email Button**
  - [ ] Mail icon displays
  - [ ] Background: #10C99E (green)
  - [ ] Border: 1px solid #10C99E
  - [ ] Border radius: 15px
  - [ ] Padding: 11px
  - [ ] Icon size: 22x22px
  - [ ] Icon color: white
  - [ ] Hover: background white, icon green, border green
  - [ ] Click opens contact popup
  - [ ] Transition: 0.3s

- [ ] **Account Icon**
  - [ ] User icon displays
  - [ ] Border: 1px solid #253461
  - [ ] Border radius: 15px
  - [ ] Icon size: 22x22px
  - [ ] Icon color: #253461
  - [ ] Hover: border color #469ADC, icon color #469ADC
  - [ ] If logged in: green dot indicator (top-right)
    - [ ] Dot size: 10x10px
    - [ ] Dot color: #10C99E
    - [ ] Dot position: top -3px, right -3px
    - [ ] White border: 2px
  - [ ] Click behavior same as main header
  - [ ] Transition: 0.3s

- [ ] **Wishlist Icon**
  - [ ] Heart icon displays
  - [ ] Same styling as Account icon
  - [ ] Badge shows count if items in wishlist
  - [ ] Badge position: top -6px, right -6px
  - [ ] Badge background: #10C99E
  - [ ] Badge text: white, 11px, Jost, weight 600
  - [ ] Badge size: 18x18px min, circular
  - [ ] Hover effect same as Account
  - [ ] Click goes to /wishlist/

- [ ] **Cart Icon**
  - [ ] Cart icon displays
  - [ ] Same styling as Account icon
  - [ ] Badge shows item count
  - [ ] Badge styled same as Wishlist badge
  - [ ] Count updates when items added/removed
  - [ ] Hover effect same as Account
  - [ ] Click goes to /cart/

### Sticky Header vs Main Header Comparison
- [ ] Sticky header appears AFTER main header scrolls out of view
- [ ] Sticky header remains visible while scrolling down
- [ ] Main header hidden when sticky header active
- [ ] All functionality identical between both headers
- [ ] Icons, search, menu all work the same way
- [ ] Sticky header more compact height than main header

---
  - [ ] Border radius: 5px
  - [ ] Padding: 13px
  - [ ] Hover state works (maintains white bg)
  - [ ] Click opens search overlay
  - [ ] Transition smooth (0.2s)

- [ ] **Contact Button** (Green bg #10C99E)
  - [ ] Button displays with mail icon
  - [ ] Icon size: 22x22px
  - [ ] Background: #10C99E
  - [ ] Border: 1px solid #10C99E
  - [ ] Border radius: 5px
  - [ ] Hover state: white bg, green icon
  - [ ] Click opens contact popup
  - [ ] Transition smooth (0.2s)

- [ ] **Hamburger Menu** (Blue bg #253461)
  - [ ] Button displays hamburger icon (☰)
  - [ ] Font size: 22px
  - [ ] Background: #253461
  - [ ] Color: white
  - [ ] Border radius: 5px
  - [ ] Hover state: white bg, blue icon
  - [ ] Click opens mobile menu
  - [ ] Same size as other icon buttons

### Mobile Search Overlay
- [ ] **Overlay Activation**
  - [ ] Opens when search button clicked
  - [ ] Covers full screen (fixed position)
  - [ ] White background
  - [ ] z-index: 200 (above header)
  - [ ] Opacity transition smooth (0.3s)

- [ ] **Close Button**
  - [ ] X icon displays (top right area)
  - [ ] Size: 44x44px
  - [ ] Color: #253461
  - [ ] Hover color: #469ADC
  - [ ] Click closes overlay
  - [ ] ESC key also closes overlay

- [ ] **Search Input**
  - [ ] Input field focuses automatically on open
  - [ ] Placeholder text visible
  - [ ] Typing triggers search
  - [ ] Results dropdown appears below
  - [ ] Max height: calc(100vh - 200px)
  - [ ] Scrollable if many results

### Mobile Menu Drawer
- [ ] **Menu Activation**
  - [ ] Opens when hamburger clicked
  - [ ] Slides in from right side
  - [ ] Full height overlay
  - [ ] Dark backdrop visible
  - [ ] Body scroll locked when open

- [ ] **Menu Structure**
  - [ ] Sports dropdown menu
    - [ ] "SPORTS" header visible
    - [ ] Arrow icon toggles submenu
    - [ ] All sport categories listed
    - [ ] Icons display next to each item
    - [ ] Links work correctly
  - [ ] Products dropdown menu
    - [ ] "PRODUCTS" header visible
    - [ ] Arrow icon toggles submenu
    - [ ] All product categories listed
    - [ ] Icons display next to each item
    - [ ] Links work correctly
  - [ ] Themes dropdown menu
    - [ ] "THEMES" header visible
    - [ ] Arrow icon toggles submenu
    - [ ] All theme categories listed
    - [ ] Icons display next to each item
    - [ ] Links work correctly
  - [ ] Direct links visible
    - [ ] Blog link
    - [ ] About link
    - [ ] Other direct links

- [ ] **Menu Close**
  - [ ] X button closes menu
  - [ ] Click outside closes menu
  - [ ] ESC key closes menu
  - [ ] Transition smooth

---

## 💻 DESKTOP HEADER (> 768px)

### Main Header Row
- [ ] **Container**
  - [ ] Max width: 1280px
  - [ ] Centered on page
  - [ ] Padding: 5px 0
  - [ ] Background: white
  - [ ] Box shadow visible: 0px 0px 10px rgba(0,0,0,0.1)

- [ ] **Logo** (Left side - 70% width area)
  - [ ] Logo displays at 172px width
  - [ ] Logo height auto-scales
  - [ ] 2x retina image loads on high-DPI screens
  - [ ] Logo links to homepage (/)
  - [ ] Logo is sharp and crisp
  - [ ] Padding: 10px 10px 10px 0

- [ ] **Search Bar** (Center - 708px)
  - [ ] Width: 708px fixed
  - [ ] Margin-left: 144px from logo
  - [ ] Height: 44px
  - [ ] Border: 1px solid #253461
  - [ ] Border radius: 15px
  - [ ] Padding: 12px 45px 12px 18px
  - [ ] Placeholder text: "Search products..."
  - [ ] Placeholder color: #253461
  - [ ] Font size: 16px
  - [ ] Search icon visible (right side)
  - [ ] Typing triggers instant search
  - [ ] Results dropdown appears below
  - [ ] Dropdown styled correctly
  - [ ] Product images load in results
  - [ ] Product prices display
  - [ ] Click product navigates to product page
  - [ ] Clear button works (if text entered)

- [ ] **Header Icons** (Right side)
  - [ ] Icons aligned right with margin-left: auto
  - [ ] Gap between icons: 10px
  - [ ] All icons same size: 44x44px

  - [ ] **Account Icon**
    - [ ] User/account icon displays
    - [ ] Border: 1px solid #253461
    - [ ] Border radius: 15px
    - [ ] Icon size: 22x22px
    - [ ] Color: #253461
    - [ ] Hover: border color #469ADC
    - [ ] Hover: icon color #469ADC
    - [ ] Click behavior:
      - [ ] If logged out: links to /my-account/
      - [ ] If logged in: shows account dropdown (if implemented)
    - [ ] Transition smooth (0.3s)

  - [ ] **Wishlist Icon**
    - [ ] Heart icon displays
    - [ ] Border: 1px solid #253461
    - [ ] Border radius: 15px
    - [ ] Icon size: 22x22px
    - [ ] Color: #253461
    - [ ] Badge shows count if items in wishlist
    - [ ] Badge position: top-right (-6px, -6px)
    - [ ] Badge bg: #10C99E
    - [ ] Badge text: white, 11px, Jost font
    - [ ] Hover: border color #469ADC
    - [ ] Hover: icon color #469ADC
    - [ ] Click links to /wishlist/
    - [ ] Transition smooth (0.3s)

  - [ ] **Cart Icon**
    - [ ] Shopping cart icon displays
    - [ ] Border: 1px solid #253461
    - [ ] Border radius: 15px
    - [ ] Icon size: 22x22px
    - [ ] Color: #253461
    - [ ] Badge shows item count
    - [ ] Badge position: top-right (-6px, -6px)
    - [ ] Badge bg: #10C99E
    - [ ] Badge text: white, 11px, Jost font, weight 600
    - [ ] Badge min-width: 18px, height: 18px
    - [ ] Badge border-radius: 9px (circular)
    - [ ] Hover: border color #469ADC
    - [ ] Hover: icon color #469ADC
    - [ ] Click links to /cart/
    - [ ] Transition smooth (0.3s)

### Navigation Row
- [ ] **Container**
  - [ ] Max width: 1280px
  - [ ] Centered on page
  - [ ] Background: white
  - [ ] Padding-bottom: 3px
  - [ ] Display: flex with space-between

- [ ] **Navigation Menu** (Left side)
  - [ ] First nav item aligned with header content above (padding-left: 0)
  - [ ] Items display: flex with gap: 0

  - [ ] **SPORTS Dropdown**
    - [ ] Button text: "SPORTS"
    - [ ] Font: Jost, 15px, weight 500
    - [ ] Color: #00aeef (cyan blue)
    - [ ] Text transform: uppercase
    - [ ] Padding: 14px 22px
    - [ ] Arrow icon visible (14x14px)
    - [ ] Arrow color: #253461
    - [ ] Hover: text color #253461
    - [ ] Hover: arrow rotates 180deg
    - [ ] Hover: arrow color #00aeef
    - [ ] Transition smooth (0.2s)
    - [ ] **Mega Menu Dropdown**
      - [ ] Opens on hover
      - [ ] Full width (100vw)
      - [ ] Position: fixed, top: auto
      - [ ] Background: white
      - [ ] Border-top: 1px solid #FAFAFA
      - [ ] Box shadow: 0px 23px 4px -21px rgba(0,0,0,0.18)
      - [ ] Opacity transition: 0 to 1 (0.3s)
      - [ ] Transform: translateY(-10px) to translateY(0)
      - [ ] Inner max-width: 1280px, centered
      - [ ] Grid layout: 3 columns x 4 rows (auto flow column)
      - [ ] Gap: 0 20px
      - [ ] All sport items listed with icons
      - [ ] Icon size: 17x17px
      - [ ] Icon margin-right: ~4px
      - [ ] Item height: 39px
      - [ ] Item padding: 0 15px
      - [ ] Font: Jost, 16px, weight 400
      - [ ] Color: #253461
      - [ ] Hover: background #F5F5F5
      - [ ] Hover: color #469ADC
      - [ ] Links work correctly
      - [ ] Stays open while hovering menu

  - [ ] **PRODUCTS Dropdown**
    - [ ] Same structure as SPORTS
    - [ ] Button text: "PRODUCTS"
    - [ ] All styling matches SPORTS
    - [ ] Mega menu with product categories
    - [ ] Icons display correctly
    - [ ] Links work

  - [ ] **THEMES Dropdown**
    - [ ] Same structure as SPORTS
    - [ ] Button text: "THEMES"
    - [ ] All styling matches SPORTS
    - [ ] Mega menu with theme categories
    - [ ] Icons display correctly
    - [ ] Links work

  - [ ] **Direct Links** (No dropdown)
    - [ ] Blog link
    - [ ] About link (if present)
    - [ ] Other direct links
    - [ ] Font: Jost, 15px, weight 500
    - [ ] Color: #253461 (dark blue, different from dropdown headers)
    - [ ] Text transform: uppercase
    - [ ] Padding: 14px 25px
    - [ ] Hover: color #00aeef
    - [ ] Transition smooth (0.3s)

- [ ] **Contact Button** (Right side)
  - [ ] Button text: "CONTACT"
  - [ ] Background: #10C99E (green)
  - [ ] Color: white
  - [ ] Font: Jost, 15px, weight 500
  - [ ] Text transform: uppercase
  - [ ] Padding: 10px 30px
  - [ ] Border: 1px solid #10C99E
  - [ ] Border radius: 50px
  - [ ] Hover: background #0eb38c (darker green)
  - [ ] Hover: border-color #0eb38c
  - [ ] Transition: 0.25s ease-in-out
  - [ ] Click opens contact popup
  - [ ] Cursor: pointer

---

## 📌 STICKY HEADER (Appears on scroll)

### Desktop Sticky Header
- [ ] **Activation**
  - [ ] Appears when scrolling down ~100px
  - [ ] Transition smooth (slide down)
  - [ ] Position: sticky, top: 0
  - [ ] z-index higher than main header
  - [ ] Background: white with shadow

- [ ] **Content**
  - [ ] Logo visible (smaller size)
  - [ ] Search bar visible
  - [ ] Account/Wishlist/Cart icons visible
  - [ ] Navigation menu visible
  - [ ] All functionality same as main header
  - [ ] Compact height compared to main header

- [ ] **Deactivation**
  - [ ] Hides when scrolling to top
  - [ ] Or scrolling up (optional behavior)
  - [ ] Transition smooth (slide up)

### Mobile Sticky Header
- [ ] Mobile header is already sticky by default
- [ ] Remains visible at top during scroll
- [ ] No additional sticky behavior needed

---

## 🏠 HOMEPAGE SECTIONS

### Hero Slider Section
- [ ] **Slider Container**
  - [ ] Full width display
  - [ ] Height appropriate for content
  - [ ] Centered on page

- [ ] **Slides**
  - [ ] Multiple slides load
  - [ ] Images display correctly (WebP format)
  - [ ] Images are high quality (not pixelated)
  - [ ] Text overlays readable
  - [ ] CTA buttons visible and styled
  - [ ] CTA buttons clickable

- [ ] **Navigation**
  - [ ] Previous arrow works
  - [ ] Next arrow works
  - [ ] Arrow hover states work
  - [ ] Arrows positioned correctly

- [ ] **Pagination Dots**
  - [ ] Dots display below slider
  - [ ] Active dot highlighted
  - [ ] Click dot changes slide
  - [ ] Dot count matches slide count

- [ ] **Autoplay**
  - [ ] Slider auto-advances (if enabled)
  - [ ] Timing: ~6 seconds per slide
  - [ ] Pauses on hover (if implemented)
  - [ ] Resumes after hover out

- [ ] **Responsive**
  - [ ] Desktop: full size images
  - [ ] Tablet: scaled appropriately
  - [ ] Mobile: mobile-optimized images
  - [ ] Text remains readable on all sizes

### Top Performer in the Spotlight
- [ ] **Section Heading**
  - [ ] Heading visible: "Top Performer in the Spotlight" (or similar)
  - [ ] Font: Jost, ~35px, weight 600
  - [ ] Color: #253461
  - [ ] Text transform: uppercase
  - [ ] Centered
  - [ ] Highlight word in #469ADC color

- [ ] **Product Cards** (Grid layout)
  - [ ] 4 products display (or configured amount)
  - [ ] Grid responsive (4 cols → 2 cols → 1 col)
  - [ ] Equal spacing between cards
  - [ ] Cards aligned properly

- [ ] **Individual Product Card**
  - [ ] **Image**
    - [ ] Product image loads
    - [ ] Image aspect ratio correct
    - [ ] Hover: slight zoom effect (if enabled)
    - [ ] Click image: go to product page
    - [ ] Loading lazy (performance)

  - [ ] **Product Name**
    - [ ] Name displays below image
    - [ ] Font: appropriate size
    - [ ] Color: #253461
    - [ ] Truncates if too long (ellipsis)
    - [ ] Click name: go to product page

  - [ ] **Price**
    - [ ] Price displays (£ symbol)
    - [ ] Format: £XX.XX
    - [ ] Color appropriate
    - [ ] Sale price styling (if on sale)
    - [ ] Original price strikethrough (if on sale)

  - [ ] **"View Product" Button**
    - [ ] Button visible
    - [ ] Styled consistently
    - [ ] Hover state works
    - [ ] Click goes to product page

### Why Choose Us Section
- [ ] **Section Heading**
  - [ ] Heading displays
  - [ ] Styled with highlight word
  - [ ] Centered

- [ ] **Feature Blocks** (Grid or Flex)
  - [ ] Multiple features displayed (3-4 typically)
  - [ ] Icons/images display
  - [ ] Icon size appropriate (~60-80px)
  - [ ] Titles display
  - [ ] Descriptions display
  - [ ] All content readable
  - [ ] Responsive layout (grid → stack on mobile)

### Design Service Section
- [ ] **Section Layout**
  - [ ] Text content on one side
  - [ ] Image/graphic on other side
  - [ ] Or full-width background image

- [ ] **Content**
  - [ ] Heading displays
  - [ ] Description text readable
  - [ ] CTA button visible
  - [ ] Button styled correctly
  - [ ] Button links to correct page
  - [ ] Button hover works

- [ ] **Image**
  - [ ] Image loads correctly
  - [ ] High quality (not pixelated)
  - [ ] Proper aspect ratio
  - [ ] Responsive (scales on mobile)

### Hercules Merchandise About Section
- [ ] **Content**
  - [ ] About text displays
  - [ ] Text readable and formatted
  - [ ] Brand message clear
  - [ ] Links in text work (if any)

- [ ] **Visual Elements**
  - [ ] Background color/image appropriate
  - [ ] Icons or graphics display
  - [ ] Layout balanced

### They Trust Us - Trust Logos
- [ ] **Section Heading**
  - [ ] Heading displays: "They Trust Us" or similar
  - [ ] Styled consistently

- [ ] **Logo Grid**
  - [ ] Multiple client logos display (10-20+)
  - [ ] Logos grayscale (matching typical trust section style)
  - [ ] Grid layout responsive
  - [ ] Equal spacing between logos
  - [ ] Logos same height (varied width OK)
  - [ ] All logos load (no broken images)
  - [ ] Logos fade in on scroll (if animated)

### What Customers Say About Us - Customer Reviews
- [ ] **Section Heading**
  - [ ] Heading: "What Customers Say About Us"
  - [ ] "Say About Us" in blue (#469ADC)
  - [ ] Font: Jost, 35px (desktop), 28px (mobile)
  - [ ] Centered

- [ ] **TrustIndex Widget** ⭐ CRITICAL - Recently Fixed
  - [ ] Widget container loads
  - [ ] Pre-rendered template visible in HTML
  - [ ] **Rating Display**
    - [ ] Shows "EXCELLENT" text
    - [ ] 5 gold stars display
    - [ ] Star size appropriate
  - [ ] **Review Count** ⭐ KEY FIX
    - [ ] Text "Based on" displays
    - [ ] Number "135" displays (or current count)
    - [ ] Text "reviews" displays after number
    - [ ] Full phrase: "Based on 135 reviews"
    - [ ] Styling: number in `<strong>` tag
  - [ ] **Google Logo**
    - [ ] Google logo displays (NOT TrustIndex logo)
    - [ ] Logo replaced via JavaScript
    - [ ] Logo size: 150x25px
    - [ ] Color logo (not grayscale)
  - [ ] **Individual Reviews**
    - [ ] Review cards display in slider
    - [ ] Profile images load
    - [ ] Reviewer names display
    - [ ] Star ratings show for each review
    - [ ] Review text displays
    - [ ] "Read more" link works (if long text)
    - [ ] Verified badge shows
    - [ ] Tooltip on verified badge works
  - [ ] **Slider Controls**
    - [ ] Next arrow button visible
    - [ ] Previous arrow button visible
    - [ ] Arrows functional (click changes review)
    - [ ] Arrows styled correctly
    - [ ] Auto-play works (6 second timeout)
    - [ ] Pauses on hover (if implemented)
  - [ ] **Pagination Dots**
    - [ ] Dots display below reviews
    - [ ] Active dot highlighted
    - [ ] Click dot changes review
  - [ ] **TrustIndex Branding**
    - [ ] "Powered by TrustIndex" link hidden
    - [ ] CSS rule working: display: none !important
  - [ ] **Responsive**
    - [ ] Desktop: full width widget
    - [ ] Tablet: scaled appropriately
    - [ ] Mobile: stacked layout
    - [ ] All text readable on mobile

---

## 📄 STATIC PAGES

### About Page (/about/)
- [ ] Page loads without errors
- [ ] Heading displays
- [ ] Content readable
- [ ] Images load (if present)
- [ ] Layout proper
- [ ] Links in content work
- [ ] CTA buttons work
- [ ] Responsive on mobile

### Blog Index (/blogs/uk)
- [ ] Page loads
- [ ] Blog post list displays
- [ ] Post thumbnails load
- [ ] Post titles display
- [ ] Post excerpts display
- [ ] Post dates display
- [ ] "Read more" links work
- [ ] Pagination works (if multi-page)
- [ ] Categories/tags display (if present)
- [ ] Search blog works (if present)
- [ ] Responsive

### Blog Post Page (/blogs/uk/[slug])
- [ ] Post loads
- [ ] Title displays
- [ ] Date/author info displays
- [ ] Featured image loads
- [ ] Post content formatted properly
- [ ] Images in content load
- [ ] Headings styled correctly
- [ ] Lists formatted properly
- [ ] Blockquotes styled (if used)
- [ ] Code blocks styled (if used)
- [ ] Links in content work
- [ ] Share buttons work (if present)
- [ ] Related posts display (if present)
- [ ] Comments section (if enabled)
- [ ] Responsive

### Contact Us (/contact-us/)
- [ ] Page loads
- [ ] Contact form displays
- [ ] All form fields present:
  - [ ] Name field
  - [ ] Email field
  - [ ] Phone field (if present)
  - [ ] Subject field (if present)
  - [ ] Message field
- [ ] Field labels display
- [ ] Placeholders visible
- [ ] Required fields marked
- [ ] Submit button styled
- [ ] Form validation works
- [ ] Submit sends email/message
- [ ] Success message displays
- [ ] Error messages display (if validation fails)
- [ ] Contact info displayed (address, phone, email)
- [ ] Map embedded (if present)
- [ ] Responsive

### Delivery & Returns (/deliveries-and-returns/)
- [ ] Page loads
- [ ] Heading displays
- [ ] Content readable
- [ ] Tables formatted properly (if present)
- [ ] Lists formatted properly
- [ ] Links work
- [ ] Responsive

### Payment Methods (/payment-methods/)
- [ ] Page loads
- [ ] Payment method logos display
- [ ] Content explains payment options
- [ ] Links to payment processors work (if external)
- [ ] Responsive

### Legal Pages
- [ ] **Legal Notice** (/legal-notice/)
  - [ ] Page loads
  - [ ] All legal content displays
  - [ ] Sections organized
  - [ ] Links work
  - [ ] Responsive

- [ ] **Privacy Policy** (/privacy-policy/)
  - [ ] Page loads
  - [ ] All sections display
  - [ ] GDPR compliance info present
  - [ ] Cookie policy mentioned
  - [ ] Contact info for data requests
  - [ ] Links work
  - [ ] Responsive

- [ ] **Terms of Service** (/terms-of-service/)
  - [ ] Page loads
  - [ ] All terms display
  - [ ] Sections numbered/organized
  - [ ] Legal language clear
  - [ ] Links work
  - [ ] Responsive

### Design Attribution (/design-by-perelweb)
- [ ] Page loads (if present)
- [ ] Designer credit displays
- [ ] Links work
- [ ] Responsive

### Wishlist Page (/wishlist/)
- [ ] Page loads
- [ ] **Empty State**
  - [ ] If no items: "Your wishlist is empty" message
  - [ ] CTA button to browse products
- [ ] **With Items**
  - [ ] All wishlist items display
  - [ ] Product images load
  - [ ] Product names display
  - [ ] Prices display
  - [ ] "Remove from wishlist" button works
  - [ ] "Add to cart" button works
  - [ ] Item count updates when items removed
  - [ ] Grid/list view responsive
- [ ] Wishlist persists (localStorage or account-based)
- [ ] Responsive

---

## 🗂️ COLLECTION PAGES (/collections/[slug])

### Page Structure
- [ ] Page loads for all collections
- [ ] Heading displays collection name
- [ ] Breadcrumb navigation (if present)
- [ ] Product count displays ("Showing X products")

### Filter/Sort Sidebar
- [ ] **Filters** (if present)
  - [ ] Price range filter
  - [ ] Category filters
  - [ ] Color filters (if applicable)
  - [ ] Size filters (if applicable)
  - [ ] Other attribute filters
  - [ ] "Apply" button works
  - [ ] "Clear filters" button works
  - [ ] Active filters display
  - [ ] Filter count updates product grid

- [ ] **Sort Dropdown**
  - [ ] Dropdown displays
  - [ ] Sort options:
    - [ ] Default sorting
    - [ ] Price: Low to High
    - [ ] Price: High to Low
    - [ ] Newest
    - [ ] Best Selling (if applicable)
  - [ ] Selecting option re-sorts products
  - [ ] URL updates with sort parameter

### Product Grid
- [ ] Products display in grid
- [ ] Grid responsive (4 cols → 3 → 2 → 1)
- [ ] Equal card heights (or consistent)
- [ ] All product cards functional (see Product Card checklist below)

### Pagination
- [ ] Pagination controls display (if multi-page)
- [ ] Page numbers visible
- [ ] "Previous" button works
- [ ] "Next" button works
- [ ] Click page number loads that page
- [ ] Current page highlighted
- [ ] Disabled states styled correctly
- [ ] URL updates with page parameter

### Specific Collections to Test
- [ ] /collections/custom-scarves/
- [ ] /collections/custom-beanies/
- [ ] /collections/football/
- [ ] /collections/rugby/
- [ ] /collections/basketball/
- [ ] /collections/accessories/
- [ ] /collections/made-in-europe/
- [ ] /collections/sustainable/
- [ ] (Test at least 5-10 different collections)

---

## 🛍️ PRODUCT PAGE (/products/[slug])

### Product Images
- [ ] **Main Image**
  - [ ] Large image displays
  - [ ] High quality (not pixelated)
  - [ ] Correct aspect ratio
  - [ ] Zoom on hover/click (if enabled)
  - [ ] Lightbox opens (if enabled)

- [ ] **Image Gallery**
  - [ ] Thumbnail images display below/side
  - [ ] Click thumbnail changes main image
  - [ ] Active thumbnail highlighted
  - [ ] Arrows scroll through thumbnails (if many)
  - [ ] All images load without errors

### Product Information
- [ ] **Product Name**
  - [ ] Displays prominently
  - [ ] Font: large, bold
  - [ ] Color: #253461

- [ ] **Price**
  - [ ] Regular price displays: £XX.XX
  - [ ] Sale price (if on sale): styled differently
  - [ ] Original price strikethrough (if on sale)
  - [ ] "Save X%" badge (if on sale)

- [ ] **Product Description**
  - [ ] Full description displays
  - [ ] Formatted properly (paragraphs, lists)
  - [ ] Read more/less toggle (if long text)
  - [ ] HTML content renders correctly
  - [ ] Links in description work

- [ ] **Product Attributes**
  - [ ] SKU displays (if applicable)
  - [ ] Stock status: "In stock" or "Out of stock"
  - [ ] Categories/tags display
  - [ ] Custom attributes display

### Product Configurator ⚙️
- [ ] **Configuration Options**
  - [ ] Size dropdown (if applicable)
    - [ ] All sizes listed
    - [ ] Out of stock sizes disabled
    - [ ] Price updates based on size (if variable)
  - [ ] Color swatches (if applicable)
    - [ ] Color options display
    - [ ] Click color changes selection
    - [ ] Selected color highlighted
    - [ ] Product image changes (if color-specific images)
  - [ ] Quantity selector
    - [ ] Plus button increases quantity
    - [ ] Minus button decreases quantity
    - [ ] Can't go below 1
    - [ ] Can't exceed max stock (if limited)
    - [ ] Manual input works
  - [ ] Custom text fields (for personalization)
    - [ ] Fields display
    - [ ] Character limit shown (if applicable)
    - [ ] Input validated
  - [ ] File upload (for custom designs)
    - [ ] Upload button works
    - [ ] File name displays after upload
    - [ ] File size limit enforced
    - [ ] Allowed file types enforced
    - [ ] Remove uploaded file button works

- [ ] **Configuration Summary**
  - [ ] Selected options summary displays
  - [ ] Price updates live based on selections
  - [ ] Required fields marked
  - [ ] Error messages if required fields empty

### Add to Cart
- [ ] **Add to Cart Button**
  - [ ] Button displays prominently
  - [ ] Styled: background #10C99E, text white
  - [ ] Hover state works
  - [ ] Click adds product to cart
  - [ ] Button disabled if out of stock
  - [ ] Button disabled if required options not selected
  - [ ] Loading state during add (spinner/text change)
  - [ ] Success feedback after add
    - [ ] Button text changes briefly: "Added!" or checkmark
    - [ ] Cart icon badge count updates
    - [ ] Popup notification (if enabled)

### Wishlist Button
- [ ] Button displays (heart icon)
- [ ] Styled consistently with rest of page
- [ ] Click adds to wishlist
- [ ] Icon fills/changes when in wishlist
- [ ] Tooltip: "Add to wishlist" / "Remove from wishlist"
- [ ] Wishlist count badge updates

### Product Tabs/Accordion
- [ ] **Tabs** (if using tabs)
  - [ ] Tab headers display: Description, Specifications, Reviews, etc.
  - [ ] Click tab switches content
  - [ ] Active tab highlighted
  - [ ] Tab content displays correctly

- [ ] **Accordion** (if using accordion on mobile)
  - [ ] Sections collapsible
  - [ ] Click header expands section
  - [ ] Click again collapses
  - [ ] Icon rotates (arrow)
  - [ ] Content displays when expanded

### Additional Product Info
- [ ] **Specifications Table**
  - [ ] Table displays (if applicable)
  - [ ] Rows styled
  - [ ] Alternating row colors (if used)
  - [ ] Data accurate

- [ ] **Shipping Info**
  - [ ] Delivery time displays
  - [ ] Shipping costs mentioned (or link to info)
  - [ ] Free shipping threshold (if applicable)

- [ ] **Returns Info**
  - [ ] Return policy mentioned
  - [ ] Link to full returns page

### Related Products
- [ ] Section displays: "Related Products" or "You May Also Like"
- [ ] 3-4 products display
- [ ] Product cards functional
- [ ] Carousel/slider works (if used)
- [ ] Links go to correct products
- [ ] Responsive

### Social Sharing
- [ ] Share buttons display (if present)
- [ ] Facebook share works
- [ ] Twitter share works
- [ ] Pinterest share works
- [ ] WhatsApp share works (mobile)
- [ ] Copy link button works
- [ ] Share popup opens correctly

### Badges/Labels
- [ ] "New" badge (if new product)
- [ ] "Sale" badge (if on sale)
- [ ] "Out of Stock" overlay (if out of stock)
- [ ] "Made in Europe" badge (if applicable)
- [ ] "Sustainable" badge (if applicable)
- [ ] Custom badges display correctly

### Responsive
- [ ] Desktop: side-by-side layout (image left, info right)
- [ ] Tablet: adjusted spacing
- [ ] Mobile: stacked (image top, info below)
- [ ] All elements accessible on mobile
- [ ] Touch interactions work (swipe gallery, tap to expand)

---

## 🛒 CART & CHECKOUT (WordPress WooCommerce)

### Cart Page (/cart)
- [ ] Page loads (hosted on WordPress)
- [ ] All cart items display
- [ ] Item images load
- [ ] Item names display
- [ ] Item prices display
- [ ] Quantities display
- [ ] Quantity can be updated (+/-)
- [ ] Remove item button works
- [ ] Subtotal calculates correctly
- [ ] Tax calculates correctly (if applicable)
- [ ] Shipping calculates correctly
- [ ] Total calculates correctly
- [ ] Coupon code field present
- [ ] Apply coupon works
- [ ] Invalid coupon shows error
- [ ] Valid coupon applies discount
- [ ] "Proceed to Checkout" button works
- [ ] "Continue Shopping" link works
- [ ] Empty cart shows empty state
- [ ] Responsive

### Checkout Page (/checkout)
- [ ] Page loads (hosted on WordPress)
- [ ] Order summary displays
- [ ] **Billing Details Form**
  - [ ] All fields present: name, email, address, city, postcode, country, phone
  - [ ] Required fields marked
  - [ ] Validation works
  - [ ] Error messages display clearly
  - [ ] "Ship to different address" checkbox (if present)
- [ ] **Shipping Details Form** (if different)
  - [ ] Form displays when checkbox checked
  - [ ] Same fields as billing
- [ ] **Shipping Methods**
  - [ ] Available methods display
  - [ ] Radio buttons work
  - [ ] Selecting method updates total
  - [ ] Costs display correctly
- [ ] **Payment Methods**
  - [ ] Available methods display (Card, PayPal, etc.)
  - [ ] Radio buttons work
  - [ ] Selected method highlighted
  - [ ] Payment instructions display
  - [ ] Card fields display (if card payment)
  - [ ] PayPal button displays (if PayPal)
- [ ] **Order Review**
  - [ ] Items list displays
  - [ ] Subtotal correct
  - [ ] Shipping cost correct
  - [ ] Tax correct
  - [ ] Total correct
- [ ] **Terms & Conditions**
  - [ ] Checkbox present
  - [ ] Link to terms opens
  - [ ] Can't submit without checking (enforced)
- [ ] **Place Order Button**
  - [ ] Button displays prominently
  - [ ] Styled correctly
  - [ ] Disabled until form valid
  - [ ] Click submits order
  - [ ] Loading state shows during processing
  - [ ] Redirects to thank you page on success
  - [ ] Error message on failure
- [ ] **Security**
  - [ ] SSL/HTTPS active (padlock icon)
  - [ ] Payment fields secured
  - [ ] No console errors
- [ ] Responsive

### Thank You Page (Order Confirmation)
- [ ] Page displays after successful order
- [ ] Order number displays
- [ ] Confirmation message clear
- [ ] Order details summary
- [ ] Customer email confirmed
- [ ] Shipping address confirmed
- [ ] Payment method confirmed
- [ ] Order tracking info (if available)
- [ ] "Continue Shopping" button works

---

## 👤 MY ACCOUNT (/my-account)

### Account Dashboard
- [ ] Page loads (hosted on WordPress)
- [ ] Dashboard displays for logged-in users
- [ ] Login form displays for logged-out users
- [ ] Registration form/link present

### Login
- [ ] Email/username field
- [ ] Password field
- [ ] "Remember me" checkbox
- [ ] "Lost password?" link works
- [ ] Login button works
- [ ] Validation errors display
- [ ] Success redirects to dashboard
- [ ] "Create account" link works

### Registration
- [ ] Email field
- [ ] Password field
- [ ] Confirm password field (if present)
- [ ] Terms checkbox (if required)
- [ ] Register button works
- [ ] Validation works
- [ ] Success creates account and logs in
- [ ] Confirmation email sent

### Account Navigation
- [ ] Dashboard link
- [ ] Orders link
- [ ] Downloads link (if digital products)
- [ ] Addresses link
- [ ] Account details link
- [ ] Logout link

### Orders Page
- [ ] Past orders list
- [ ] Order numbers display
- [ ] Order dates display
- [ ] Order statuses display (Processing, Completed, etc.)
- [ ] Order totals display
- [ ] "View" button for each order works
- [ ] Order detail page displays correctly
- [ ] "Reorder" button works (if present)
- [ ] Pagination works (if many orders)

### Addresses Page
- [ ] Billing address displays
- [ ] Shipping address displays
- [ ] "Edit" buttons work
- [ ] Edit form displays
- [ ] Save updates address
- [ ] "Add address" works (if multiple addresses allowed)

### Account Details
- [ ] Current email displays
- [ ] Name fields display
- [ ] Password change fields display
- [ ] Save button works
- [ ] Validation works
- [ ] Success message displays
- [ ] Changes persist

### Logout
- [ ] Logout link visible
- [ ] Click logs out user
- [ ] Redirects to appropriate page
- [ ] Cart/wishlist handling correct

---

## 🔍 SEARCH FUNCTIONALITY

### Global Product Search
- [ ] Search input in header works (desktop)
- [ ] Search overlay works (mobile)
- [ ] Typing triggers instant search (after 2-3 chars)
- [ ] Results dropdown displays below input
- [ ] Results show:
  - [ ] Product images
  - [ ] Product names
  - [ ] Product prices
  - [ ] Product categories (if shown)
- [ ] Clicking result goes to product page
- [ ] "View all results" link (if present)
- [ ] Number of results shown ("Showing 10 of 50")
- [ ] Scroll works if many results
- [ ] No results message displays correctly
- [ ] Search history (if enabled)
- [ ] Clear search button works
- [ ] ESC key closes dropdown
- [ ] Click outside closes dropdown

### Search Results Page (if separate)
- [ ] Page loads: /search?q=query
- [ ] Search term displayed: "Results for 'query'"
- [ ] Product grid displays results
- [ ] Filters work (same as collection page)
- [ ] Sort works
- [ ] Pagination works
- [ ] No results page displays correctly
- [ ] Spelling suggestions (if enabled)

---

## 📧 CONTACT POPUPS

### Desktop Contact Popup
- [ ] **Trigger Button** (in nav)
  - [ ] Button displays: "CONTACT"
  - [ ] Styled: green background #10C99E
  - [ ] Click opens popup

- [ ] **Popup Modal**
  - [ ] Modal overlays page
  - [ ] Dark backdrop behind modal
  - [ ] Modal centered on screen
  - [ ] White background
  - [ ] Close button (X) visible
  - [ ] Click X closes popup
  - [ ] Click backdrop closes popup
  - [ ] ESC key closes popup

- [ ] **Contact Form in Popup**
  - [ ] Heading displays
  - [ ] Name field
  - [ ] Email field
  - [ ] Phone field (if present)
  - [ ] Subject field
  - [ ] Message field
  - [ ] All fields labeled
  - [ ] Placeholders visible
  - [ ] Required fields marked
  - [ ] Character counts (if applicable)
  - [ ] File upload button (if present)
  - [ ] Submit button styled
  - [ ] Form validation works
  - [ ] Submit sends message
  - [ ] Success message in popup
  - [ ] Error messages display
  - [ ] Loading state during submit

### Mobile Contact Popup
- [ ] **Trigger Button** (mail icon in header)
  - [ ] Icon displays: green background
  - [ ] Click opens popup

- [ ] **Popup Modal**
  - [ ] Full screen on mobile (or bottom sheet)
  - [ ] Close button works
  - [ ] Form displays properly
  - [ ] All form elements functional
  - [ ] Submit works
  - [ ] Keyboard doesn't obstruct input

### Express Delivery Popup
- [ ] Popup displays (if enabled on certain pages/products)
- [ ] Trigger condition correct (e.g., product page only)
- [ ] Modal displays correctly
- [ ] Content explains express delivery
- [ ] Close button works
- [ ] "Don't show again" checkbox (if present)
- [ ] CTA button works

### Quantity Request Popup
- [ ] Popup displays (if bulk order request feature enabled)
- [ ] Trigger button/link works
- [ ] Form displays:
  - [ ] Product field (pre-filled if from product page)
  - [ ] Quantity field
  - [ ] Additional info field
  - [ ] Contact fields
- [ ] Submit works
- [ ] Confirmation displayed
- [ ] Close works

---

## 🍪 COOKIE CONSENT BANNER

### Banner Display
- [ ] Banner displays on first visit
- [ ] Position: bottom of screen (or top)
- [ ] Doesn't obstruct critical content
- [ ] Text readable
- [ ] Styling matches site design

### Banner Content
- [ ] Message explains cookies used
- [ ] Link to Privacy Policy/Cookie Policy
- [ ] Link opens correctly (new tab or same)

### Buttons
- [ ] "Accept" button visible
- [ ] Click "Accept" sets consent cookie
- [ ] Banner hides after acceptance
- [ ] "Decline" button (if present)
- [ ] Click "Decline" sets preference
- [ ] "Customize" button (if present)
- [ ] Customize opens settings modal

### Cookie Settings Modal
- [ ] Modal displays if "Customize" clicked
- [ ] Explains cookie categories:
  - [ ] Essential/Necessary
  - [ ] Analytics
  - [ ] Marketing
  - [ ] Preferences
- [ ] Toggles for each category (except essential)
- [ ] Save preferences button works
- [ ] Preferences persist

### Persistence
- [ ] Consent choice remembered (cookie set)
- [ ] Banner doesn't reappear after consent
- [ ] Banner respects "Decline" choice
- [ ] Consent can be changed (link in footer or settings)

---

## 🦶 FOOTER

### Newsletter Section
- [ ] **Section Background**
  - [ ] Background color or image
  - [ ] Padding/spacing appropriate

- [ ] **Heading**
  - [ ] "Interested?" displays
  - [ ] Font: appropriate size
  - [ ] Color contrasts with background

- [ ] **Newsletter Form**
  - [ ] Email input field
  - [ ] Placeholder: "Subscribe to newsletter"
  - [ ] Field styled consistently
  - [ ] "Send" button visible
  - [ ] Button styled (matches brand)
  - [ ] Form submits via AJAX or POST
  - [ ] Validation works (valid email required)
  - [ ] Success message displays: "Thank you for subscribing!"
  - [ ] Error message displays if fails
  - [ ] Loading state during submission
  - [ ] Form integrates with email service (Mailchimp, Brevo, etc.)

- [ ] **Description Text**
  - [ ] "Be the first to see our latest sports merchandise..."
  - [ ] Text readable
  - [ ] Styled appropriately

### Main Footer Section
- [ ] **Background & Layout**
  - [ ] Background color appropriate
  - [ ] Max width: 1280px, centered
  - [ ] 5 columns on desktop
  - [ ] Stacked on mobile

- [ ] **Logo Column**
  - [ ] Hercules logo displays
  - [ ] Logo links to homepage
  - [ ] Logo size appropriate (176x78px)

- [ ] **About Us Column**
  - [ ] Heading: "About Us"
  - [ ] Links listed:
    - [ ] About Us
    - [ ] Blog
    - [ ] Delivery & Returns
    - [ ] Payment Methods
    - [ ] Contact
    - [ ] My Account
  - [ ] All links work
  - [ ] Hover states work

- [ ] **Sports Column**
  - [ ] Heading: "Sports"
  - [ ] Links listed:
    - [ ] Football
    - [ ] Rugby
    - [ ] Basketball
    - [ ] Running
    - [ ] Cycling
    - [ ] Volleyball
  - [ ] All links work
  - [ ] Hover states work

- [ ] **Products Column**
  - [ ] Heading: "Products"
  - [ ] Links listed:
    - [ ] Scarves
    - [ ] Beanies
    - [ ] Sportswear
    - [ ] Pennants
    - [ ] Accessories
    - [ ] Tifo
  - [ ] All links work
  - [ ] Hover states work

- [ ] **Reseller Column**
  - [ ] Heading: "Reseller or professional club?"
  - [ ] Text: "We offer bulk discounts."
  - [ ] Contact button displays
  - [ ] Button styled consistently
  - [ ] Click opens contact popup
  - [ ] Phone number displays
  - [ ] Phone icon visible (32x32px)
  - [ ] Phone link works (tel: link)
  - [ ] Clicking phone number opens phone app (mobile)

### Footer Badges (if present)
- [ ] Made in Europe badge
- [ ] Sustainable badge
- [ ] Payment method icons
- [ ] Security badges
- [ ] All badges display correctly

### Footer Bottom Bar
- [ ] Copyright text displays: "© 2026 Hercules Merchandise"
- [ ] Legal links display:
  - [ ] Privacy Policy
  - [ ] Terms of Service
  - [ ] Legal Notice
- [ ] Links work
- [ ] "Design by Perelweb" credit (if present)
- [ ] Social media icons (if present)
- [ ] Social links work

### Responsive Footer
- [ ] Desktop: 5 columns side-by-side
- [ ] Tablet: 2-3 columns
- [ ] Mobile: 1 column, stacked
- [ ] All content accessible
- [ ] No horizontal scroll
- [ ] Touch targets sized appropriately

---

## ⚡ PERFORMANCE & TECHNICAL

### Page Load Performance
- [ ] **Homepage**
  - [ ] Loads in under 3 seconds (good connection)
  - [ ] First Contentful Paint (FCP) < 1.8s
  - [ ] Largest Contentful Paint (LCP) < 2.5s
  - [ ] Time to Interactive (TTI) < 3.8s

- [ ] **Product Page**
  - [ ] Loads quickly
  - [ ] Images lazy load (not all at once)
  - [ ] Above-the-fold content prioritized

- [ ] **Collection Page**
  - [ ] Loads reasonably with many products
  - [ ] Pagination keeps page size manageable

### Image Optimization
- [ ] Images use modern formats (WebP, AVIF)
- [ ] Fallback to JPEG/PNG for compatibility
- [ ] Responsive images (srcset) used
- [ ] Images sized appropriately (not oversized)
- [ ] Lazy loading implemented
- [ ] Alt text present on all images
- [ ] No broken image links

### Font Loading
- [ ] Fonts load without Flash of Unstyled Text (FOUT)
- [ ] Font files optimized (WOFF2)
- [ ] Only necessary font weights loaded
- [ ] Font display: swap or optional

### JavaScript
- [ ] No JavaScript errors in console
- [ ] React hydration works correctly
- [ ] Interactive components functional
- [ ] No memory leaks (test long session)

### CSS
- [ ] Styles load correctly
- [ ] No Flash of Unstyled Content (FOUC)
- [ ] Critical CSS inlined (if implemented)
- [ ] CSS file size reasonable
- [ ] No unused CSS (or acceptable amount)

### Caching
- [ ] Static assets cached (long cache headers)
- [ ] HTML cached appropriately (short cache)
- [ ] API responses cached (if applicable)
- [ ] Service worker registered (if PWA)

### SEO Technical
- [ ] robots.txt present and correct
- [ ] Sitemap.xml present and accessible
- [ ] Meta robots tags correct (no accidental noindex)
- [ ] Canonical URLs set correctly
- [ ] Structured data present (JSON-LD)
  - [ ] Organization
  - [ ] Product schema on product pages
  - [ ] Review schema
  - [ ] Breadcrumb schema
- [ ] hreflang tags (if multi-language) - Not applicable for UK-only
- [ ] Social meta tags (Open Graph, Twitter Cards)
- [ ] Title tags unique per page
- [ ] Meta descriptions unique per page
- [ ] Headings hierarchy correct (H1, H2, H3)

### Accessibility (A11y)
- [ ] **Keyboard Navigation**
  - [ ] All interactive elements reachable via Tab
  - [ ] Tab order logical
  - [ ] Enter/Space activate buttons/links
  - [ ] ESC closes modals/dropdowns
  - [ ] Focus indicators visible

- [ ] **Screen Reader**
  - [ ] Alt text on images
  - [ ] ARIA labels on icon buttons
  - [ ] ARIA roles appropriate
  - [ ] Form labels associated with inputs
  - [ ] Error messages announced
  - [ ] Skip to content link (optional but good)

- [ ] **Color Contrast**
  - [ ] Text readable against backgrounds (WCAG AA minimum)
  - [ ] Link colors distinguishable
  - [ ] Button text readable

- [ ] **Forms**
  - [ ] Labels visible or aria-label present
  - [ ] Required fields indicated
  - [ ] Error messages clear and associated
  - [ ] Autocomplete attributes used

### Security
- [ ] HTTPS everywhere (SSL certificate valid)
- [ ] No mixed content warnings
- [ ] Forms use CSRF protection
- [ ] XSS protection (sanitized outputs)
- [ ] Content Security Policy headers (optional but recommended)
- [ ] No sensitive data in URLs
- [ ] Password fields properly masked

### Browser Compatibility
- [ ] **Chrome** (latest)
  - [ ] Site works correctly
  - [ ] No console errors
- [ ] **Firefox** (latest)
  - [ ] Site works correctly
- [ ] **Safari** (latest, macOS)
  - [ ] Site works correctly
- [ ] **Safari** (iOS)
  - [ ] Site works correctly
  - [ ] Touch gestures work
- [ ] **Edge** (latest)
  - [ ] Site works correctly
- [ ] **Older browsers** (last 2 versions)
  - [ ] Graceful degradation or polyfills

### Device Testing
- [ ] **Desktop**
  - [ ] 1920x1080 (Full HD)
  - [ ] 1366x768 (common laptop)
  - [ ] 2560x1440 (2K)
  - [ ] 3840x2160 (4K)

- [ ] **Tablet**
  - [ ] iPad (768x1024)
  - [ ] iPad Pro (1024x1366)
  - [ ] Android tablets (various)

- [ ] **Mobile**
  - [ ] iPhone SE (375x667)
  - [ ] iPhone 12/13 (390x844)
  - [ ] iPhone 14 Pro Max (430x932)
  - [ ] Samsung Galaxy (360x640, 412x915)
  - [ ] Pixel phones

- [ ] **Landscape Mode**
  - [ ] Tablet landscape works
  - [ ] Mobile landscape works

### Network Conditions
- [ ] Fast 3G (test load time)
- [ ] Slow 3G (test load time)
- [ ] Offline (check error handling)

---

## 🐛 COMMON BUG CHECKS

### Layout Issues
- [ ] No horizontal scroll on any page
- [ ] No content cut off at edges
- [ ] Overlapping elements
- [ ] Broken grid layouts
- [ ] Images outside containers
- [ ] Text overflow issues
- [ ] Footer not at bottom (floating footer)
- [ ] Header not fixed properly (jumping)

### Functional Issues
- [ ] Broken links (404 errors)
- [ ] Forms not submitting
- [ ] Buttons not clickable
- [ ] Dropdowns not opening
- [ ] Modals not closing
- [ ] Cart not updating
- [ ] Search not working
- [ ] Filters not applying

### Visual Issues
- [ ] Wrong colors (brand colors incorrect)
- [ ] Wrong fonts (fallback fonts showing)
- [ ] Pixelated images
- [ ] Missing icons (broken icon fonts)
- [ ] Inconsistent spacing
- [ ] Misaligned elements
- [ ] Hover states not working

### Content Issues
- [ ] Lorem ipsum placeholder text
- [ ] [Placeholder] text visible
- [ ] Broken image placeholders
- [ ] Empty sections
- [ ] Duplicate content
- [ ] Spelling/grammar errors
- [ ] Wrong currency symbol
- [ ] Wrong language (if multi-language)

### JavaScript Errors
- [ ] Console errors on page load
- [ ] Console errors on interaction
- [ ] Features not working (JS-dependent)
- [ ] Memory leaks (check Performance tab)
- [ ] Slow animations/transitions

---

## 📊 ANALYTICS & TRACKING

### Google Analytics
- [ ] GA code present on all pages
- [ ] Pageviews tracking
- [ ] Events tracking (button clicks, form submissions)
- [ ] E-commerce tracking (if set up)
  - [ ] Product views
  - [ ] Add to cart
  - [ ] Checkout steps
  - [ ] Purchase completion
- [ ] User ID tracking (if logged-in users)
- [ ] No PII (personally identifiable information) sent

### Google Tag Manager
- [ ] GTM container loads
- [ ] Tags firing correctly
- [ ] Triggers working
- [ ] Variables populating
- [ ] Data Layer pushes (if used)

### Facebook Pixel / Meta Pixel
- [ ] Pixel loads (if using)
- [ ] PageView event fires
- [ ] Custom events fire (AddToCart, Purchase, etc.)
- [ ] No errors in Pixel Helper extension

### Other Tracking
- [ ] Hotjar / heat mapping (if used)
- [ ] Live chat scripts (if used)
- [ ] Email marketing pixels (if used)

---

## 🎨 DESIGN CONSISTENCY

### Colors
- [ ] Primary color (#253461) used consistently
- [ ] Secondary color (#469ADC) used appropriately
- [ ] Accent/CTA color (#10C99E) for buttons
- [ ] Link color (#00AEEF) for navigation
- [ ] Text color consistent (#253461 for headings, etc.)
- [ ] Background colors consistent

### Typography
- [ ] Jost font loads for headings/nav
- [ ] Roboto font loads for body text (if used)
- [ ] Font sizes consistent (35px H2, 15px nav, etc.)
- [ ] Font weights consistent (500, 600, 700)
- [ ] Line heights appropriate
- [ ] Letter spacing correct (if set)

### Spacing
- [ ] Consistent padding in sections (80px, 50px, etc.)
- [ ] Consistent margins
- [ ] Consistent gaps between elements
- [ ] Grid gaps consistent

### Buttons
- [ ] All buttons styled consistently
- [ ] Primary CTA: green (#10C99E), 50px border-radius
- [ ] Secondary: outlined or different color
- [ ] Hover states consistent
- [ ] Disabled states styled
- [ ] Icon buttons: 44x44px, 15px border-radius

### Form Inputs
- [ ] All inputs styled consistently
- [ ] Border: 1px solid #253461
- [ ] Border radius: 15px
- [ ] Padding consistent
- [ ] Focus state (outline or border color change)
- [ ] Error state (red border/message)
- [ ] Success state (green border/checkmark)

### Cards/Boxes
- [ ] Product cards styled consistently
- [ ] Border radius consistent (15-20px typical)
- [ ] Shadows consistent (if used)
- [ ] Padding consistent
- [ ] Hover effects consistent

---

## ✅ FINAL CROSS-CHECKS

### Pre-Launch Checklist
- [ ] All pages reviewed
- [ ] All links tested
- [ ] All forms tested
- [ ] Contact information correct
- [ ] Legal pages reviewed by legal team
- [ ] Prices correct
- [ ] Product information accurate
- [ ] Images all high quality
- [ ] No placeholder content
- [ ] Analytics working
- [ ] SSL certificate installed
- [ ] Domain configured correctly
- [ ] Email sending working (contact forms, order confirmations)
- [ ] Search engines can crawl (robots.txt, sitemap)
- [ ] Social sharing working
- [ ] Backup system in place

### Post-Launch Monitoring
- [ ] Monitor error logs (first 24 hours)
- [ ] Check analytics data coming in
- [ ] Test ordering flow (real transaction)
- [ ] Monitor server performance
- [ ] Check email deliverability
- [ ] Monitor user feedback
- [ ] Check search console for errors

---

## 📝 TESTING NOTES

**Instructions:**
1. Open site in Chrome: https://9a668c27.hercules-uk-staging-e9z.pages.dev/
2. Go through each section methodically
3. Check boxes as you test each item
4. Note any issues found in the section below
5. Retest after fixes applied

### Issues Found

| Issue # | Page/Section | Description | Severity | Status | Notes |
|---------|--------------|-------------|----------|--------|-------|
| 1 | | | High/Med/Low | Open/Fixed | |
| 2 | | | | | |
| 3 | | | | | |

### Browser Testing Results

| Browser | Version | Pass/Fail | Notes |
|---------|---------|-----------|-------|
| Chrome | | | |
| Firefox | | | |
| Safari | | | |
| Edge | | | |

### Device Testing Results

| Device | Screen Size | Pass/Fail | Notes |
|--------|-------------|-----------|-------|
| Desktop | 1920x1080 | | |
| Laptop | 1366x768 | | |
| iPad | 768x1024 | | |
| iPhone 13 | 390x844 | | |
| Galaxy S21 | 412x915 | | |

---

## 🎯 PRIORITY TESTING ORDER

### Phase 1: Critical Functionality (1-2 hours)
1. Homepage loads
2. Product search works
3. Product page loads
4. Add to cart works
5. Cart page works
6. Checkout flow works
7. Contact forms work
8. Mobile header/menu works

### Phase 2: Content & Design (2-3 hours)
9. All static pages load correctly
10. Footer links work
11. Collection pages work
12. Blog pages work
13. Design consistency check
14. Responsive breakpoints

### Phase 3: Advanced Features (1-2 hours)
15. Wishlist functionality
16. Product configurator
17. Filters and sorting
18. Search results
19. User account pages

### Phase 4: Performance & Polish (1-2 hours)
20. Page load speeds
21. Image optimization check
22. Accessibility audit
23. SEO technical check
24. Cross-browser testing
25. Analytics verification

**Total Estimated Time: 5-9 hours for complete QA**

---

**End of Checklist**

Last Updated: 2026-02-09
