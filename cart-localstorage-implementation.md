# Cart localStorage Implementation Guide

**Implemented on Staging:** 2026-01-29
**Status:** Ready for testing on staging.hercules-merchandise.de

---

## Overview

This implementation stores cart data in localStorage permanently, eliminating unnecessary API calls on every page navigation. Cart data is only updated when the cart actually changes (add/remove items).

---

## Files Changed

| File | Action | Description |
|------|--------|-------------|
| `src/lib/cartStore.ts` | **NEW** | localStorage wrapper for cart data |
| `src/lib/sessionManager.ts` | **MODIFIED** | Removed cart logic, keeps user login state only |
| `src/components/UserSession.tsx` | **MODIFIED** | Uses cartStore for cart, sessionManager for user |
| `src/components/ProductConfigurator.tsx` | **MODIFIED** | Updates cartStore on add-to-cart |

---

## Detailed Changes

### 1. New File: `src/lib/cartStore.ts`

```typescript
// Cart Store - Persistent localStorage wrapper for cart data
// Updates only when cart changes (add/remove), no API fetching for display

const CART_KEY = 'hercules_cart';

interface CartItem {
  key: string;
  product_id: number;
  name: string;
  quantity: number;
  price: string;
  line_total: string;
  thumbnail: string | null;
  permalink?: string;
}

interface CartData {
  count: number;
  items: CartItem[];
  subtotal: string;
  total: string;
}

const DEFAULT_CART: CartData = {
  count: 0,
  items: [],
  subtotal: '€0,00',
  total: '€0,00'
};

export const cartStore = {
  get(): CartData {
    if (typeof window === 'undefined') return DEFAULT_CART;
    try {
      const data = localStorage.getItem(CART_KEY);
      return data ? JSON.parse(data) : DEFAULT_CART;
    } catch {
      return DEFAULT_CART;
    }
  },

  set(cart: CartData) {
    if (typeof window === 'undefined') return;
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    window.dispatchEvent(new CustomEvent('hercules:cart-changed'));
  },

  incrementCount(by = 1) {
    const cart = this.get();
    cart.count += by;
    this.set(cart);
  },

  decrementCount(by = 1) {
    const cart = this.get();
    cart.count = Math.max(0, cart.count - by);
    this.set(cart);
  },

  clear() {
    if (typeof window === 'undefined') return;
    localStorage.removeItem(CART_KEY);
    window.dispatchEvent(new CustomEvent('hercules:cart-changed'));
  },

  needsInitialSync(): boolean {
    if (typeof window === 'undefined') return false;
    return !localStorage.getItem(CART_KEY);
  }
};

export type { CartData, CartItem };
```

---

### 2. Changes to `src/lib/sessionManager.ts`

**Before:** Handled both user session AND cart data with 10-second cache

**After:**
- Only handles user login state (logged_in, user info)
- Cache extended to 5 minutes (user state rarely changes)
- Removed `updateCart()` and `updateSession()` methods
- Changed cache key from `hercules_session` to `hercules_user_session`

**Key changes:**
```typescript
// Changed cache duration
const CACHE_DURATION = 300000; // 5 minutes (was 10 seconds)

// Changed cache key
const CACHE_KEY = 'hercules_user_session'; // was 'hercules_session'

// Removed methods:
// - updateCart()
// - updateSession()
```

---

### 3. Changes to `src/components/UserSession.tsx`

**Key changes:**

1. **Imports:**
```typescript
import { cartStore, type CartData, type CartItem } from '../lib/cartStore';
```

2. **State separation:**
```typescript
// Cart state from localStorage (persistent)
const [cart, setCart] = useState<CartData>(cartStore.get());
const [cartLoading, setCartLoading] = useState(false);

// User session state (for account type only)
const [session, setSession] = useState<SessionData | null>(null);
const [sessionLoading, setSessionLoading] = useState(true);
```

3. **Cart listeners:**
```typescript
// Listen for cart changes from cartStore
const handleCartChange = () => {
  setCart(cartStore.get());
};

// Listen for localStorage changes from other tabs
const handleStorageChange = (e: StorageEvent) => {
  if (e.key === 'hercules_cart') {
    setCart(cartStore.get());
  }
};

window.addEventListener('hercules:cart-changed', handleCartChange);
window.addEventListener('storage', handleStorageChange);
```

