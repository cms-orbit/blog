# CMS Orbit Blog Guidelines

`cms-orbit/blog` is a reference **SaaS container package** demonstrating posts admin (`PostEntity`),
`container/container.json`, instance routes, and a default Blade theme.

## Dependencies

- `cms-orbit/core` — admin engine and entities
- `cms-orbit/saas` — container/instance runtime

Install is Composer-only plus standard SaaS commands; no host entity registration required.

## Package independence

- Container metadata ships in `container/container.json` inside this package.
- Migrations load from `container/database/migrations` via the container bootloader — not from host `database/migrations`.
- Theme views live under `container/resources/views` and register through `BlogThemeServiceProvider`.
- Admin hub routes register from `BlogServiceProvider` using Orbit access config — no hardcoded host domain.

## Internationalization (required)

- `PostEntity` labels, blog admin menu, hub screens: `__()` + package `ko.json`.
- Blade welcome view: dynamic `lang` attribute and `{{ __('Blog') }}` title pattern.
- Any future Inertia blog pages: `useT()` + `frontend.json` + `orbit:frontend-sync`.

## Host setup

| Step | Required |
| --- | --- |
| `composer require cms-orbit/blog` | Yes |
| `php artisan migrate` | Yes |
| `php artisan saas:route-cache build` | Yes after install / route changes |
| `php artisan saas:instance create blog "Demo" --subdomain=demo` | To serve an instance |

## Contributing

Use this package as a template for new containers — copy structure, not host-specific branding.
Follow `saas-container-development` and `orbit-package-contribution` skills.
