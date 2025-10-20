# Kontribusi ke pf2

Terima kasih ingin berkontribusi! Mohon ikuti panduan berikut agar proses review lebih cepat.

## Alur Git
- `main`: rilis stabil
- `dev`: pengembangan aktif
- `feature/*`: fitur baru
- `fix/*`: perbaikan bug
- `chore/*`: tooling/deps

## Komit
Gunakan conventional commits:
- feat(scope): ...
- fix(scope): ...
- perf(scope): ...
- docs(scope): ...
- chore(scope): ...
- refactor(scope): ...
- test(scope): ...

## Kode
- PHP 8.2+, WordPress 6.8+, patuhi WPCS.
- Gunakan escaping & sanitasi: `esc_html`, `esc_attr`, `wp_kses_post`, `sanitize_text_field`, `check_admin_referer`.
- Hindari logika berat di template; letakkan pada helper/class.

## Lint & Build
- `npm run build`
- `phpcs -s --standard=phpcs.xml`

## Pull Request
Lampirkan ringkasan, checklist, bukti (ss/gif), dan rujukan issue.
