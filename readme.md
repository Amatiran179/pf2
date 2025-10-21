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
Kerjakan bootstrap pf2: package.json (vite dev/build/preview), vite.config.js (input front.js & admin.js → output assets/), .editorconfig, .gitignore, phpcs.xml, struktur dasar assets/inc/template-parts. Tambahkan deps: vite,@vitejs/plugin-legacy, swiper, simplelightbox. Pastikan npm run build sukses. Commit "chore: bootstrap project, vite config, deps".

TARGET
- Inisialisasi proyek pf2, setup Vite, struktur folder minimal, WPCS config, .editorconfig, .gitignore.

TINDAKAN
1) Buat/isi:
   - package.json (scripts: dev/build/preview)
   - vite.config.js (input front.js & admin.js; output ke assets/)
   - .editorconfig, .gitignore, phpcs.xml
   - assets/{js,css}/, inc/core/, template-parts/ (kerangka awal)
2) Siapkan NPM deps: vite, @vitejs/plugin-legacy; deps front: swiper, simplelightbox.
3) Pastikan `npm run dev` & `npm run build` sukses.

STANDAR KODE
- ESM untuk JS. Strict mode. Hindari var global tak perlu.
- PHP: declare strict_types tidak wajib, tapi ikuti WPCS & esc/sanitize.

PERINTAH
- npm install
- npm run build

ACCEPTANCE
- Build selesai tanpa error.
- Struktur project sesuai tree dasar.
- Commit: chore: bootstrap project, vite config, deps.

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
Tambahkan style.css (header tema lengkap). Buat functions.php memuat inc/core/{autoload.php,setup.php,enqueue.php,hooks.php}. Implement autoloader PF2\, setup theme supports & menus, enqueue mode dev/prod, hooks placeholder. Pastikan tema aktif tanpa fatal. Commit "feat(core): theme supports, autoloader, enqueue skeleton".

TARGET
- style.css (header tema lengkap)
- functions.php memuat core: autoload.php, setup.php, enqueue.php, hooks.php
- inc/core/{autoload.php,setup.php,enqueue.php,hooks.php}

DETAIL IMPLEMENTASI
- autoloader ringan namespace PF2\ → inc/.../lowercase-path.
- setup.php: load_theme_textdomain, title-tag, thumbnails, html5, responsive-embeds, nav menus.
- enqueue.php: mode dev (HMR) vs prod, versikan asset.
- hooks.php: registrasi awal (placeholder) untuk aksi/filters.

PERINTAH
- Tidak ada migrasi DB. Aktifkan tema & cek error log.

ACCEPTANCE
- Tema aktif tanpa fatal error.
- Asset front & admin bisa dienqueue (prod still minimal).
- Commit: feat(core): theme supports, autoloader, enqueue skeleton.


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
Buat assets/js/{front.js,admin.js}, assets/css/{front.css,admin.css}, front-page.php (hero + grid portofolio), template-parts/hero/hero-default.php, product/portfolio loop-item, index.php minimal. Pastikan console bersih & build OK. Commit "feat(templates): hero, loops, minimal layout".

TARGET
- assets/js/front.js, assets/js/admin.js, assets/css/front.css, assets/css/admin.css
- front-page.php sederhana (hero + grid portofolio)
- template-parts/hero/hero-default.php, loop-item product/portfolio
- index.php minimal

DETAIL
- Pastikan front.js siap untuk inisialisasi CTA/Gallery pada batch selanjutnya.
- CSS dasar responsif dan komponen kartu.

