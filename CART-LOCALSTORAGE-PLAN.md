# Cart localStorage Plan

## Overview

Store cart data in localStorage permanently and update only when cart changes (add/remove), without fetching from API each time.

---

## Current Behavior (Problem)

- `sessionManager.ts` fetches `/wp-json/hercules/v1/session` on every page load
- Caches in localStorage for only 10 seconds
- After cache expires, refetches on next page navigation
- Unnecessary API calls just to display cart count

---

## Proposed Behavior

- Store cart data in localStorage **permanently**
- Update localStorage **only** when cart changes (add/remove)
- Never fetch session API just for cart count
- Fetch fresh data only on cart/checkout pages (where accuracy matters)

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         localStorage                             │
│                    hercules_cart_data                            │
│  { count: 3, items: [...], subtotal: "€150,00", updated: ... }  │
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

## Update Points (Only 3 Places)

| Action | File | What Happens |
|--------|------|--------------|
| **Add to cart** | `ProductConfigurator.tsx` | After successful add → update localStorage with new count/items |
| **Remove from cart** | `UserSession.tsx` | After successful remove → API returns new cart → save to localStorage |
| **Visit cart page** | WordPress `/warenkorb/` | Fresh data from server (handled by WordPress) |

---

## Implementation

### 1. New `cartStore.ts` (Simple localStorage wrapper)

Create new file: `src/lib/cartStore.ts`

```typescript
// src/lib/cartStore.ts
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

  // Check if we need initial sync (first visit)
  needsInitialSync(): boolean {
    if (typeof window === 'undefined') return false;
    return !localStorage.getItem(CART_KEY);
  }
};

export type { CartData, CartItem };
```

### 2. Update `ProductConfigurator.tsx`

After successful add-to-cart API call:

```typescript
import { cartStore } from '../lib/cartStore';

// Inside the add-to-cart success handler:
const handleAddToCart = async () => {
  // ... existing add to cart logic ...

  const response = await fetch('/wp-json/pearl/v1/add-to-cart', {
    // ... request config
  });

  if (response.ok) {
    const data = await response.json();

    // Update localStorage with new count
    cartStore.incrementCount(quantity);

    // Or if API returns full cart data:
    // cartStore.set({
    //   count: data.cart_count,
    //   items: data.cart_items,
    //   subtotal: data.subtotal,
    //   total: data.total
    // });
  }
};
```

### 3. Update `UserSession.tsx`

Change to read from localStorage instead of sessionManager:

```typescript
import { useState, useEffect, useRef } from 'react';
import { cartStore, type CartData } from '../lib/cartStore';

export default function UserSession({ type }: UserSessionProps) {
  const [cart, setCart] = useState<CartData>(cartStore.get());
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Listen for cart changes (from any component)
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

    // Initial sync if first visit
    if (cartStore.needsInitialSync()) {
      setLoading(true);
      fetch('/wp-json/hercules/v1/session', { credentials: 'include' })
        .then(r => r.json())
        .then(data => {
          cartStore.set(data.cart);
          setCart(data.cart);
        })
        .catch(console.error)
        .finally(() => setLoading(false));
    }

    return () => {
      window.removeEventListener('hercules:cart-changed', handleCartChange);
      window.removeEventListener('storage', handleStorageChange);
    };
  }, []);

  // Remove item from cart
  const removeFromCart = async (cartItemKey: string) => {
    // ... existing remove logic ...

    const response = await fetch(`${baseUrl}/wp-json/hercules/v1/cart/remove`, {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ key: cartItemKey }),
    });

    if (response.ok) {
      const data = await response.json();
      if (data.success && data.cart) {
        // Save updated cart to localStorage
        cartStore.set(data.cart);
      }
    }
  };

  // Rest of component uses `cart` state instead of `session?.cart`
  const count = cart.count;
  const items = cart.items;
  const subtotal = cart.subtotal;

  // ... rest of render logic
}
```

### 4. Simplify `sessionManager.ts`

Keep only user session logic, remove cart fetching:

```typescript
// src/lib/sessionManager.ts
// Remove cart-related code, keep only:
// - User login state
// - User info (name, email, avatar)
// - Account-related session data
```

---

## File Changes Summary

| File | Action |
|------|--------|
| `src/lib/cartStore.ts` | **CREATE** - New localStorage wrapper |
| `src/lib/sessionManager.ts` | **MODIFY** - Remove cart logic, keep user session only |
| `src/components/UserSession.tsx` | **MODIFY** - Use cartStore instead of sessionManager for cart |
| `src/components/ProductConfigurator.tsx` | **MODIFY** - Call cartStore.incrementCount() on add |

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

## Edge Cases

| Case | Solution |
|------|----------|
| First visit (no data) | Fetch once from API, store in localStorage |
| Cart modified on WordPress | Fresh data loaded on `/warenkorb/` page (WordPress handles) |
| Multiple browser tabs | `storage` event automatically syncs |
| User logs out | Call `cartStore.clear()` on logout |
| localStorage disabled | Fallback to default empty cart |

---

## Testing Checklist

- [ ] Add item to cart → badge updates instantly
- [ ] Remove item from mini cart → badge updates
- [ ] Navigate between pages → count persists (no flicker)
- [ ] Open new tab → cart count matches
- [ ] Refresh page → cart count persists
- [ ] Clear cookies → cart data remains (localStorage)
- [ ] Visit /warenkorb/ → shows accurate server data

---

## Future Enhancements

1. **Stale data indicator** - Show "refreshing..." if data older than 1 hour
2. **Background sync** - Optionally sync with server every 5 minutes
3. **Conflict resolution** - Handle server/local mismatch on cart page

---

*Created: 2026-01-29*
