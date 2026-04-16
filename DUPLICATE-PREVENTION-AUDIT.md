# 🛡️ Complete Duplicate Prevention Audit

> **TIGON DMS Connect — Inventory Sync Dedup Architecture**
>
> A comprehensive breakdown of every layer protecting against duplicate WooCommerce products during DMS inventory sync.

---

## Overview

You have **7 layers** of protection. Below is every layer, what it checks, where it lives in the codebase, and where the remaining gaps are.

---

## Layer 1 — PID from DMS

**File:** `src/Abstracts/Abstract_Cart.php` · Lines 571–578 · `get_pid()`

```php
if (wc_get_product($this->cart['pid']) != false)
    $this->product_id = $this->cart['pid'];       // DMS told us the WordPress ID

if (!$this->product_id)
    $this->product_id = wc_get_product_id_by_sku($this->sku);  // Fallback: SKU lookup
```

| Detail       | Value                                          |
|--------------|-------------------------------------------------|
| **Checks**   | `pid` (WooCommerce post ID) first, then `_sku` (VIN or Serial) |
| **Used by**  | Both mapped and selective sync                  |

---

## Layer 2 — `_dms_cart_id` Meta Lookup

**File:** `dms-bridge-plugin.php` · Lines 651–664

```php
function tigon_dms_get_product_by_cart_id($cart_id) {
    // SELECT post_id FROM wp_postmeta
    // WHERE meta_key = '_dms_cart_id' AND meta_value = $cart_id
}
```

| Detail        | Value                                             |
|---------------|---------------------------------------------------|
| **Checks**    | DMS `_id` stored as WordPress post meta           |
| **Used by**   | Selective sync only (`tigon_dms_ensure_woo_product()`) |
| **Not used by** | ⚠️ Mapped sync *(this is a gap)*              |

---

## Layer 3 — Create / Update Decision

**File:** `src/Abstracts/Abstract_Cart.php` · Lines 3428–3433

```php
if ($this->product_id) {
    $this->method = 'update';     // Found existing → update it
} else {
    $this->method = 'create';     // Not found → new product
}
```

| Detail    | Value                                                              |
|-----------|--------------------------------------------------------------------|
| **Drives** | Whether `create_from_database_object()` or `update_from_database_object()` is called |

---

## Layer 4 — Write-Time Guards

**File:** `src/Admin/Database_Write_Controller.php` · Lines 57–64, 79–85

| Guard                            | Behavior                              |
|----------------------------------|---------------------------------------|
| `create_from_database_object()`  | Refuses if `posts.ID` is already set  |
| `update_from_database_object()`  | Refuses if `posts.ID` is 0 or empty   |

---

## Layer 5 — New Cart Dedup Key

**File:** `src/Core.php` · Lines 926–942 (selective batch) and Lines 1086–1112 (mapped init)

```
$dedup_key = make | model | cartColor | seatColor | locationId
```

| Detail        | Value                                                           |
|---------------|-----------------------------------------------------------------|
| **Purpose**   | Prevents identical new-cart templates from creating multiple products in one sync |
| **Storage**   | Persisted in transients across batched AJAX requests            |

---

## Layer 6 — PID Report-Back to DMS

After every successful create/update:

```php
DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
// { _id, pid, advertising: { onWebsite: true, websiteUrl: "..." } }
```

| Detail      | Value                                                                  |
|-------------|------------------------------------------------------------------------|
| **Purpose** | Ensures next sync, the cart arrives with a valid `pid` → Layer 1 catches it → routes to update |

---

## Layer 7 — Slug Deduplication

**File:** `src/Admin/Database_Write_Controller.php` · Lines 43–52

```php
$unique_slug = wp_unique_post_slug(...)
```

| Detail      | Value                                                                    |
|-------------|--------------------------------------------------------------------------|
| **Purpose** | If a duplicate somehow gets created, at least the URL slug won't collide (appends `-2`, `-3`, etc.) |
| **Note**    | This doesn't **prevent** duplicates but does prevent URL conflicts       |

---

## ⚠️ Where Duplicates CAN Still Happen

| Scenario | Risk | Explanation |
|----------|------|-------------|
| DMS `pid` is `null` **AND** SKU was manually edited in WooCommerce | 🟡 Medium | Both Layer 1 checks fail → creates duplicate |
| PID report-back fails (network error) | 🟡 Medium | DMS still has `pid: null` next sync. Caught by try/catch as "non-fatal" so it silently fails |
| Mapped sync doesn't use `_dms_cart_id` lookup | 🟡 Medium | Selective sync checks `_dms_cart_id` meta (Layer 2), but mapped sync skips this entirely — it only uses `pid` + SKU |
| `already_exists` flag is hardcoded `false` | 🟢 Low | Declared at line 115, set to `false` at line 577, never set to `true` — it's dead code |
| Used carts have no in-memory dedup | 🟢 Low | Only new carts get the `make\|model\|color\|seat\|location` key. Used carts rely entirely on `pid` + SKU |

---

## Bottom Line

Your **strongest protection** is the **SKU** (VIN / Serial number).

- **Used carts:** `wc_get_product_id_by_sku()` is highly reliable since VINs are unique.
- **New carts** (which may not have a VIN/Serial yet): the dedup key prevents duplicates within a single sync run, and the PID report-back prevents them across runs.

### Biggest real-world risk

If the `PUT` back to DMS fails after creating a product, and then someone re-runs the sync — the DMS cart still has `pid: null`. If it's a new cart with no VIN/Serial, the SKU fallback uses the generated `MAKE+MODEL+COLOR+SEAT+CITY` key which `wc_get_product_id_by_sku()` should still catch.

**You're generally covered**, but it depends on that generated SKU not changing between syncs.
