<?php

namespace App\Modules\Core\Services;

/**
 * Shared installation state used by both the Laravel application (installed
 * middleware) and the standalone graphical installer.
 */
class InstallerService
{
    public function lockPath(): string
    {
        $lock = (string) config('core.installer.lock_file', 'installed.lock');

        return str_starts_with($lock, DIRECTORY_SEPARATOR) ? $lock : storage_path($lock);
    }

    public function isInstalled(): bool
    {
        return is_file($this->lockPath());
    }

    public function installerUrl(): string
    {
        return (string) config('core.installer.url', '/install.php');
    }

    public function installerEnabled(): bool
    {
        return (bool) config('core.installer.enabled', true);
    }

    /** @return array{version: string|null, installed_at: string|null} */
    public function lockContents(): array
    {
        if (! $this->isInstalled()) {
            return ['version' => null, 'installed_at' => null];
        }

        $data = json_decode((string) file_get_contents($this->lockPath()), true);

        return [
            'version' => $data['version'] ?? null,
            'installed_at' => $data['installed_at'] ?? null,
        ];
    }
}
