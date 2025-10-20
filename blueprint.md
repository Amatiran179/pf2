Siap. Di bawah ini saya tuliskan **README.md versi lengkap** untuk repo `pf2`—sudah termasuk:

* Deskripsi & Visi (enterprise-grade, SEO/CTA/Performance).
* **Tree struktur lengkap** (folder & file kunci, modul per area).
* **Sistem batch** yang berkesinambungan (Batch 0 → 14) + *perintah* real (Git, NPM/Vite, WP-CLI) + *deliverables* & *acceptance criteria* per batch.
* Pedoman kontribusi, standar koding, CI/CD, dan rilis.

Anda tinggal **salin seluruh isi** ke `README.md` di root repo `pf2`.

---

# 🧠 PUTRAFIBER AI THEME v2 (`pf2`)

**Blueprint WordPress Enterprise – SEO • CTA • Performance • AI-Ready**

> **Author**: PutraFiber Lab (Amatiran179)
> **License**: GPLv3
> **Requires**: PHP 8.2+, WordPress 6.8+
> **Stack**: WordPress Core • Modular OOP • Vite Build • WP-CLI

---

## 1) Deskripsi Singkat

`pf2` adalah **tema WordPress kelas enterprise** yang memadukan **SEO bawaan (schema otomatis)**, **sistem CTA berlapis** (inline, floating, modal, exit-intent), **pipeline performa modern** (Vite, lazy-load, cache), **CPT lengkap** (Produk, Portofolio, Layanan, Blog/Artikel, Tim, Testimoni), dan **Dashboard Analytics** (klik CTA, validasi schema, perilaku pengguna) — *AI-ready* via REST untuk generator konten/meta.

**Tujuan utama**: menjadikan WordPress **mesin konversi end-to-end** yang **cepat**, **stabil**, **terukur**, dan **mudah dikembangkan** tim.

---

## 2) Visi

1. **Conversion-First** – setiap elemen (layout, CTA, schema, tracking) diarahkan ke konversi.
2. **SEO Native** – schema + meta terotomasi dan editable, siap *rich results*.
3. **Arsitektur Modular** – OOP + autoloader + hooks → mudah dikembangkan/dirawat.
4. **Performance** – target Lighthouse 95+ (mobile & desktop).
5. **Data-Driven** – dashboard metrik CTA/schema; export data; audit on-page.
6. **Enterprise-Ready** – kompatibel ekosistem besar (RankMath, Woo, Elementor), WPCS, WCAG 2.1 AA.

---

## 3) Tree Struktur Lengkap

