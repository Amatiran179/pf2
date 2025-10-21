# PutraFiber AI Theme v2 (pf2)

Bootstrap scaffolding for the enterprise WordPress theme that will power SEO automation, layered CTA components, performant galleries, analytics dashboards, and REST-based AI features.

## Requirements
- PHP 8.2+
- WordPress 6.8+
- Node.js 18+
- Composer 2+

## Getting Started
1. Install Node.js dependencies:
   ```bash
   npm install
   ```
2. Build the assets with Vite (outputs to `assets/js` and `assets/css`):
   ```bash
   npm run build
   ```
3. Install PHP development dependencies (WordPress Coding Standards):
   ```bash
   composer install
   ```
4. Optionally run code style checks:
   ```bash
   composer lint
   ```

## Project Structure
```
pf2/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── front.css
│   ├── images/
│   │   ├── icons/
│   │   └── placeholders/
│   └── js/
│       ├── admin.js
│       ├── front.js
│       └── utils/
│           └── dom.js
├── inc/
│   ├── admin/
│   ├── core/
│   │   ├── autoload.php
│   │   ├── enqueue.php
│   │   ├── hooks.php
│   │   └── setup.php
│   ├── cpt/
│   ├── helpers/
│   ├── performance/
│   ├── rest/
│   ├── schema/
│   └── templates/
│       ├── admin/
│       ├── cta/
│       └── parts/
├── languages/
├── template-parts/
│   ├── footer/
│   ├── hero/
│   ├── portfolio/
│   └── product/
├── front-page.php
├── functions.php
├── index.php
├── package.json
├── vite.config.js
├── composer.json
├── phpcs.xml
├── .editorconfig
├── .gitignore
└── README.md
```

## Next Batches
- **Batch 1** will wire autoloaders, enqueue routines, and WordPress hooks.
- **Batch 2+** will progressively add CPTs, CTA templates, gallery features, analytics, and AI-ready REST endpoints.

Each batch is designed to be merge-safe and incrementally extensible.
