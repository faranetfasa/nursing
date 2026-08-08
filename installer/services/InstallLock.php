<?php

declare(strict_types=1);

final class InstallLock
{
    public function __construct(private readonly string $path)
    {
    }

    public function isLocked(): bool
    {
        return is_file($this->path);
    }

    public function lock(array $metadata): void
    {
        $payload = json_encode($metadata + ['locked_at' => date(DATE_ATOM)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            throw new RuntimeException('Unable to encode installer lock metadata.');
        }
        file_put_contents($this->path, $payload, LOCK_EX);
    }
}
