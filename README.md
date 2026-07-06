# CMS Orbit Blog

`cms-orbit/blog`는 `cms-orbit/core`와 `cms-orbit/saas` 위에 구축된 **블로그 SaaS 컨테이너** 패키지입니다.  
포스트·카테고리·태그 관리, 10개 퍼블릭 테마, 인스턴스별 `/admin` SSO, 통합 Posting, 자동 프로비저닝, 데모 시딩을 제공합니다.

## 무엇을 제공하나요?

- **`container.json` 기반 SaaS 컨테이너** — `multi_database` 격리, domain/subdomain/path 라우팅
- **Blog Hub** — 호스트 Orbit에서 인스턴스 목록·생성·Posting 통합 관리
- **Posting / Sync** — 여러 인스턴스의 글을 호스트에서 통합 조회·편집·인스턴스 간 복제
- **인스턴스 SSO Admin** — signed URL로 인스턴스 `/admin` 진입 (포스트·카테고리·태그·테마 설정)
- **10개 퍼블릭 테마** — `default`, `minimal`, `editorial`, `magazine`, `photo`, `dark`, `classic`, `corporate`, `playful`, `neon`
- **퍼블릭 라우트** — `/`, `/about`, `/posts/{slug}`, `/categories/{slug}`, `/feed` (RSS)
- **Public Hub** — `blog.{host}/` 컨테이너 도메인에서 인스턴스 목록·생성 CTA
- **`blog:seed-demos`** — 10개 데모 인스턴스 + 샘플 콘텐츠 시딩

## 요구사항

- PHP `^8.3`
- `cms-orbit/core` `4.0.2`
- `cms-orbit/saas` `4.0.3`
- Laravel `^11.0 || ^12.0 || ^13.0`

## 설치

```bash
composer require cms-orbit/blog:^4.0
php artisan migrate
php artisan saas:route-cache build
```

`cms-orbit/core`(`orbit:install`)와 `cms-orbit/saas`가 먼저 설치·설정되어 있어야 합니다.

## Laravel Boost

이 패키지는 `resources/boost/guidelines/blog.md`, `resources/boost/skills/blog-container-development/`를 제공합니다.

- Boost **최초 설정**: 호스트에서 `php artisan boost:install` 1회
- **이후**: `orbit:install` / `orbit:sync`가 이 패키지를 Boost에 등록하고 `boost:update` 실행 (Boost가 이미 설정된 경우)

## 호스트 설정

| 작업 | 필수 여부 | 설명 |
| --- | --- | --- |
| `composer require cms-orbit/blog` + `migrate` | **필수** | |
| `php artisan saas:route-cache build` | **필수** | 컨테이너·엔드포인트 변경 후 재실행 |
| `container.json`의 `auto_provision.enabled` | 선택 | `true`로 바꾸면 sync 시 `blog.{host}` 인스턴스 1회 생성 |
| `config/saas.php`의 `host_domains` | 운영 **필수** | SaaS 패키지 설정 |
| 호스트 `routes/*.php` 수동 편집 | **불필요** | 컨테이너 패키지가 라우트 제공 |

## 핵심 개념

### Container

`container/container.json`이 블로그 앱 종류를 선언합니다.

- **격리 엔진** — `multi_database` (인스턴스별 DB)
- **라우팅** — domain, subdomain, path 지원 (`path_first: true`)
- **테마** — 인스턴스 생성 시 선택 가능 (`theme_selectable: true`)

### 자동 프로비저닝

`instance.auto_provision`으로 `syncContainers()` 시 기본 인스턴스를 1회 생성할 수 있습니다.

> **기본값은 `enabled: false`입니다.** 사용하려면 `container/container.json`에서 `enabled: true`로 변경하세요.

```json
"auto_provision": {
  "enabled": true,
  "name": "Blog",
  "subdomain": "blog",
  "theme": "default"
}
```

동일 서브도메인이 이미 있으면 skip하며, 생성 후 route cache를 rebuild합니다.

### 인스턴스 관리자 (SSO)

Blog Hub 또는 인스턴스 목록의 **블로그 관리** 링크 → signed URL → `/admin/sso` → `/admin`.

- TTL: `admin.sso_ttl_minutes` (기본 30분)
- 호스트 도메인에서 `/admin` 직접 접근은 404 (인스턴스 컨텍스트에서만 동작)

