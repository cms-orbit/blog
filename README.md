# CMS Orbit Blog

`cms-orbit/blog`는 `cms-orbit/core`와 `cms-orbit/saas` 위에 구축된 블로그 컨테이너 패키지입니다.  
포스트·카테고리·태그 관리, 10개 퍼블릭 테마, 인스턴스별 `/admin` SSO, 자동 프로비저닝, 데모 시딩을 제공합니다.

## 주요 기능

- `container.json` 기반 **자동 프로비저닝** — `syncContainers()` 시 `blog.{appUrl}` 기본 인스턴스 1회 생성
- 호스트 Orbit **SSO** — signed URL로 인스턴스 `/admin` 진입 (포스트·카테고리·태그·테마 설정)
- **10개 테마** — `default` + `themes/blog/` 아래 9개 테마
- 퍼블릭 라우트 — `/`, `/posts/{slug}`, `/categories/{slug}`, `/feed` (RSS)
- **`blog:seed-demos`** — 10개 데모 인스턴스 + 샘플 콘텐츠 시딩

## 설치

```bash
composer require cms-orbit/blog:^4.0
php artisan migrate
php artisan saas:route-cache build
```

## 자동 프로비저닝

`container/container.json`의 `instance.auto_provision`으로 sync 시 `blog.{host}` 인스턴스를 1회 생성합니다.  
동일 서브도메인이 이미 있으면 skip하며, 생성 후 route cache를 rebuild합니다.

## 인스턴스 관리자 (SSO)

Blog Hub 또는 인스턴스 목록의 **블로그 관리** 링크 → signed URL → `/admin/sso` → `/admin`.  
TTL은 `admin.sso_ttl_minutes` (기본 30분). 호스트 도메인에서 `/admin` 직접 접근은 404입니다.

## 테마 & 데모

```bash
php artisan blog:seed-demos
php artisan saas:instance create blog "Demo" --subdomain=demo --theme=magazine
```

## 포함 리소스

- `container/routes/instance.php` — 퍼블릭 라우트
- `container/routes/admin.php` — SSO
- `src/Provisioning/BlogDefaultInstanceProvisioner.php`
- `src/Admin/SignedAdminUrlGenerator.php`
- `config/blog.php`
