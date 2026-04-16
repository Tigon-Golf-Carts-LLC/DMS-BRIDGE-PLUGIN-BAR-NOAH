# Tara Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All Tara models inherit from `../Manufacturer-Logic/Tara.md` which inherits from `../Global-New-Logic.md`.
Tara has NO converter defaults — all values come directly from the DMS / API payload.

---

## Tara Harmony

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) HARMONY {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | **No Tara-specific tabs.** No tabs are injected for Tara models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Harmony Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `TARA(R)` |
| `_wc_gla_pattern` | `Harmony` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `TARA(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Harmony` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | **NO** |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_cart-color` | From API payload |
| `pa_seat-color` | From API payload |
| `pa_sound-system` | `TARA(R) SOUND SYSTEM` |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Tara Spirit Air

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) SPIRIT AIR {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Spirit Air Add Ons` |

### postmeta - Google / Facebook pattern

`Spirit Air`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Spirit Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) SPIRIT PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Spirit Plus Add Ons` |

### postmeta - Google / Facebook pattern

`Spirit Plus`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Spirit Pro

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) SPIRIT PRO {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Spirit Pro Add Ons` |

### postmeta - Google / Facebook pattern

`Spirit Pro`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Roadster 2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) ROADSTER 2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Roadster 2 Add Ons` |

### postmeta - Google / Facebook pattern

`Roadster 2`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Roadster 2+2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) ROADSTER 2+2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Roadster 2+2 Add Ons` |

### postmeta - Google / Facebook pattern

`Roadster 2+2`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Explorer 2+2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) EXPLORER 2+2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Explorer 2+2 Add Ons` |

### postmeta - Google / Facebook pattern

`Explorer 2+2`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Turfman 450

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) TURFMAN 450 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Turfman 450 Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 450`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Turfman 700

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) TURFMAN 700 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Turfman 700 Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 700`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Turfman 700 EEC

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) TURFMAN 700 EEC {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Turfman 700 EEC Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 700 EEC`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara Turfman 1000

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) TURFMAN 1000 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) Turfman 1000 Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 1000`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## Tara T3 2+2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TARA(R) T3 2+2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TARA(R) T3 2+2 Add Ons` |

### postmeta - Google / Facebook pattern

`T3 2+2`

### All other postmeta

Inherit from Tara manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Tara models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TARA(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.
