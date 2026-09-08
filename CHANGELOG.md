# Changelog

이 문서는 `cms-orbit/blog`의 릴리스 노트를 기록합니다.

## 4.3.1 - 2026-09-08

### 변경

- **`tabuna/breadcrumbs` `^5.0` 을 명시 선언했습니다**: `BlogInstanceAdminRouteRegistrar` 가 `Tabuna\Breadcrumbs\Trail` 을 타입으로 쓰는데 선언이 없어 `cms-orbit/core` 를 통한 전이 의존에 기대고 있었습니다.
- **`blog:seed-demos` 가 실제로 동작합니다**: `DemoBlogInstancesSeeder` 를 패키지 안(`CmsOrbit\Blog\Database\Seeders`)으로 옮겼습니다. 이전에는 호스트 네임스페이스(`Database\Seeders`)의 클래스를 참조하면서 패키지가 그 클래스를 제공하지도, 게시하지도 않았습니다. 즉 README 가 광고하는 이 명령이 **모든 소비자에게 깨져 있었습니다.** 시더는 `CmsOrbit\Blog`·`CmsOrbit\Saas`·`Illuminate` 만 참조하므로 호스트에 있을 이유가 없었습니다.

## 4.3.0 - 2026-09-08

### 변경

- **`cms-orbit/core` `^4.5` → `^4.6`**: core 4.6.0 이 `OrbitAccess` 라우팅 리졸버와 여러 관리자 화면 수정(PostgreSQL 500, strict mode, `orbit:install` 의 OrbitProvider 미등록)을 담고 있습니다.
- **`cms-orbit/saas` `^4.2` → `^4.3`**: saas 4.3.0 이 인스턴스별 관리자 콘솔과 마이그레이션 전 부팅 수정을 담고 있습니다.
- **GitHub Actions 워크플로 추가**: php 8.4·8.5 로 `composer validate` 와 `pint --test` 를 돌리는 `ci.yml`, 그리고 태그의 `composer.json` version 이 태그명과 일치하는지 확인하는 `release-guard.yml`. Packagist 는 불일치 태그를 조용히 무시합니다.
- CI 는 의존성을 설치하지 않고 포매터만 별도로 받아 검사합니다 — 이 패키지가 요구하는 `cms-orbit/saas` 는 유료·비공개 배포라 공개 러너에서 `composer update` 가 성립하지 않습니다.
- **pint 포맷 정규화**: `pint --test` 를 CI 게이트로 걸려면 기존 코드가 설정에 맞아야 해서 함께 정규화했습니다. 순수 포맷 변경이며 동작은 바뀌지 않습니다.

## 4.2.0 - 2026-08-31

### 변경

- **`laravel/framework` `^11.0 || ^12.0 || ^13.0` → `^13.0`**: Laravel 13 전용으로 좁혔습니다.
- **`cms-orbit/core` `^4.4` → `^4.5`**.
- **`cms-orbit/saas` `^4.1` → `^4.2`**.

### 안내

- **왜 좁혔나**: Pest 5 를 채택한 4.4.0(패키지별 4.1.0)부터 `pest-plugin-laravel` 5 가 `laravel/framework ^13.23` 을 요구해, 이 저장소의 테스트가 Laravel 13 으로만 해석됩니다. 즉 Laravel 11·12 호환성을 더 이상 검증할 수 없는 상태로 그 범위를 광고하고 있었습니다. 검증되지 않는 지원 범위를 제약에 남겨두지 않기로 했습니다.
- **Laravel 11·12 사용자는 업그레이드가 필요합니다.** 이번 변경은 실제로 지원 구성을 제거하므로, php 하한 상향과 달리 소비자에게 직접 영향이 있습니다. Laravel 13 으로 올리거나 이전 버전에 머물러야 합니다.

## 4.1.0 - 2026-08-31

### 변경

- **php 제약 `^8.3` → `^8.4`**: php 8.3 환경에서는 더 이상 설치되지 않습니다.
- **`cms-orbit/core` `^4.1` → `^4.4`**.
- **`cms-orbit/saas` `^4.0.8` → `^4.1`**.

### 안내

- **생산 의존은 하나도 바뀌지 않았습니다.** php 하한 상향으로 새로 받게 된 패키지는 Pest 5 계열(`require-dev`)뿐입니다. cms-orbit 전 패키지의 직접 의존 20개를 최신판과 전수 대조했고, 나머지 18개는 이미 php `^8.3` 에서 최신을 받고 있었습니다. 이번 상향은 기능 확보가 아니라 장기 정리 목적입니다.
- **소비자의 Laravel 11·12 지원은 유지됩니다** (`laravel/framework ^11.0 || ^12.0 || ^13.0`).

## 4.0.7 - 2026-08-28

### 개선

