<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Admin;

use App\Models\User;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use CmsOrbit\Blog\Support\BlogContainerDomain;
use CmsOrbit\Saas\Enums\EndpointType;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use Illuminate\Contracts\Auth\Authenticatable;

class SignedAdminUrlGenerator
{
    public function __construct(protected BlogContainerConfig $config) {}

    public function for(Instance $instance, Authenticatable $user): string
    {
        $endpoint = $instance->primaryEndpoint();

        if (! $endpoint instanceof RouteEndpoint) {
            throw new \RuntimeException('Instance has no primary endpoint.');
        }

        $ttl = $this->config->ssoTtlMinutes();
        $expires = now()->addMinutes($ttl)->timestamp;
        $payload = implode('|', [
            (string) $instance->getKey(),
            (string) $user->getAuthIdentifier(),
            (string) $expires,
        ]);
        $signature = hash_hmac('sha256', $payload, (string) config('app.key'));

        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'http';
        $host = $endpoint->normalizedValue();
        $pathPrefix = '';

        if ($endpoint->type === EndpointType::Path) {
            $host = BlogContainerDomain::host();
            $pathPrefix = '/'.trim($endpoint->value, '/');
        }

        $query = http_build_query([
            'instance' => $instance->getKey(),
            'user' => $user->getAuthIdentifier(),
            'expires' => $expires,
            'signature' => $signature,
        ]);

        return "{$scheme}://{$host}{$pathPrefix}/".trim($this->config->adminPrefix(), '/')."/sso?{$query}";
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function validate(array $query): bool
    {
        $instanceId = (string) ($query['instance'] ?? '');
        $userId = (string) ($query['user'] ?? '');
        $expires = (int) ($query['expires'] ?? 0);
        $signature = (string) ($query['signature'] ?? '');

        if ($instanceId === '' || $userId === '' || $expires === 0 || $signature === '') {
            return false;
        }

        if ($expires < now()->timestamp) {
            return false;
        }

        $context = instance_context();

        if ($context === null || $context->instance->getKey() !== $instanceId) {
            return false;
        }

        $payload = implode('|', [$instanceId, $userId, (string) $expires]);
        $expected = hash_hmac('sha256', $payload, (string) config('app.key'));

        return hash_equals($expected, $signature);
    }
}
