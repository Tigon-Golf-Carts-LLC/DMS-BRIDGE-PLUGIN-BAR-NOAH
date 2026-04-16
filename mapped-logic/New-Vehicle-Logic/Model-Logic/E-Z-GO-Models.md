# E-Z-GO Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All E-Z-GO models inherit from `../Manufacturer-Logic/E-Z-GO.md` which inherits from `../Global-New-Logic.md`.
E-Z-GO has NO converter defaults — all values come directly from the DMS / API payload.

> **Special Name Handling (inherited from manufacturer):**
> - `post_title` / tags use: `EZGO(R)`
> - Categories / model taxonomy use: `EZ-GO(R)`
> - Color attribute slugs: `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`

---

## E-Z-GO RXV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) RXV {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | **No E-Z-GO-specific tabs.** No tabs are injected for E-Z-GO models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) RXV Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `EZGO(R)` |
| `_wc_gla_pattern` | `RXV` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EZGO(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `RXV` |
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
| `pa_ezgo-cart-colors` | From API payload |
| `pa_ezgo-seat-colors` | From API payload |
| `pa_sound-system` | `EZGO(R) SOUND SYSTEM` |
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

## E-Z-GO RXV 2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) RXV 2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) RXV 2 Add Ons` |

### postmeta - Google / Facebook pattern

`RXV 2`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO Liberty

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) LIBERTY {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) Liberty Add Ons` |

### postmeta - Google / Facebook pattern

`Liberty`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO Express 4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) EXPRESS 4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) Express 4 Add Ons` |

### postmeta - Google / Facebook pattern

`Express 4`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO RXV 4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) RXV 4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) RXV 4 Add Ons` |

### postmeta - Google / Facebook pattern

`RXV 4`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO Liberty 4F

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) LIBERTY 4F {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) Liberty 4F Add Ons` |

### postmeta - Google / Facebook pattern

`Liberty 4F`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO Express 6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) EXPRESS 6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) Express 6 Add Ons` |

### postmeta - Google / Facebook pattern

`Express 6`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.

---

## E-Z-GO Freedom RXV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EZGO(R) FREEDOM RXV {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EZGO(R) Freedom RXV Add Ons` |

### postmeta - Google / Facebook pattern

`Freedom RXV`

### All other postmeta

Inherit from E-Z-GO manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven E-Z-GO models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `EZGO(R) SOUND SYSTEM`. Uses `pa_ezgo-cart-colors` / `pa_ezgo-seat-colors`.

### DMS Defaults

All values from API payload.