- **`cms-orbit/saas`가 사설 저장소 유료 패키지임을 설치 안내에 명시**: blog는 Packagist에 공개되어 있지만 saas를 요구하므로 Packagist 단독으로는 설치되지 않습니다. saas 없이 blog를 쓸 수 없음을 노출하는 의도된 전략입니다. 다만 문서가 `composer require cms-orbit/blog:^4.0`만 안내해서, 실패 시 `cms-orbit/saas could not be found in any version, there may be a typo in the package name`이라는 "패키지가 깨졌다"로 읽히는 메시지만 남았습니다. 사설 저장소 선언과 인증 설정 절차를 추가하고, 그 메시지가 정상 동작임을 명시했습니다.
- Packagist 첫 화면에서 유료 의존이 드러나도록 `description`을 수정했습니다.

### 수정

- **요구사항의 낡은 버전 수정**: `cms-orbit/core` `4.0.2` → `^4.1`, `cms-orbit/saas` `4.0.3` → `^4.0.8`.

### 안내

- **Composer 동작 주의**: Composer는 의존 패키지가 선언한 `repositories`를 무시하고 **루트 패키지의 것만** 읽습니다. 따라서 구매자가 자기 `composer.json`에 사설 저장소를 직접 추가해야 합니다.

## 4.0.6 - 2026-08-28

### 수정

- **php 제약 `^8.2` → `^8.3` 복구**: 게시된 태그는 모두 `^8.3`이었으나 main에서 `^8.2`로 내려가 있었습니다. Laravel 13은 php `^8.3`을 요구하므로 `php ^8.2` + `laravel/framework ^13` 조합은 php 8.2 환경에서 조용히 Laravel 11을 설치합니다.
- **`laravel/pint` `^1.14` → `^1.30`** (1.30이 php `^8.3`을 요구).

### 추가

- **릴리스 파이프라인 도입**: `.githooks/pre-push`가 composer.json의 `version` 필드와 태그명이 어긋난 태그의 푸시를 차단합니다. `cms-orbit/core`의 `4.0.8` 태그가 `version: 4.0.7`로 만들어져 Packagist가 아무 오류 없이 그 태그를 무시했고, 4.0.8이 게시되지 않은 사실을 아무도 알지 못한 사고가 있었습니다. `bin/release <버전>`이 version 갱신·검증·커밋·태그·푸시를 한 동작으로 묶어 드리프트를 원천 차단하고, `cms-orbit/*` 의존이 실제로 Packagist에 게시되어 있는지 Composer 리졸버로 확인합니다. 클론 후 `composer install` 시 `core.hooksPath`가 자동 설정됩니다.

## 4.0.5 - 2026-07-13

### 변경

- `cms-orbit/core`·`cms-orbit/saas` 의존성을 정확한 버전(각각 `4.0.2`·`4.0.3`)에서 `^4.0`으로 완화했습니다. 이제 core/saas 최신 패치와 함께 설치·업데이트할 때 버전 충돌이 발생하지 않습니다.

## 4.0.3 - 2026-07-06

### 추가

- `BlogDatabaseConnection` 헬퍼와 `blog.database.connection`(`BLOG_DB_CONNECTION`) 설정을 추가했습니다.

### 개선

- SSO·프로비저닝·PostSync에서 호스트 DB 연결 해석을 `BlogDatabaseConnection`/`HostConnection`으로 통일했습니다.
- `PostSyncService::instanceDatabaseExists()`가 `HostConnection::isSqlite()`를 사용합니다.

### 변경

- `cms-orbit/core` `4.0.2`, `cms-orbit/saas` `4.0.3`에 맞춰 의존성을 정렬했습니다.

## 4.0.2 - 2026-07-05

### 추가

- `blog.{appUrl}` 컨테이너 도메인 + path 기반 인스턴스 URL (`/{instanceHost}/`, `/{postSlug}`)을 정식 지원합니다.
- Blog Hub(`blog.{appUrl}/`)에서 인스턴스 목록·생성 CTA·게시 글 수를 표시합니다.
- Orbit **포스팅** 화면에 모든 워크스페이스 글을 1depth 테이블로 통합 표시합니다.
- `container/themes/` 아래 10개 테마 + `_base` 공통 레이아웃, showcase 4종(editorial/magazine/photo/dark)을 추가했습니다.
- `/about` 정적 페이지 라우트와 `PageController`를 추가했습니다.

### 개선

- 테마를 `container/themes`로 이전하고 ThemeServiceProvider autodiscover 등록으로 admin 테마 선택을 안정화했습니다.
- `PostSyncService::listAllPosts()`, `publicPostUrl()` suffix 수정(`/{slug}`).
- `BlogViewResolver`가 stub 테마 뷰를 감지하면 `_base`/package fallback을 사용합니다.

### 변경

- `cms-orbit/saas` `4.0.2` 필수 (path 별칭 subdomain 리다이렉트 미들웨어).

