<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

/**
 * Single entry point for persisted settings. Nothing in the application should
 * hard code a title, colour, logo or limit: it is read from here with a
 * configuration fallback (config/core.php).
 */
class SettingService
{
    public const CACHE_KEY = 'core.settings';

    public const VERSION_KEY = 'core.settings.version';

    /** Per-organization scopes resolved during the current request. @var array<string, array<string, mixed>> */
    private array $loaded = [];

    /**
     * Settings of a single scope: the organization rows layered on top of the
     * global (organization_id = null) rows.
     */
    public function all(int|string|null $organizationId = 'current'): array
    {
        $organizationId = $this->resolveOrganizationId($organizationId);
        $scope = $organizationId === null ? 'global' : (string) $organizationId;

        if (array_key_exists($scope, $this->loaded)) {
            return $this->loaded[$scope];
        }

        $ttl = (int) config('core.settings.cache_ttl', 3600);

        return $this->loaded[$scope] = Cache::remember(
            self::CACHE_KEY.'.v'.$this->version().'.'.$scope,
            $ttl,
            function () use ($organizationId): array {
                if (! $this->tableExists()) {
                    return [];
                }

                return Setting::query()
                    ->where(function ($query) use ($organizationId): void {
                        $query->whereNull('organization_id');

                        if ($organizationId !== null) {
                            $query->orWhere('organization_id', $organizationId);
                        }
                    })
                    // Global rows first so the organization rows override them.
                    ->orderByRaw('organization_id is null desc')
                    ->get()
                    ->mapWithKeys(static fn (Setting $setting): array => [
                        $setting->group.'.'.$setting->key => $setting->typedValue(),
                    ])
                    ->all();
            }
        );
    }

    public function get(string $key, mixed $default = null, int|string|null $organizationId = 'current'): mixed
    {
        $key = $this->qualify($key);

        return $this->all($organizationId)[$key] ?? $this->fallback($key, $default);
    }

    /**
     * Configuration fallback. The default keys contain dots ("general.app_name"),
     * so the array is read directly instead of through dot notation.
     */
    public function fallback(string $key, mixed $default = null): mixed
    {
        $defaults = (array) config('core.settings.defaults', []);

        return array_key_exists($this->qualify($key), $defaults) ? $defaults[$this->qualify($key)] : $default;
    }

    public function set(string $key, mixed $value, string $type = 'string', array $attributes = []): Setting
    {
        $key = $this->qualify($key);
        [$group, $name] = explode('.', $key, 2);

        $encoded = Setting::encode($value, $type);
        $encrypted = (bool) ($attributes['is_encrypted'] ?? false);

        $setting = Setting::query()->updateOrCreate(
            [
                'organization_id' => $attributes['organization_id'] ?? null,
                'group' => $group,
                'key' => $name,
            ],
            array_merge($attributes, [
                'value' => $encrypted && $encoded !== null ? Crypt::encryptString($encoded) : $encoded,
                'type' => $type,
                'is_encrypted' => $encrypted,
            ])
        );

        $this->flush();

        return $setting;
    }

    /** @param array<string, mixed> $values key => value or key => [value, type] */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (is_array($value) && array_key_exists('value', $value)) {
                $this->set($key, $value['value'], $value['type'] ?? 'string', $value['attributes'] ?? []);

                continue;
            }

            $this->set($key, $value);
        }
    }

    /** Removes a setting from a single scope, mirroring set(). */
    public function forget(string $key, ?int $organizationId = null): void
    {
        $key = $this->qualify($key);
        [$group, $name] = explode('.', $key, 2);

        Setting::query()
            ->where('organization_id', $organizationId)
            ->where('group', $group)
            ->where('key', $name)
            ->delete();

        $this->flush();
    }

    /**
     * Invalidates every scope at once. Cache keys carry a version number because
     * the cache store has no tag support on all drivers.
     */
    public function flush(): void
    {
        $this->loaded = [];
        Cache::forever(self::VERSION_KEY, $this->version() + 1);
    }

    /** Branding values consumed by the layouts (title, logo, colours). */
    public function branding(): array
    {
        return [
            'app_name' => $this->get('general.app_name', config('app.name')),
            'organization_name' => $this->get('general.organization_name', ''),
            'logo' => $this->get('appearance.logo_path'),
            'favicon' => $this->get('appearance.favicon_path'),
            'primary_color' => $this->get('appearance.primary_color'),
            'secondary_color' => $this->get('appearance.secondary_color'),
            'theme' => $this->get('appearance.default_theme'),
            'font' => $this->get('appearance.font_family'),
        ];
    }

    private function qualify(string $key): string
    {
        return str_contains($key, '.') ? $key : 'general.'.$key;
    }

    private function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    /** "current" resolves to the organization of the authenticated user. */
    private function resolveOrganizationId(int|string|null $organizationId): ?int
    {
        if ($organizationId !== 'current') {
            return $organizationId === null ? null : (int) $organizationId;
        }

        $user = Auth::hasUser() ? Auth::user() : null;

        return $user?->organization_id;
    }

    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
