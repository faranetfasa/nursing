<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Setting;
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

    /** @var array<string, mixed>|null */
    private ?array $loaded = null;

    public function all(): array
    {
        if ($this->loaded !== null) {
            return $this->loaded;
        }

        $ttl = (int) config('core.settings.cache_ttl', 3600);

        return $this->loaded = Cache::remember(self::CACHE_KEY, $ttl, function (): array {
            if (! $this->tableExists()) {
                return [];
            }

            return Setting::query()->get()->mapWithKeys(static fn (Setting $setting): array => [
                $setting->group.'.'.$setting->key => $setting->typedValue(),
            ])->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->qualify($key);

        return $this->all()[$key] ?? $this->fallback($key, $default);
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

    public function forget(string $key): void
    {
        $key = $this->qualify($key);
        [$group, $name] = explode('.', $key, 2);

        Setting::query()->where('group', $group)->where('key', $name)->delete();

        $this->flush();
    }

    public function flush(): void
    {
        $this->loaded = null;
        Cache::forget(self::CACHE_KEY);
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

    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
