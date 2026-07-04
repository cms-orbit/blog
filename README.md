# CMS Orbit Blog

`cms-orbit/blog`는 `cms-orbit/core`와 `cms-orbit/saas` 위에 구축된 샘플 블로그 컨테이너 패키지입니다.  
블로그 포스트 관리자 엔티티, 컨테이너 정의, 기본 테마 뷰와 인스턴스 라우트를 함께 제공해 SaaS 컨테이너 예제로 활용하기 좋습니다.

## 주요 기능

- `cms-orbit/core` 기반 `PostEntity` 관리자 화면
- `cms-orbit/saas` 기반 컨테이너 패키지 예제
- `container/container.json`을 통한 컨테이너 정의
- 기본 블로그 테마 서비스 프로바이더와 뷰 제공
- 인스턴스 홈 라우트 샘플 제공

## 설치

```bash
composer require cms-orbit/blog:^4.0@beta
php artisan migrate
php artisan saas:route-cache build
```

패키지는 내부적으로 다음 의존성을 사용합니다.

- `cms-orbit/core`
- `cms-orbit/saas`

## 어떻게 동작하나요?

### 1. 컨테이너 정의

`container/container.json`은 이 패키지가 `blog` 컨테이너임을 선언합니다.  
격리 엔진은 `multi_database`, 라우팅 방식은 `domain`, `subdomain`, `path`를 모두 지원합니다.

### 2. 관리자 엔티티

`PostEntity`가 Orbit 관리자에 블로그 포스트 CRUD를 등록합니다.  
기본 필드는 `title`, `body`이며, 컨테이너 전용 관리자 섹션 키를 사용해 호스트/컨테이너 메뉴를 분리합니다.

### 3. 기본 테마

`BlogThemeServiceProvider`가 `container/resources/views`를 로드합니다.  
인스턴스 루트(`/`)는 기본적으로 `blog::welcome` 뷰를 렌더링합니다.

## 빠른 시작

### 1. 인스턴스 생성

```bash
php artisan saas:instance create blog "Demo Blog" --subdomain=demo --theme=default
```

### 2. 경로 기반 인스턴스 예시

```bash
php artisan saas:instance create blog "Docs Blog" --path=docs --theme=default
```

### 3. 테마 확장

필요하면 `cms-orbit/saas`의 테마 스캐폴드 커맨드로 별도 테마를 만들 수 있습니다.

```bash
php artisan saas:theme make blog dark
```

## 포함 리소스

- `container/container.json`
- `container/routes/instance.php`
- `container/resources/views/welcome.blade.php`
- `container/database/migrations/*`
- `src/Entities/PostEntity.php`

## 운영 팁

- 컨테이너 메타데이터나 라우트 구조를 바꿨다면 `php artisan saas:route-cache build`를 다시 실행하는 편이 좋습니다.
- 이 패키지는 SaaS 컨테이너 예제 성격이 강하므로, 실제 운영 패키지를 만들 때는 이 구조를 복사해 시작하면 빠릅니다.

## License

Proprietary
