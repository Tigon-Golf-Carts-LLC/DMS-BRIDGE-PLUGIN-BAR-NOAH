# Club Car Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All Club Car models inherit from `../Manufacturer-Logic/Club-Car.md` which inherits from `../Global-New-Logic.md`.
Club Car has NO converter defaults — all values come directly from the DMS / API payload.
Club Car HAS dedicated color palette attributes (`pa_club-car-cart-colors` / `pa_club-car-seat-colors`).

---

## Club Car Onward 2 Passenger

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) ONWARD 2 PASSENGER {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | **No Club Car-specific tabs.** No tabs are injected for Club Car models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Onward 2 Passenger Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `CLUB CAR(R)` |
| `_wc_gla_pattern` | `Onward 2 Passenger` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `CLUB CAR(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Onward 2 Passenger` |
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
| `pa_club-car-cart-colors` | From API payload |
| `pa_club-car-seat-colors` | From API payload |
| `pa_sound-system` | `CLUB CAR(R) SOUND SYSTEM` |
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

## Club Car Onward 4 Passenger

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) ONWARD 4 PASSENGER {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Onward 4 Passenger Add Ons` |

### postmeta - Google / Facebook pattern

`Onward 4 Passenger`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Onward 4 Passenger LSV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) ONWARD 4 PASSENGER LSV {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Onward 4 Passenger LSV Add Ons` |

### postmeta - Google / Facebook pattern

`Onward 4 Passenger LSV`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Onward 4 Forward

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) ONWARD 4 FORWARD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Onward 4 Forward Add Ons` |

### postmeta - Google / Facebook pattern

`Onward 4 Forward`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Onward 6 Passenger

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) ONWARD 6 PASSENGER {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Onward 6 Passenger Add Ons` |

### postmeta - Google / Facebook pattern

`Onward 6 Passenger`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Cru

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CRU {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Cru Add Ons` |

### postmeta - Google / Facebook pattern

`Cru`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Heritage Blue Onward

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) HERITAGE BLUE ONWARD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Heritage Blue Onward Add Ons` |

### postmeta - Google / Facebook pattern

`Heritage Blue Onward`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Sagebrush Matte Green Onward

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) SAGEBRUSH MATTE GREEN ONWARD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Sagebrush Matte Green Onward Add Ons` |

### postmeta - Google / Facebook pattern

`Sagebrush Matte Green Onward`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Urban XR

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) URBAN XR {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Urban XR Add Ons` |

### postmeta - Google / Facebook pattern

`Urban XR`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 100

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 100 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 100 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 100`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 300

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 300 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 300 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 300`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 500

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 500 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 500 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 500`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 502

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 502 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 502 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 502`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 550

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 550 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 550 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 550`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 700

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 700 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 700 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 700`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 1500 2WD

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 1500 2WD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 1500 2WD Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 1500 2WD`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car XRT 500

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) XRT 500 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) XRT 500 Add Ons` |

### postmeta - Google / Facebook pattern

`XRT 500`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 1500

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 1500 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 1500 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 1500`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 1700

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 1700 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 1700 Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 1700`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car XRT 1500

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) XRT 1500 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) XRT 1500 Add Ons` |

### postmeta - Google / Facebook pattern

`XRT 1500`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car XRT 1500 SE

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) XRT 1500 SE {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) XRT 1500 SE Add Ons` |

### postmeta - Google / Facebook pattern

`XRT 1500 SE`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Villager 6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) VILLAGER 6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Villager 6 Add Ons` |

### postmeta - Google / Facebook pattern

`Villager 6`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Villager 8

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) VILLAGER 8 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Villager 8 Add Ons` |

### postmeta - Google / Facebook pattern

`Villager 8`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Transporter

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) TRANSPORTER {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Transporter Add Ons` |

### postmeta - Google / Facebook pattern

`Transporter`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Tempo 2+2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) TEMPO 2+2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Tempo 2+2 Add Ons` |

### postmeta - Google / Facebook pattern

`Tempo 2+2`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Urban LSV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) URBAN LSV {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Urban LSV Add Ons` |

### postmeta - Google / Facebook pattern

`Urban LSV`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 510 LSV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 510 LSV {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 510 LSV Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 510 LSV`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Club Car Carryall 710 LSV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `CLUB CAR(R) CARRYALL 710 LSV {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `CLUB CAR(R) Carryall 710 LSV Add Ons` |

### postmeta - Google / Facebook pattern

`Carryall 710 LSV`

### All other postmeta

Inherit from Club Car manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Club Car models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `CLUB CAR(R) SOUND SYSTEM`. Uses `pa_club-car-cart-colors` / `pa_club-car-seat-colors`.

### DMS Defaults

All values from API payload.