```
pf2/
├─ assets/
│  ├─ css/
│  │  ├─ admin.css
│  │  ├─ front.css
│  │  └─ critical.css              # optional: inlining critical CSS
│  ├─ js/
│  │  ├─ front.js                  # entry: Swiper, SimpleLightbox, CTA handlers
│  │  ├─ admin.js                  # entry: dashboard/admin UI
│  │  ├─ cta/
│  │  │  ├─ cta-core.js
│  │  │  ├─ cta-exit-intent.js
│  │  │  └─ cta-floating.js
│  │  ├─ gallery/
│  │  │  ├─ gallery-init.js
│  │  │  └─ lightbox-init.js
│  │  └─ utils/
│  │     ├─ dom.js
│  │     └─ metrics.js
│  └─ images/
│     ├─ icons/
│     └─ placeholders/
│
├─ inc/
│  ├─ core/
│  │  ├─ autoload.php              # PSR-4 like autoloader (theme scope)
│  │  ├─ setup.php                 # theme supports, menus, thumbs, etc.
│  │  ├─ enqueue.php               # Vite/dev & build enqueue
│  │  ├─ options.php               # theme options registry
│  │  ├─ security.php              # nonce, sanitization helpers
│  │  ├─ hooks.php                 # action/filter registrations
│  │  └─ compatibility.php         # plugin/theme compatibility shims
│  ├─ admin/
│  │  ├─ menu.php                  # admin menu & pages registration
│  │  ├─ settings-ui.php           # settings fields & sections
│  │  ├─ dashboard.php             # analytics widgets, reports
│  │  └─ exporter.php              # CSV/JSON/PDF export
│  ├─ cpt/
│  │  ├─ register-product.php
│  │  ├─ register-portfolio.php
│  │  ├─ register-service.php
│  │  ├─ register-team.php
│  │  └─ register-testimonial.php
│  ├─ schema/
│  │  ├─ core.php                  # dispatcher + helpers
│  │  ├─ product.php
│  │  ├─ faq.php
│  │  ├─ howto.php
│  │  ├─ service-area.php
│  │  ├─ tourist-attraction.php
│  │  ├─ article.php
│  │  ├─ organization.php
│  │  └─ local-business.php
│  ├─ rest/
│  │  ├─ index.php                 # namespace register (e.g. pf2/v1)
│  │  ├─ ai-content.php            # POST /ai/generate (title/desc/topic)
│  │  └─ metrics.php               # POST/GET /metrics (CTA clicks, etc.)
│  ├─ performance/
│  │  ├─ cache.php                 # transient/object cache helpers
│  │  ├─ lazyload.php
│  │  └─ critical-css.php
│  ├─ helpers/
│  │  ├─ images.php                # WebP, sizes, attributes
│  │  ├─ gallery.php               # Swiper + SimpleLightbox composition
│  │  ├─ cta.php                   # CTA rendering helpers
│  │  └─ seo.php                   # meta, og, twitter, canonical
│  └─ templates/
│     ├─ cta/
│     │  ├─ inline.php
│     │  ├─ floating.php
│     │  └─ modal.php
│     ├─ parts/
│     │  ├─ breadcrumbs.php
│     │  ├─ card-product.php
│     │  ├─ card-portfolio.php
│     │  └─ hero.php
│     └─ admin/
│        └─ cards.php
│
├─ template-parts/
│  ├─ hero/hero-default.php
│  ├─ product/
│  │  ├─ loop-item.php
│  │  └─ single-gallery.php
│  ├─ portfolio/
│  │  ├─ loop-item.php
│  │  └─ single-gallery.php
│  └─ footer/footer-default.php
│
├─ languages/
│  └─ pf2.pot
│
├─ .editorconfig
├─ .gitignore
├─ phpcs.xml                      # WordPress Coding Standards config
├─ package.json                   # vite scripts
├─ vite.config.js
├─ functions.php
├─ style.css                      # theme header + minimal base
├─ front-page.php
├─ index.php
└─ README.md
```

> **Catatan**: Nama file/struktur boleh Anda sesuaikan, yang penting *concern* per modul tetap terpisah rapi.

---

## 4) Setup & Build

### Prasyarat

* Node 18+ (atau 20+), npm 9+
* PHP 8.2+, ekstensi `json`, `mbstring`, `curl`, `dom`
* WP-CLI (opsional namun disarankan)
* Server mendukung `mod_rewrite` (Apache) atau aturan rewrite pada Nginx

### Langkah

```bash
# 1) Clone
git clone https://github.com/Amatiran179/pf2.git wp-content/themes/pf2
cd wp-content/themes/pf2

# 2) Install deps front-end
npm install

# 3) Mode dev (Vite dev server)
npm run dev

# 4) Build produksi
npm run build
```

**Aktifkan tema** via `Appearance → Themes`.

---

## 5) Konvensi Git & Branch

```
main         → rilisan stabil (tagged releases)
dev          → pengembangan aktif (PR masuk ke sini)
feature/*    → fitur baru (mis. feature/cta-exit-intent)
fix/*        → perbaikan bug (mis. fix/gallery-autozoom)
chore/*      → tooling, CI, deps, dsb.
```

**Commit message (disarankan)**

