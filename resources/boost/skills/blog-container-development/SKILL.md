---
name: blog-container-development
description: Extend or fork the cms-orbit/blog reference container — PostEntity, container.json, instance routes, theme views, and SaaS admin hub. Activate when building blog-like SaaS containers or customizing the sample blog package.
---

# Blog container development

## When to use

- Modifying `PostEntity`, blog admin hub routes, or container routes/views
- Creating a new container modeled after `cms-orbit/blog`
- Wiring blog admin menus under SaaS host/container sections

## Workflow

1. Keep `container/container.json` slug aligned with CLI (`blog`).
2. Register `PostEntity` via `EntityRegistry` in `BlogServiceProvider::register()`.
3. Use `Theme::register('blog', 'default', BlogThemeServiceProvider::class)` for the default theme.
4. Instance home route should stay in `container/routes/instance.php` — not host `routes/web.php`.
5. After route/container changes: `php artisan saas:route-cache build`.
6. Translate all admin menu items and entity labels; update `resources/lang/ko.json`.

## Quick instance demo

```bash
php artisan saas:instance create blog "Demo Blog" --subdomain=demo --theme=default
php artisan saas:route-cache build
```

## Forking for a new container

Copy the package structure, rename namespace/slug, publish as a new Composer package depending on
`cms-orbit/core` + `cms-orbit/saas`. Do not fork host project routes or `.env` values into the package.
