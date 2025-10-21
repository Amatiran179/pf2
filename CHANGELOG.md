# Changelog

## Unreleased

### Fixed

* REST metrics endpoint now accepts standard `X-WP-Nonce` headers and trims referrer payloads to prevent false `401` errors.
* CTA metrics rate limiting enforces a filterable 3-second window to reduce spam submissions.
* Added the missing `footer.php` wrapper so WordPress core no longer emits the deprecated "Theme without footer" warning.
* Added a core-compliant `header.php` wrapper and primary navigation template so the theme meets WordPress header requirements without deprecation notices.

### Improved

* Vite dev assets are only enqueued when the dev server is reachable, preventing 404 spam and ensuring REST config localization stays sanitized and filterable.

## v2.0.0 – 2025-10-21

✨ Major Release (Stable)

### Added

* CPT lengkap (Produk, Portofolio, Layanan, Tim, Testimoni)
* CTA System (Inline, Floating, Modal + Exit-Intent)
* Schema Engine (Product, Article, LocalBusiness, dsb.)
* SEO Meta Manager (Title, OG, Twitter, Canonical)
* AI Content Generator (REST + Adapter)
* Dashboard Analytics (REST Metrics + Export CSV/JSON)
* Theme Options & Settings UI
* Performance Pipeline (Cache, Critical CSS, Lazyload)
* i18n + A11y Compliance
* Build System: Vite + npm scripts

### Fixed

* Gallery autozoom bug (Batch 7)
* Schema compatibility with RankMath
* CTA modal focus trap & accessibility

### Improved

* Overall codebase (PSR-12 + WPCS)
* Loading speed (Lighthouse 95+)