* `feat(scope): ...`
* `fix(scope): ...`
* `refactor(scope): ...`
* `perf(scope): ...`
* `docs(scope): ...`
* `chore(scope): ...`
* `test(scope): ...`

---

## 6) Sistem Batch (End-to-End) — Perintah, Deliverables, Acceptance

> **Format tiap batch**: *Tujuan → Perintah Utama → Deliverables → Acceptance Criteria*
> Ikuti urutan untuk hasil konsisten. Anda boleh *parallelize* sebagian pekerjaan non-blokir.

### **Batch 0 — Bootstrap & Tooling**

**Tujuan**: inisialisasi proyek, standar koding, tooling.
**Perintah**

```bash
git checkout -b chore/bootstrap
npm init -y
npm i -D vite @vitejs/plugin-legacy
npm i swiper simplelightbox
# (opsional) linter/formatter:
npm i -D eslint stylelint postcss autoprefixer
# WPCS (opsional via composer jika diperlukan):
# composer require --dev wp-coding-standards/wpcs dealerdirect/phpcodesniffer-composer-installer
git add . && git commit -m "chore: bootstrap project, vite config, deps"
git push -u origin chore/bootstrap
```

**Deliverables**: `package.json`, `vite.config.js`, `phpcs.xml`, `.editorconfig`, `.gitignore`.
**Acceptance**: `npm run dev` & `npm run build` berjalan tanpa error.

---

### **Batch 1 — Core Theme & Autoload**

**Tujuan**: *theme supports*, autoloader, hooks dasar.
**Perintah**

```bash
git checkout -b feat/core-theme
# isi style.css header, functions.php panggil inc/core/*
git add . && git commit -m "feat(core): theme supports, autoloader, enqueue skeleton"
git push -u origin feat/core-theme
```

**Deliverables**: `style.css`, `functions.php`, `inc/core/{autoload,setup,enqueue,hooks}.php`.
**Acceptance**: Tema dapat diaktifkan, tidak ada fatal error.

---

### **Batch 2 — Enqueue (Vite), Front & Admin Entries**

**Tujuan**: integrasi Vite, entry `assets/js/front.js` & `admin.js`.
**Perintah**

```bash
git checkout -b feat/enqueue-vite
npm run build
git add . && git commit -m "feat(enqueue): vite front/admin, HMR dev, build mapping"
git push -u origin feat/enqueue-vite
```

**Deliverables**: `inc/core/enqueue.php`, entri JS/CSS minimal.
**Acceptance**: asset termuat di front/admin, console bersih.

---

### **Batch 3 — CPT (Produk, Portofolio, Layanan, Tim, Testimoni)**

**Tujuan**: daftar CPT + labels + rewrite.
**Perintah**

```bash
git checkout -b feat/cpt
wp rewrite flush --hard   # via WP-CLI setelah aktivasi
git add . && git commit -m "feat(cpt): product, portfolio, service, team, testimonial"
git push -u origin feat/cpt
```

**Deliverables**: `inc/cpt/register-*.php`.
**Acceptance**: menu CPT muncul, dapat tambah item tanpa error.

---

### **Batch 4 — Template Parts (Hero, Loop, Single)**

**Tujuan**: struktur tampilan dasar + *front-page*.
**Perintah**

```bash
git checkout -b feat/templates
git add template-parts/ front-page.php index.php
git commit -m "feat(templates): hero, product/portfolio loops, front-page"
git push -u origin feat/templates
```

**Deliverables**: `template-parts/*`, `front-page.php`.
**Acceptance**: halaman depan tampil lengkap (header→hero→section→footer).

---

### **Batch 5 — CTA System (Inline, Floating, Modal, Exit-Intent)**

**Tujuan**: CTA modular + hooks + event tracking.
**Perintah**

```bash
git checkout -b feat/cta-system
git add inc/helpers/cta.php inc/templates/cta/* assets/js/cta/*
git commit -m "feat(cta): inline/floating/modal + exit-intent + tracking"
git push -u origin feat/cta-system
```