ACCEPTANCE
- Halaman depan tampil (hero → grid).
- Console bebas error. Build sukses.
- Commit: feat(templates): hero, loops, minimal layout.


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
Daftarkan CPT: product(/produk), portfolio(/portofolio), service(/layanan), team(/tim), testimonial(/testimoni) di inc/cpt/*.php show_in_rest:true. Flush rewrite. Pastikan UI CPT muncul & add/edit jalan. Commit "feat(cpt): register product/portfolio/service/team/testimonial".

TARGET
- Buat inc/cpt/{register-product.php,register-portfolio.php,register-service.php,register-team.php,register-testimonial.php}
- Registrasi labels, supports (title, editor, thumbnail), rewrite slug:
  - product → /produk/
  - portfolio → /portofolio/
  - service → /layanan/
  - team → /tim/
  - testimonial → /testimoni/

DETAIL
- Hook "init" prioritas default. Show in REST (Gutenberg).
- Tambahkan taxonomy kustom bila perlu (e.g., product_cat), tapi default boleh kosong.

PERINTAH
- wp rewrite flush --hard (post registrasi CPT)
- Tambahkan menu di Admin sesuai CPT

ACCEPTANCE
- CPT muncul di sidebar, bisa add/edit tanpa error.
- Permalink rapi & 404 tidak terjadi setelah flush.
- Commit: feat(cpt): register product/portfolio/service/team/testimonial.

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
Lengkapi template parts untuk product/portfolio (loop & single gallery placeholder), perindah front-page sections. Gunakan ukuran gambar WP + alt. Commit "feat(templates): product/portfolio loops & singles".

TARGET
- Lengkapi template-parts/* untuk product & portfolio (loop-item, single-gallery placeholder).
- Perkaya front-page.php dengan section produk & CTA ringkas.

DETAIL
- Buat partial: breadcrumbs.php, card-product.php, card-portfolio.php.
- Pastikan semua gambar pakai ukuran WP & atribut alt.

ACCEPTANCE
- Daftar & single tiap CPT tampil sempurna (tanpa style pecah).
- Lighthouse basic layout OK.
- Commit: feat(templates): product/portfolio loops & singles.

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
Implement CTA system: inc/helpers/cta.php + inc/templates/cta/{inline,floating,modal}. JS: assets/js/cta/{cta-core,cta-floating,cta-exit-intent}. Render CTA inline akhir konten, floating sticky, modal exit-intent. Logging klik sementara (console); REST menyusul. Commit "feat(cta): inline/floating/modal + exit-intent + basic tracking".

TARGET
- inc/helpers/cta.php (renderers + logic)
- inc/templates/cta/{inline.php,floating.php,modal.php}
- assets/js/cta/{cta-core.js,cta-floating.js,cta-exit-intent.js}
- hooks untuk menampilkan CTA pada single post & front-page section.
- Opsi global CTA (nomor WA default, text default) → akan ditautkan batch 8.

DETAIL
- Buat event tracking (klik CTA) ke REST (akan dihubungkan batch 9).
- Exit-intent pakai mouseout top metaKey check & debounce.

ACCEPTANCE
- CTA muncul sesuai posisi (inline akhir konten, floating sticky, modal on exit).
- Klik CTA ter-log di console sementara (REST menyusul).
- Commit: feat(cta): inline/floating/modal + exit-intent + basic tracking stub.

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
Schema engine: inc/schema/core.php + tipe {product,article,faq,howto,service-area,tourist-attraction,organization,local-business}. Auto-detect by post type; filter pf2_schema_data; output JSON-LD di wp_head. Toggle disable via filter agar tidak konflik SEO plugin. Commit "feat(schema): engine + multiple types + filters".

TARGET
- inc/schema/core.php (dispatcher + helper)
- Schema: product.php, article.php, faq.php, howto.php, service-area.php, tourist-attraction.php, organization.php, local-business.php
- Filter `pf2_schema_data` agar developer bisa modifikasi sebelum output.
- Output JSON-LD di wp_head dengan prioritas rendah.

DETAIL
- Auto-detect berdasarkan post type & category.
- Manual override (nanti UI di batch 8), sementara sediakan filter per-post meta (key reserved).

ACCEPTANCE
- Valid di Rich Results Test untuk tipe relevan.
- Tidak dobel output jika plugin SEO lain aktif (sediakan toggle disable via filter).
- Commit: feat(schema): engine + multiple types + filters.

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
Gallery stabil: inc/helpers/gallery.php, assets/js/gallery/{gallery-init,lightbox-init}. Cegah auto-zoom via CSS dan konfigurasi lightbox. Terapkan pada single product/portfolio. Commit "fix(gallery): stable swiper+lightbox, prevent auto-zoom".

TARGET
- inc/helpers/gallery.php (API render gallery)
- assets/js/gallery/{gallery-init.js,lightbox-init.js}
- CSS anti-autozoom: pastikan gambar tidak overflow, object-fit: contain bila perlu.
- Template single-gallery untuk product & portfolio finalized.

DETAIL
- Inisialisasi Swiper (loop, pagination, nav opsional), SimpleLightbox pada anchor group.
- Lazy-load via loading="lazy" + IntersectionObserver (jika perlu).

ACCEPTANCE
- Tidak ada auto-zoom bug saat slide/zoom.
- Lightbox & swipe berjalan mulus di mobile/desktop.
- Commit: fix(gallery): stable swiper+lightbox, prevent auto-zoom.

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
Theme Options & Settings UI: inc/admin/{menu.php,settings-ui.php}, inc/core/options.php. Field: warna, tipografi, WA, CTA default, hero. Nonce + capability check. Nilai terbaca di front. Commit "feat(admin): theme options + settings UI + sanitize/nonce".

TARGET
- inc/admin/{menu.php,settings-ui.php}
- inc/core/options.php (registry get/set)
- Fields: warna primer, tipografi dasar, kontak (WA), CTA default text, hero title/subtitle.
- Nonce & capability check (manage_options).

DETAIL
- Simpan ke wp_options namespace 'pf2_*'.
- Preview sederhana (enqueue admin.css).

ACCEPTANCE
- Nilai tersimpan, tervalidasi, terbaca di front.
- Tidak ada notice/warning.
- Commit: feat(admin): theme options + settings UI + sanitize/nonce.

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
Dashboard Analytics + REST metrics: inc/admin/{dashboard.php,exporter.php}, inc/rest/metrics.php (POST/GET), assets/js/utils/metrics.js untuk kirim event CTA. Tampilkan total klik CTA & top pages. Export CSV/JSON. Commit "feat(analytics): dashboard metrics + exporter + REST".

TARGET
- inc/admin/{dashboard.php,exporter.php}
- inc/rest/metrics.php → namespace pf2/v1: POST /metrics (cta_click), GET /metrics (aggregate).
- assets/js/utils/metrics.js → kirim event CTA ke REST (nonce + currentUser canRead).

DETAIL
- Tampilkan cards: total klik CTA hari ini, minggu ini, top 10 halaman.
- Export CSV & JSON.

ACCEPTANCE
- Klik CTA tercatat & terbaca di dashboard.
- Export menghasilkan file valid.
- Commit: feat(analytics): dashboard metrics + exporter + REST.

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
Performance: inc/performance/{cache.php,critical-css.php,lazyload.php}, assets/css/critical.css + inlining. Cache transient schema/partial, bypass untuk admin. Perbaiki LCP/CLS. Commit "perf: cache layers, critical CSS, lazyload observer".

TARGET
- inc/performance/{cache.php,critical-css.php,lazyload.php}
- critical.css di assets/css/ + inlining pada head (opsi via filter).
- Caching transient untuk schema & template parsial (TTL aman).

DETAIL
- Hindari cache untuk user logged-in admin.
- Sediakan CLI hook (nanti batch 14) untuk flush.

ACCEPTANCE
- LCP/CLS lebih baik (uji Lighthouse).
- Tidak ada regresi render.
- Commit: perf: cache layers, critical CSS, lazyload observer.

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
REST AI: inc/rest/index.php register pf2/v1; inc/rest/ai-content.php POST /ai/generate (title,desc,outline). Adapter dummy + filter. Setting API key di Settings UI. Commit "feat(ai): REST generator + settings key adapter".

TARGET
- inc/rest/index.php (register namespace pf2/v1)
- inc/rest/ai-content.php (POST /ai/generate → title, meta description, outline)
- Tambahkan setting API key di Settings UI (reuse batch 8).

DETAIL
- Endpoint menerima {topic, tone, keywords?}, balikan {title, description, outline}.
- Implementasi adapter (dummy) + filter agar bisa plug-in OpenAI/Gemini di masa depan.

ACCEPTANCE
- Request uji (Postman) menghasilkan payload terstruktur.
- Keamanan: nonce/cap, rate-limit sederhana (transient) untuk admin side.
- Commit: feat(ai): REST generator + settings key adapter.

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
SEO Meta Manager: inc/helpers/seo.php (title,desc,canonical,og/tw). Override per-post sederhana. Toggle disable bila plugin SEO aktif. Commit "feat(seo): meta manager + per-post override + compatibility".

TARGET
- inc/helpers/seo.php → title, meta description, canonical, og/tw cards.
- Override per-post via custom fields (sederhana), atau toggle disable bila RankMath/Yoast aktif.

DETAIL
- Hook ke wp_head, prioritas sebelum schema.
- Canonical cerdas: paginated, search, 404 di-skip.

ACCEPTANCE
- Meta muncul benar & tidak duplikat.
- Kompatibel plugin SEO populer (sediakan filter disable).
- Commit: feat(seo): meta manager + per-post override + compatibility filters.

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
i18n & A11y: generate languages/pf2.pot; audit aria-label, focus, kontras; bungkus string dengan __()/_e(). Commit "chore(i18n): add POT; a11y tweaks".

TARGET
- languages/pf2.pot
- Audit aria-label, keyboard focus states, kontras.
- Tambahkan teks/label melalui __()/_e() di seluruh template.

ACCEPTANCE
- POT terbentuk, string bisa diterjemahkan.
- Aksesibilitas minimal setara WCAG 2.1 AA untuk komponen inti.
- Commit: chore(i18n): add POT; a11y tweaks.

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
Release: finalize README.md & CHANGELOG.md, npm run build, tag v2.0.0, push tag. (Opsional) tambahkan GitHub Actions untuk lint/build. Commit/tag: "docs: finalize README", "chore(release): v2.0.0".

TARGET
- README.md final, CHANGELOG.md, tag rilis v2.0.0
- Tambah panduan upgrade & known issues.
- (Opsional) GitHub Actions untuk lint+build.

PERINTAH
- npm run build
- git tag -a v2.0.0 -m "pf2 v2.0.0"
- git push origin v2.0.0

ACCEPTANCE
- Build final OK; aktivasi tema sukses; tidak ada fatal.
- Commit/tag: docs: finalize README; chore(release): v2.0.0

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