### Posting (통합 글 관리)

호스트 Orbit **Blog → Posting**에서 모든 블로그 인스턴스의 글을 한곳에서 조회·편집할 수 있습니다.  
`PostSyncService`로 인스턴스 간 포스트 복제도 지원합니다.

## 빠른 시작

### 1. 인스턴스 생성

```bash
php artisan saas:instance create blog "Demo Blog" --subdomain=demo --theme=magazine
php artisan saas:route-cache build
```

경로 기반 인스턴스:

```bash
php artisan saas:instance create blog "Docs Blog" --path=docs --theme=default
```

### 2. 데모 시딩

```bash
php artisan blog:seed-demos
```

> 호스트 앱에 `Database\Seeders\DemoBlogInstancesSeeder`가 있어야 합니다. 10개 테마별 데모 인스턴스와 샘플 콘텐츠를 생성합니다.

### 3. 인스턴스 관리자 접속

Orbit **Blog Hub** → 인스턴스 **블로그 관리** 링크 → SSO → `/admin`에서 포스트·카테고리·태그·테마 설정을 관리합니다.

## 설정 (`config/blog.php`)

| 키 | ENV | 기본값 | 설명 |
| --- | --- | --- | --- |
| `posts_per_page` | — | `12` | 퍼블릭 목록 페이지당 글 수 |
| `database.connection` | `BLOG_DB_CONNECTION` / `BLOG_DB_DRIVER` | — | 블로그 DB 연결 (미지정 시 SaaS 격리 엔진 사용) |
| `sso.enabled` | — | `true` | SSO 관리자 링크 활성화 |

## 관리자 화면

| 화면 | 라우트 이름 | 권한 |
| --- | --- | --- |
| Blog Hub | `orbit.blog.index` | `blog.dashboard` |
| 인스턴스 목록 | `orbit.blog.instances.index` | `blog.dashboard` |
| 인스턴스 생성 | `orbit.blog.instances.create` | `blog.dashboard` |
| 인스턴스 상세 | `orbit.blog.instances.view` | `blog.dashboard` |
| Posting 목록 | `orbit.blog.posting.index` | `blog.dashboard` |
| Posting (인스턴스) | `orbit.blog.posting.instance` | `blog.dashboard` |
| Posting (글 상세) | `orbit.blog.posting.posts.view` | `blog.dashboard` |
| Posting (글 편집) | `orbit.blog.posting.posts.edit` | `blog.dashboard` |

엔티티 CRUD(`blog-posts`, `blog-categories`, `blog-tags`)는 인스턴스 SSO Admin 또는 Posting에서 접근하며, `blog.entities.{uriKey}.*` 권한으로 제어됩니다.

## 퍼블릭 라우트 (인스턴스)

| 경로 | 설명 |
| --- | --- |
| `/` | 홈 (최근 글 목록) |
| `/about` | 소개 페이지 |
| `/{slug}` | 글 상세 |
| `/categories/{slug}` | 카테고리별 목록 |
| `/feed` | RSS 피드 |
| `/admin/sso` | SSO 진입 (signed URL) |
| `/admin` | 인스턴스 관리자 CRUD |

## Public Hub (컨테이너 도메인)

`blog.{appHost}/` — 컨테이너 기본 서브도메인에서 인스턴스 목록과 생성 CTA를 제공합니다.

## 테마

| slug | 스타일 |
| --- | --- |
| `default` | 기본 블로그 |
| `minimal` | 미니멀 |
| `editorial` | 에디토리얼 |
| `magazine` | 매거진 |
| `photo` | 포토 중심 |
| `dark` | 다크 모드 |
| `classic` | 클래식 |
| `corporate` | 기업형 |
| `playful` | 캐주얼 |
| `neon` | 네온 |

테마는 `container/themes/{slug}/` 아래 Blade 뷰 + Vite 빌드로 구성됩니다.

## 포함 리소스

- `container/routes/instance.php` — 퍼블릭·Admin 라우트
- `container/routes/admin.php` — SSO
- `container/database/migrations/` — posts, categories, tags, blog_settings
- `src/Provisioning/BlogDefaultInstanceProvisioner.php` — 자동 프로비저닝
- `src/Admin/SignedAdminUrlGenerator.php` — SSO URL 생성
- `src/Services/PostSyncService.php` — 인스턴스 간 포스트 동기화

## License

Proprietary