**Deliverables**: template CTA + JS handler + opsi global.
**Acceptance**: CTA muncul sesuai konfigurasi; klik tersimpan (local/REST).

---

### **Batch 6 — Schema Engine (Auto & Manual)**

**Tujuan**: dispatcher schema + tipe: Product, Article, FAQ, HowTo, ServiceArea, TouristAttraction, Organization, LocalBusiness.
**Perintah**

```bash
git checkout -b feat/schema-engine
git add inc/schema/*
git commit -m "feat(schema): engine + multiple types + filters"
git push -u origin feat/schema-engine
```

**Deliverables**: `inc/schema/core.php` + file tipe schema.
**Acceptance**: JSON-LD valid pada single post CPT terkait (uji di Rich Results).

---

### **Batch 7 — Gallery Stable (Swiper + SimpleLightbox + Anti-Autozoom)**

**Tujuan**: galeri produk/portofolio stabil + CSS anti-zoom.
**Perintah**

```bash
git checkout -b fix/gallery-stable
git add inc/helpers/gallery.php template-parts/*/single-gallery.php assets/js/gallery/*
git commit -m "fix(gallery): swiper+lightbox stable, prevent auto-zoom"
git push -u origin fix/gallery-stable
```

**Deliverables**: komponen galeri siap pakai.
**Acceptance**: tidak ada auto-zoom; swipe & lightbox berfungsi, lazy-load aktif.

---

### **Batch 8 — Theme Options & Admin Settings UI**

**Tujuan**: halaman **PutraFiber → Settings** (warna, tipografi, kontak, CTA default, hero).
**Perintah**

```bash
git checkout -b feat/admin-settings
git add inc/admin/{menu.php,settings-ui.php} inc/core/options.php
git commit -m "feat(admin): theme options, settings UI, sanitize & nonce"
git push -u origin feat/admin-settings
```

**Deliverables**: settings page + registrasi opsi.
**Acceptance**: nilai tersimpan & terbaca; sanitasi/nonce lolos.

---

### **Batch 9 — Dashboard Analytics**

**Tujuan**: panel metrik CTA/schema, export data.
**Perintah**

```bash
git checkout -b feat/analytics
git add inc/admin/{dashboard.php,exporter.php} inc/rest/metrics.php assets/js/utils/metrics.js
git commit -m "feat(analytics): dashboard metrics + exporter + REST"
git push -u origin feat/analytics
```

**Deliverables**: kartu metrik, grafik sederhana, export CSV/JSON.
**Acceptance**: data klik CTA terbaca; export menghasilkan file valid.

---

### **Batch 10 — Performance (Cache, Lazy, Critical CSS)**

**Tujuan**: caching transient/object, inlining critical CSS, observer lazy-load.
**Perintah**

```bash
git checkout -b perf/pipeline
git add inc/performance/* assets/css/critical.css
git commit -m "perf: cache layers, critical CSS, lazyload observer"
git push -u origin perf/pipeline
```

**Deliverables**: modul performa aktif.
**Acceptance**: TTFB & LCP membaik; tidak ada regresi tampilan.

---

### **Batch 11 — REST: AI Content Generator**

**Tujuan**: endpoint AI (title/desc/outline), pengaturan API key.
**Perintah**

```bash
git checkout -b feat/ai-rest
git add inc/rest/{index.php,ai-content.php} inc/admin/settings-ui.php
git commit -m "feat(ai): REST generator + settings key"
git push -u origin feat/ai-rest
```

**Deliverables**: `/wp-json/pf2/v1/ai/generate` (mock/adapter).
**Acceptance**: permintaan uji (POST) memberi respon JSON terstruktur.

---

### **Batch 12 — SEO Meta Manager**

**Tujuan**: title, desc, canonical, OG/Twitter Cards; override per post.
**Perintah**