4. **Initial sync on first visit:**
```typescript
if (cartStore.needsInitialSync()) {
  setCartLoading(true);
  fetch(`${baseUrl}/wp-json/hercules/v1/session`, { credentials: 'include' })
    .then(r => r.json())
    .then(data => {
      if (data.cart) {
        cartStore.set(data.cart);
        setCart(data.cart);
      }
    })
    .finally(() => setCartLoading(false));
}
```

5. **Remove from cart uses cartStore:**
```typescript
if (data.success && data.cart) {
  cartStore.set(data.cart); // Instead of sessionManager.updateCart()
}
```

6. **Rendering uses cart state:**
```typescript
const count = cart.count;        // Instead of session?.cart?.count
const items = cart.items;        // Instead of session?.cart?.items
const subtotal = cart.subtotal;  // Instead of session?.cart?.subtotal
```

---

### 4. Changes to `src/components/ProductConfigurator.tsx`

**Key changes:**

1. **Import:**
```typescript
import { cartStore } from '../lib/cartStore';
```

2. **On successful add-to-cart:**
```typescript
if (result.success) {
  // Update cartStore with new cart data (persists to localStorage)
  if (result.cart) {
    cartStore.set(result.cart);
  } else {
    // Fallback: increment count by quantity added
    cartStore.incrementCount(quantitySelected);
  }
  // ... redirect logic
}
```

---

## How It Works

```
┌─────────────────────────────────────────────────────────────────┐
│                         localStorage                             │
│                      hercules_cart                               │
│  { count: 3, items: [...], subtotal: "€150,00", total: "..." }  │
└─────────────────────────────────────────────────────────────────┘
        ▲                           │
        │ WRITE                     │ READ
        │                           ▼
┌───────┴───────┐           ┌───────────────────┐
│ Cart Actions  │           │ All Pages         │
│ - Add to cart │           │ - Header cart icon│
│ - Remove item │           │ - Mini cart       │
│ - Checkout    │           │ - Badge count     │
└───────────────┘           └───────────────────┘
```

---

## Update Points

| Action | File | What Happens |
|--------|------|--------------|
| **Add to cart** | `ProductConfigurator.tsx` | `cartStore.set(result.cart)` |
| **Remove from cart** | `UserSession.tsx` | `cartStore.set(data.cart)` |
| **First visit** | `UserSession.tsx` | Fetch from API, save to localStorage |
| **Visit cart page** | WordPress `/warenkorb/` | Fresh data from server |

---

## Benefits

| Benefit | Description |
|---------|-------------|
| **Zero API calls** | No fetching just to display cart count |
| **Instant updates** | Cart badge updates immediately on add/remove |
| **Works offline** | Shows last known cart state |
| **Survives refresh** | localStorage persists across sessions |
| **Multi-tab sync** | `storage` event syncs across browser tabs |
| **Simple code** | Just localStorage + custom events |

---

## Testing Checklist

- [ ] Add item to cart → badge updates instantly
- [ ] Remove item from mini cart → badge updates
- [ ] Navigate between pages → count persists (no flicker)
- [ ] Open new tab → cart count matches
- [ ] Refresh page → cart count persists
- [ ] Clear cookies → cart data remains (localStorage)
- [ ] Visit /warenkorb/ → shows accurate server data
- [ ] First visit (incognito) → fetches from API once, then persists

---

## Edge Cases Handled

| Case | Solution |
|------|----------|
| First visit (no data) | Fetch once from API, store in localStorage |
| Cart modified on WordPress | Fresh data loaded on `/warenkorb/` page |
| Multiple browser tabs | `storage` event automatically syncs |
| User logs out | Call `cartStore.clear()` on logout |
| localStorage disabled | Fallback to default empty cart |

---

## Deployment to Live Site

When ready to deploy to `hercules-de-live`:

1. **Option A: Deploy same codebase**
   ```bash
   cd astro-hercules
   npm run build
   CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
   CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
   npx wrangler pages deploy dist/ --project-name=hercules-de-live
   ```

2. **Option B: Create separate workflow**
   - Add a new GitHub Actions workflow for `hercules-de-live`
   - Trigger manually when ready to go live

---

## Rollback

If issues occur, revert to the previous sessionManager-based approach:

1. Restore `sessionManager.ts` to include `updateCart()` method
2. Remove `cartStore.ts`
3. Revert `UserSession.tsx` to use `sessionManager` for cart
4. Revert `ProductConfigurator.tsx` to dispatch `hercules:cart-updated` event

---

*Document created: 2026-01-29*
