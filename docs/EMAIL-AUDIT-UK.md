# Hercules UK - Email Audit & Test Checklist

> **Date:** 2026-02-10
> **Sites:** Staging (staging.hercules-merchandise.co.uk) + Astro (localhost:4321)
> **Brevo Account:** gilles@hercules-merchandise.com
> **Brevo API Key:** xkeysib-457d...ZGF1 (set on both Worker + wp-config.php)

---

## Current Email Configuration

### Sender Settings

| Setting | Live Site | Staging Site | Correct Value |
|---------|-----------|-------------|---------------|
| WC From Email (DB) | `info@hercules-merchandise.com` | `info@hercules-merchandise.com` | `info@hercules-merchandise.co.uk` |
| WC From Name (DB) | `hercules` | `hercules` | `Hercules Merchandise UK` |
| wp-config From Email | *(not set)* | `info@hercules-merchandise.co.uk` | `info@hercules-merchandise.co.uk` |
| wp-config From Name | *(not set)* | `Hercules Merchandise` | `Hercules Merchandise UK` |
| Admin Email | `info@hercules-merchandise.co.uk` | `info@hercules-merchandise.co.uk` | OK |
| Worker Sender Email | - | `info@hercules-merchandise.co.uk` | OK |
| Worker Company Email | - | `info@hercules-merchandise.co.uk` | OK |

> **Note:** Staging wp-config.php forces the from email/name via `EASY_WP_SMTP_MAIL_FROM_EMAIL_FORCE`, overriding the wrong DB values. But the DB values should still be fixed.

---

## All Emails - Complete List

### A. WordPress / WooCommerce Emails (9 emails)

| # | Email ID | Email Type | Enabled | Sent To | Subject |
|---|----------|-----------|---------|---------|---------|
| 1 | `new_order` | New Order (Admin) | YES | `info@hercules-merchandise.co.uk` | New Order #{order_number} from {customer_first_name} |
| 2 | `customer_processing_order` | Order Processing (Customer) | YES | Customer | Your Order #{order_number} at Hercules Merchandise Has Been Received |
| 3 | `customer_on_hold_order` | Order On Hold (Customer) | YES | Customer | Your Hercules Merchandise Order #{order_number} Is On Hold |
| 4 | `customer_completed_order` | Order Completed (Customer) | NO | - | - |
| 5 | `customer_failed_order` | Failed Payment (Customer) | YES | Customer | Your Payment For {site_title} Order #{order_number} Was Unsuccessful! |
| 6 | `customer_refunded_order` | Refund (Customer) | YES | Customer | Your Order #{order_number} at Hercules Merchandise Has been Refunded! |
| 7 | `customer_new_account` | New Account (Customer) | YES | Customer | Your Hercules Merchandise Account Has Been Created! |
| 8 | `customer_reset_password` | Password Reset (Customer) | YES | Customer | Password Request For Hercules Merchandise |
| 9 | `low_stock` / `no_stock` | Stock Alert (Admin) | YES | `radium.kawshad0@gmail.com` | Low/No stock |

### B. Astro / Cloudflare Worker Emails (4 emails)

| # | Email ID | Email Type | Sent To | CC / Reply-To | Subject |
|---|----------|-----------|---------|--------------|---------|
| 10 | `contact_form` | Contact Form | `info@hercules-merchandise.co.uk` | CC: customer | Contact Request from {name} |
| 11 | `quantity_request` | Quantity/Quote Request | `info@hercules-merchandise.co.uk` | Reply-To: customer | Quote Request: {product} - {qty} pcs |
| 12 | `express_delivery` | Express Delivery Request | `info@hercules-merchandise.co.uk` | Reply-To: customer | Urgent Quote Request - {product} |
| 13 | `newsletter` | Newsletter Signup (Admin) | `info@hercules-merchandise.co.uk` | - | New Newsletter Subscription: {email} |

---

## Issues Fixed (Staging)

| # | Issue | Old Value | Fixed To | Status |
|---|-------|-----------|----------|--------|
| 1 | WC From Email (DB) | `info@hercules-merchandise.com` | `info@hercules-merchandise.co.uk` | FIXED |
| 2 | WC From Name (DB) | `hercules` | `Hercules Merchandise UK` | FIXED |
| 3 | Stock Email Recipient | `radium.kawshad0@gmail.com` | `info@hercules-merchandise.co.uk` | FIXED |
| 4 | Brevo API Key (wp-config) | Old perelweb.studio account | New gilles@ account key | FIXED |
| 5 | Brevo API Key (Worker) | Old perelweb.studio account | New gilles@ account key | FIXED |

