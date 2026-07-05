---
name: blog-container-development
description: Extend or fork the cms-orbit/blog reference container — PostEntity, auto provision, SSO admin, themes, seeding, instance routes, and SaaS admin hub. Activate when building blog-like SaaS containers or customizing the sample blog package.
---

# Blog container development

## When to use

- Modifying entities, auto provision, SSO admin, themes, or demo seeding
- Creating a container modeled after `cms-orbit/blog`
- Wiring blog admin menus and instance `/admin` routes

## Workflow

1. Keep `container/container.json` slug aligned with CLI (`blog`).
2. Use `instance.auto_provision` for default `blog.{host}` instance (idempotent).
3. Instance public routes in `container/routes/instance.php`; SSO in `container/routes/admin.php`.
4. Package views use namespace `blog-package::`; theme views use `blog-theme-{name}::`.
5. `ThemeResolver` replaces `blog` namespace with container theme path — do not store package views under `blog::`.
6. After route/container changes: `php artisan saas:route-cache build`.
7. Container runtime routes need `refreshNameLookups()` after dynamic registration (see `ContainerBootloader`).

## SSO admin

- `SignedAdminUrlGenerator` — HMAC URL for host user + instance
- `BlogInstanceAdminAuth` — session keys: `blog_admin_instance_id`, `blog_admin_user_id`, `blog_admin_expires_at`
- Entity CRUD via `BlogInstanceAdminRouteRegistrar` under `/admin`

## Themes & seeding

```bash
php artisan saas:instance create blog "Demo" --subdomain=demo --theme=default
php artisan blog:seed-demos
```

Register container themes from `container/themes/{slug}/ThemeServiceProvider.php` (autodiscovered in `BlogServiceProvider`).

## Tests

- `tests/Feature/Saas/BlogEnhancementTest.php` — provision, SSO, public routes
- `tests/Feature/Saas/BlogEnhancementSeederTest.php` — demo seeder (group: slow)
- `tests/Unit/Blog/ContainerDefinitionTest.php` — container.json schema fields
