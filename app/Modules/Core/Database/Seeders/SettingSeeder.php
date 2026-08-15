<?php

namespace App\Modules\Core\Database\Seeders;

use App\Modules\Core\Models\Setting;
use App\Modules\Core\Services\SettingService;
use Illuminate\Database\Seeder;

/**
 * Writes the initial settings rows from the configuration defaults so every
 * value is editable from the interface instead of being hard coded.
 */
class SettingSeeder extends Seeder
{
    /** @var array<string, array{type: string, label: string, is_public?: bool}> */
    private array $meta = [
        'general.app_name' => ['type' => 'string', 'label' => 'عنوان سامانه', 'is_public' => true],
        'general.organization_name' => ['type' => 'string', 'label' => 'نام سازمان', 'is_public' => true],
        'general.timezone' => ['type' => 'string', 'label' => 'منطقه زمانی'],
        'general.locale' => ['type' => 'string', 'label' => 'زبان پیش‌فرض'],
        'general.calendar' => ['type' => 'string', 'label' => 'تقویم'],
        'general.currency' => ['type' => 'string', 'label' => 'واحد پول'],
        'general.support_phone' => ['type' => 'string', 'label' => 'تلفن پشتیبانی', 'is_public' => true],
        'appearance.primary_color' => ['type' => 'color', 'label' => 'رنگ اصلی', 'is_public' => true],
        'appearance.secondary_color' => ['type' => 'color', 'label' => 'رنگ فرعی', 'is_public' => true],
        'appearance.default_theme' => ['type' => 'string', 'label' => 'حالت پیش‌فرض نمایش', 'is_public' => true],
        'appearance.font_family' => ['type' => 'string', 'label' => 'فونت', 'is_public' => true],
        'appearance.logo_path' => ['type' => 'file', 'label' => 'لوگو', 'is_public' => true],
        'appearance.favicon_path' => ['type' => 'file', 'label' => 'فاوآیکون', 'is_public' => true],
        'appearance.sidebar_collapsed' => ['type' => 'boolean', 'label' => 'منوی جمع‌شده'],
        'security.session_lifetime' => ['type' => 'integer', 'label' => 'مدت اعتبار نشست (دقیقه)'],
        'security.force_password_change_days' => ['type' => 'integer', 'label' => 'اجبار تغییر رمز عبور (روز)'],
    ];

    public function run(): void
    {
        $settings = app(SettingService::class);

        foreach ((array) config('core.settings.defaults', []) as $key => $value) {
            $meta = $this->meta[$key] ?? ['type' => 'string', 'label' => $key];

            if (Setting::query()->where('group', explode('.', $key)[0])->where('key', explode('.', $key, 2)[1])->exists()) {
                continue;
            }

            $settings->set($key, $value, $meta['type'], [
                'label' => $meta['label'],
                'is_public' => $meta['is_public'] ?? false,
            ]);
        }

        $settings->flush();
    }
}