## Issues Remaining (Live Site)

| # | Issue | Current Value | Should Be |
|---|-------|--------------|-----------|
| 1 | WC From Email (DB) | `info@hercules-merchandise.com` | `info@hercules-merchandise.co.uk` |
| 2 | WC From Name (DB) | `hercules` | `Hercules Merchandise UK` |
| 3 | Stock Email Recipient | `radium.kawshad0@gmail.com` | `info@hercules-merchandise.co.uk` |
| 4 | PayPal Email (disabled) | `radium.kawshad0@gmail.com` | `info@hercules-merchandise.co.uk` |

## Known Issue

- **New Order admin email subject** uses `{customer_first_name}` placeholder which is NOT a standard WooCommerce placeholder and renders literally as `{customer_first_name}` instead of the customer's name. Should be changed to `{billing_first_name}` or a supported placeholder.

---

## Test Results (2026-02-10)

> Test emails sent to `kamindudushmantha+test7@gmail.com` via WC REST API and WP-CLI.
> All admin emails verified delivered to `info@hercules-merchandise.co.uk`.
> All production settings verified correct after testing.

### WordPress Emails

| # | Email Type | Status | Subject Sent | Delivered Via |
|---|-----------|--------|-------------|---------------|
| 1 | New Order (Admin) | PASS | New Order #13471 from {customer_first_name} | Brevo - DELIVERED + OPENED |
| 2 | Order Processing (Customer) | PASS | Your Order #13471 at Hercules Merchandise Has Been Received | Brevo - DELIVERED |
| 3 | Order On Hold (Customer) | PASS | Your Hercules Merchandise Order #13471 Is On Hold | Brevo - DELIVERED |
| 4 | Order Completed (Customer) | SKIP | *(Disabled in settings)* | - |
| 4b | Failed Order (Admin) | PASS | [Hercules Merchandise UK]: Order #13471 has failed | Brevo - DELIVERED |
| 5 | Failed Payment (Customer) | PASS | Your Payment For Hercules Merchandise UK Order #13471 Was Unsuccessful! | Brevo - DELIVERED |
| 6 | Refund (Customer) | PASS | Your Order #13471 at Hercules Merchandise Has been Refunded! | Brevo - DELIVERED |
| 7 | New Account (Customer) | PASS | Your Hercules Merchandise Account Has Been Created! | Brevo - DELIVERED + OPENED |
| 8 | Password Reset (Customer) | PASS | [Hercules Merchandise UK] Password Changed | Brevo - DELIVERED |
| 9 | Stock Alert (Admin) | PASS | [Hercules Merchandise UK] Product low in stock | Brevo - DELIVERED |

### Astro / Worker Emails

| # | Email Type | Status | Subject Sent | Delivered Via |
|---|-----------|--------|-------------|---------------|
| 10 | Contact Form | PASS | Contact Request from Test User | Brevo - DELIVERED |
| 11 | Quantity Request | PASS | Quote Request: Test Product - 500 pcs | Brevo - DELIVERED |
| 12 | Express Delivery | PASS | Urgent Quote Request - Test Product | Brevo - DELIVERED |
| 13 | Newsletter Signup | PASS | New Newsletter Subscription: kamindudushmantha@gmail.com | Brevo - DELIVERED |

### Post-Test Cleanup

- Test order #13471: DELETED
- Test customer #736 (kamindu_test7_uk): DELETED
- All email settings verified at correct production values
- No test email addresses remaining in settings

---

## Verified Production Settings (Staging)

| Setting | Value |
|---------|-------|
| admin_email | `info@hercules-merchandise.co.uk` |
| woocommerce_email_from_address | `info@hercules-merchandise.co.uk` |
| woocommerce_email_from_name | `Hercules Merchandise UK` |
| woocommerce_stock_email_recipient | `info@hercules-merchandise.co.uk` |
| New Order recipient | `info@hercules-merchandise.co.uk` |
| Worker SENDER_EMAIL | `info@hercules-merchandise.co.uk` |
| Worker COMPANY_EMAIL | `info@hercules-merchandise.co.uk` |
| Brevo sender | `info@hercules-merchandise.co.uk` (active, domain authenticated) |

---

*Last updated: 2026-02-10*
