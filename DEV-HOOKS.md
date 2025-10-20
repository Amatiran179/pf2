# Git Hooks (Husky + lint-staged)

## Instalasi
```bash
npm i -D husky lint-staged
npx husky init
# akan membuat .husky/pre-commit
```

## Pre-commit hook contoh
Edit `.husky/pre-commit`:
```sh
#!/usr/bin/env sh
. "$(dirname -- "$0")/_/husky.sh"

echo "Running pre-commit checks..."
npm run build --silent || exit 1
composer install --no-interaction --no-progress --quiet || true
vendor/bin/phpcs -s --standard=phpcs.xml || exit 1
```

## lint-staged (opsional, tambahkan ke package.json)
```json
{
  "lint-staged": {
    "*.php": "phpcs -s --standard=phpcs.xml",
    "assets/**/*.{js,css,scss}": "echo 'OK'"
  }
}
```
