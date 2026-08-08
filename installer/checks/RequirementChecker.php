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
        $items[] = $this->recommendedNumber('Max Execution Time', (int) ini_get('max_execution_time'), 120);
        $items[] = [
            'name' => 'Installer Storage Permission',
            'status' => is_writable(__DIR__ . '/../storage') ? 'pass' : 'fail',
            'message' => is_writable(__DIR__ . '/../storage') ? 'قابل نوشتن است' : 'Permission نوشتن ندارد',
        ];
        $items[] = [
            'name' => 'Disk Free Space',
            'status' => disk_free_space(__DIR__) > 500 * 1024 * 1024 ? 'pass' : 'warn',
            'message' => 'حداقل پیشنهادی 500MB فضای آزاد است',
        ];

        return $items;
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
        $bytes = $this->toBytes((string) $value);
        return [
            'name' => $name,
            'status' => $bytes >= $minimum || $bytes === -1 ? 'pass' : 'warn',
            'message' => 'مقدار فعلی: ' . (string) $value,
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

    private function toBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }
        $unit = strtolower(substr($value, -1));
        $number = (int) $value;
        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
