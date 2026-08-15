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
        $payload = json_encode($metadata + ['locked_at' => date(DATE_ATOM)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $directory = dirname($this->path);
        if (! is_dir($directory) && ! @mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create installer lock directory "%s".', $directory));
        }

        $written = @file_put_contents($this->path, $payload, LOCK_EX);
        if ($written === false || $written !== strlen($payload)) {
            throw new RuntimeException(sprintf('Unable to write installer lock file "%s".', $this->path));
        }
    }
}