```bash
git checkout -b feat/seo-meta
git add inc/helpers/seo.php inc/admin/settings-ui.php
git commit -m "feat(seo): meta manager + per-post override"
git push -u origin feat/seo-meta
```

**Deliverables**: meta output head, filterable.
**Acceptance**: tag muncul benar; konflik dengan plugin SEO bisa di-toggle.

---

### **Batch 13 — i18n, A11y, Compliance**

**Tujuan**: `.pot`, teks siap translate, markup aksesibel.
**Perintah**

```bash
git checkout -b chore/i18n-a11y
# generate pot pakai wp-cli make-pot atau Poedit
git add languages/pf2.pot
git commit -m "chore(i18n): add POT; a11y tweaks; aria-labels"
git push -u origin chore/i18n-a11y
```

**Deliverables**: `languages/pf2.pot`, label ARIA, fokus states.
**Acceptance**: *screen reader* baik; teks dapat diterjemahkan.

---

### **Batch 14 — Release & Docs**

**Tujuan**: dokumentasi final, changelog, tag rilis.
**Perintah**

```bash
git checkout main
git merge dev --no-ff
npm run build
# uji akhir di staging → bila OK:
git tag -a v2.0.0 -m "pf2 v2.0.0"
git push origin v2.0.0
```

**Deliverables**: `README.md` (ini), `CHANGELOG.md`, tag `v2.0.0`.
**Acceptance**: build final, aktivasi sukses, lint/kode bersih.

---

## 7) WP-CLI (Opsional)

```bash
# flush rewrite setelah registrasi CPT
wp rewrite flush --hard

# bersihkan cache theme
wp transient delete --all

# (opsional) CLI kustom (bila disediakan)
wp pf2:flush-cache
wp pf2:regen-schema
wp pf2:export-config
```

---

## 8) Hooks Penting

```php
do_action('pf2_before_cta');
do_action('pf2_after_cta');

apply_filters('pf2_schema_data', $data);
apply_filters('pf2_cta_text', $text);
apply_filters('pf2_meta_overrides', $meta);
```

---

## 9) Standar Koding & QA

* **WPCS** via `phpcs.xml` (disarankan pre-commit hook).
* **Escaping/Sanitasi**: `esc_html`, `esc_attr`, `wp_kses_post`, `sanitize_text_field`.
* **Nonce & Capability** untuk semua form/admin action.
* **A11y**: role/aria yang tepat, fokus jelas, kontras memadai.

---

## 10) CI/CD (Opsional)

* **GitHub Actions**:

  * Job: lint PHP (PHPCS), lint JS/CSS, build Vite, upload artifact.
  * Job: release (draft GitHub Release saat tag).
* **Deployment**:

  * Sync hanya `pf2/` ke `wp-content/themes/pf2`.
  * Build di server atau commit hasil build (sesuai workflow).

---

## 11) Roadmap (Pasca v2.0)

* **v2.1**: Dashboard Analytics Pro, PWA Manifest, Dark Mode Toggle UI.
* **v2.2**: WooCommerce starter (katalog ringan), Mega-menu, Breadcrumbs Pro.
* **v3.0**: Multi-tenant/SaaS, License Server, Realtime Metrics, GraphQL.

---

## 12) Contoh Implementasi Bisnis

* **Kontraktor/Manufaktur**: Portofolio + Product Schema + CTA WA otomatis; harga default Rp1.000 bila kosong.
* **Hotel/Villa**: LocalBusiness + CTA Book; galeri *lightbox* cepat.
* **Sekolah/Edu**: Organization + ServiceArea; blog edukasi + FAQ.

---

## 13) Support

* **WA**: 0856423188455
* **Demo**: (opsional)
* **Issue**: gunakan GitHub Issues dengan label `bug`, `feature`, `perf`, `docs`.

---

### Tagline

**PUTRAFIBER AI Theme v2 — From Design to Conversion in One System.**