## 4.0.1 - 2026-07-05

### 변경

- `cms-orbit/core` `4.0.1`, `cms-orbit/saas` `4.0.1`에 맞춰 의존성을 정렬했습니다.

## 4.0.0 - 2026-07-05

### 변경

- 4.0.0 정식 릴리스로 승격했습니다. 컨테이너 `posts` 테이블은 단일 create 마이그레이션으로 유지됩니다.

### 추가

- Laravel Boost 가이드라인·스킬(`blog-container-development`)을 추가했습니다.

### 개선

- README **호스트 설정** 표로 컨테이너 패키지 설치·route cache·인스턴스 생성 단계를 정리했습니다.
- README에 Laravel Boost 연동과 설치 버전(`^4.0`)을 정리했습니다.
- `cms-orbit/core` `4.0.0`, `cms-orbit/saas` `4.0.0-beta5`에 맞춰 의존성을 정렬했습니다.
- 블로그 허브·컨테이너 관련 관리자 메뉴 한글팩이 PHP `__()` 경로에서도 안정적으로 로드되도록 번역 경로 등록 시점을 `register()`로 앞당겼습니다.
- 블로그 컨테이너 아이콘과 허브 메뉴 정렬을 SaaS 컨테이너 섹션과 맞췄습니다.

### 수정

- 코어 부트 단계에서 번역 캐시가 먼저 고정되면서 블로그 허브·컨테이너 상세 메뉴 문구가 영어로 남을 수 있던 문제를 수정했습니다.

## 4.0.0-beta4 - 2026-07-04

### 추가

- 블로그 허브/관리 흐름과 컨테이너 메타데이터 확장을 기준으로 블로그 패키지를 보다 독립적인 SaaS 워크스페이스처럼 다룰 수 있는 기반을 보강했습니다.

### 개선

- 이제 더 이상 블로그 웰컴 화면 제목과 몇몇 관리자 레이블이 영어로 남아 있지 않고, 한글팩과 애플리케이션 로케일을 따라 자연스럽게 맞춰지도록 개선되었습니다.
- `cms-orbit/core`, `cms-orbit/saas`와의 버전선을 `4.0.0-beta4`로 맞춰 블로그 컨테이너 패키지를 동일 릴리스 선에서 관리하기 쉬워졌습니다.

### 수정

- `ID` 같은 기본 관리자 레이블이 패키지 로컬 한글팩에 비어 있어 패키지 단독 기준으로는 영어 노출 가능성이 남아 있던 부분을 보완했습니다.
- 블로그 인스턴스 웰컴 뷰가 고정 `en` 언어와 영문 타이틀을 사용하던 문제를 수정했습니다.

## 4.0.0-beta3 - 2026-07-04

### 안내

- 블로그 컨테이너를 어떻게 설치하고, 어떤 인스턴스 커맨드로 띄우며, 어떤 리소스가 함께 들어오는지 README에 정리했습니다.

### 개선

- 이제 더 이상 `container.json`, 테마 프로바이더, 인스턴스 라우트 파일을 각각 열어보지 않아도, 블로그 컨테이너가 어떤 구조로 동작하는지 README 한 곳에서 파악할 수 있도록 개선되었습니다.
- `cms-orbit/core`, `cms-orbit/saas`와의 릴리스 선을 `4.0.0-beta3`로 맞춰 블로그 컨테이너 의존성 조합을 더 명확하게 유지할 수 있게 했습니다.

### 수정

- 샘플 컨테이너 패키지의 진입점이 문서에 정리되어 있지 않아 실제 사용 시 시작 지점을 찾기 어려웠던 문제를 보완했습니다.
- 릴리스 기록이 없어 어떤 버전에서 블로그 컨테이너 테마/관리자 훅을 기준 삼아야 하는지 파악하기 어렵던 부분을 정리했습니다.

## 4.0.0-beta2 - 2026-07-04

### 추가

- `blog` 컨테이너 정의와 기본 인스턴스 라우트, 웰컴 뷰, 포스트 마이그레이션을 포함한 샘플 컨테이너 구조를 추가했습니다.
- Orbit 관리자에 블로그 포스트를 노출하는 `PostEntity`를 추가했습니다.
- 블로그 컨테이너의 기본 테마 프로바이더와 테마 등록 훅을 추가했습니다.

### 개선

- 이제 더 이상 블로그 샘플을 위해 컨테이너 프로바이더와 테마 등록 코드를 별도로 묶지 않아도, 패키지 설치만으로 SaaS 컨테이너 예제를 바로 띄울 수 있도록 개선되었습니다.

### 수정

- 컨테이너 예제에서 관리자 섹션 연결 지점이 드러나지 않아 실제 컨테이너 패키지 제작 시 복제하기 어려웠던 부분을 보완했습니다.
