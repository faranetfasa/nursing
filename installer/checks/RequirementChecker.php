<?php

declare(strict_types=1);

final class RequirementChecker
{
    /** @return array<int, array{name:string,status:string,message:string}> */
    public function check(): array
    {
        $items = [];
        $items[] = $this->version('PHP Version', PHP_VERSION, '8.2.0');

        foreach (['pdo','pdo_mysql','openssl','mbstring','fileinfo','json','ctype','xml','tokenizer','bcmath','gd','zip','curl'] as $extension) {
            $items[] = [
                'name' => strtoupper($extension),
                'status' => extension_loaded($extension) ? 'pass' : 'fail',
                'message' => extension_loaded($extension) ? 'فعال است' : 'باید فعال شود',
            ];
        }

        $items[] = $this->recommendedBytes('Memory Limit', ini_get('memory_limit'), 256 * 1024 * 1024);
        $items[] = $this->recommendedBytes('Upload Limit', ini_get('upload_max_filesize'), 20 * 1024 * 1024);
        $items[] = $this->recommendedBytes('Post Size', ini_get('post_max_size'), 20 * 1024 * 1024);
        $items[] = $this->executionTime();
        $items[] = $this->storagePermission(__DIR__ . '/../storage');
        $items[] = $this->diskSpace(__DIR__, 500 * 1024 * 1024);

        return $items;
    }

    /** @return array{name:string,status:string,message:string} */
    private function executionTime(): array
    {
        $value = ini_get('max_execution_time');
        if ($value === false) {
            return [
                'name' => 'Max Execution Time',
                'status' => 'warn',
                'message' => 'مقدار max_execution_time قابل خواندن نیست',
            ];
        }

        return $this->recommendedNumber('Max Execution Time', (int) $value, 120);
    }

    /** @return array{name:string,status:string,message:string} */
    private function storagePermission(string $directory): array
    {
        if (! is_dir($directory)) {
            return [
                'name' => 'Installer Storage Permission',
                'status' => 'fail',
                'message' => 'مسیر storage وجود ندارد و باید ساخته شود',
            ];
        }

        $writable = is_writable($directory);

        return [
            'name' => 'Installer Storage Permission',
            'status' => $writable ? 'pass' : 'fail',
            'message' => $writable ? 'قابل نوشتن است' : 'Permission نوشتن ندارد',
        ];
    }

    /** @return array{name:string,status:string,message:string} */
    private function diskSpace(string $path, int $minimum): array
    {
        $free = @disk_free_space($path);
        if ($free === false) {
            return [
                'name' => 'Disk Free Space',
                'status' => 'warn',
                'message' => 'میزان فضای آزاد دیسک قابل تشخیص نیست',
            ];
        }

        return [
            'name' => 'Disk Free Space',
            'status' => $free > $minimum ? 'pass' : 'warn',
            'message' => 'حداقل پیشنهادی 500MB فضای آزاد است',
        ];
    }

    /** @return array{name:string,status:string,message:string} */
    private function version(string $name, string $current, string $minimum): array
    {
        return [
            'name' => $name,
            'status' => version_compare($current, $minimum, '>=') ? 'pass' : 'fail',
            'message' => "Current: {$current}, Required: {$minimum}+",
        ];
    }

    /** @return array{name:string,status:string,message:string} */
    private function recommendedBytes(string $name, string|false $value, int $minimum): array
    {
        if ($value === false) {
            return [
                'name' => $name,
                'status' => 'warn',
                'message' => 'مقدار این تنظیم در php.ini قابل خواندن نیست',
            ];
        }

        $bytes = $this->toBytes($value);
        if ($bytes === null) {
            return [
                'name' => $name,
                'status' => 'warn',
                'message' => 'مقدار فعلی قابل تفسیر نیست: ' . $value,
            ];
        }

        return [
            'name' => $name,
            'status' => $bytes >= $minimum || $bytes === -1 ? 'pass' : 'warn',
            'message' => 'مقدار فعلی: ' . $value,
        ];
    }

    /** @return array{name:string,status:string,message:string} */
    private function recommendedNumber(string $name, int $value, int $minimum): array
    {
        return [
            'name' => $name,
            'status' => $value === 0 || $value >= $minimum ? 'pass' : 'warn',
            'message' => "مقدار فعلی: {$value}",
        ];
    }

    private function toBytes(string $value): ?int
    {
        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }
        if (preg_match('/^(\d+)\s*([kmg]?)b?$/i', $value, $matches) !== 1) {
            return null;
        }

        $number = (int) $matches[1];

        return match (strtolower($matches[2])) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
