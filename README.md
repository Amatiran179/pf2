# PutraFiber v2.0.0

🚀 WordPress Theme Profesional untuk <a href="https://putrafiber.com"><strong>Kontraktor Waterpark, Waterboom dan Produsen Fasilitas Wahana Wisata</strong></a>

## ✨ Fitur Utama

* Custom Post Type: Produk, Portofolio, Layanan, Tim, Testimoni
* CTA System (Inline, Floating, Modal + Exit Intent)
* Schema Engine (Auto JSON-LD, Product, LocalBusiness, dll.)
* SEO Meta Manager (Title, OG, Twitter, Canonical)
* Gallery Stabil (Swiper + SimpleLightbox Anti-Autozoom)
* Dashboard Analytics (REST Metrics + Export)
* AI Content Generator (REST API Ready)
* Theme Options (Settings API)
* Performance Pipeline (Cache, Critical CSS, Lazyload)
* i18n + A11y + WCAG Compliant

## 🧠 Teknologi

* PHP 8.1+, WP 6.5+
* Node 18+ (Vite Build)
* JS Modules (Vite Build)
* REST API (pf2/v1)
* Composer + WPCS
* No external dependency berat

## ⚙️ Instalasi

```bash
npm install
npm run build
# Upload theme ke /wp-content/themes/
```

Aktifkan dari WP Dashboard → Appearance → Themes.

## 🧩 Struktur Direktori

```
pf2/
 ├── inc/           → Core, Admin, REST, Schema, SEO, Performance
 ├── assets/        → JS, CSS, Images, Vite
 ├── template-parts/→ Hero, Gallery, CTA, Layouts
 ├── languages/     → pf2.pot
 └── style.css
```

## 🧱 REST Endpoints

| Endpoint              | Method   | Fungsi                |
| --------------------- | -------- | --------------------- |
| `/pf2/v1/metrics`     | POST/GET | Logging & Analytics   |
| `/pf2/v1/ai/generate` | POST     | AI Title/Meta/Outline |

## 🔒 Keamanan

* Nonce + Rate-limit di REST
* Escape HTML & URL di semua output
* Filterable dan compatible dengan RankMath/Yoast

## 🧾 Lisensi

GPLv3 – bebas dimodifikasi & didistribusikan dengan kredit PutraFiber Dev Team.
