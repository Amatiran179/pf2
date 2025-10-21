# Contributing Guide

## Branching

* `main` → stable release
* `dev` → active development
* `feature/*` → experimental modules

## Commit Convention

* feat: fitur baru
* fix: perbaikan bug
* perf: optimisasi
* chore: housekeeping
* docs: dokumentasi

## Build Workflow

```bash
npm install
npm run build
composer run lint
```

## Pull Request

* Sertakan deskripsi singkat + checklist testing
* Jangan commit file build (`dist/`)
* Gunakan PR Template otomatis (Batch 2)
