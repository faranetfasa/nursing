<?php

declare(strict_types=1);

/**
 * Restricts installer access to local requests or requests carrying the
 * deployment access token, so system information and the wizard are never
 * exposed to anonymous visitors.
 */
final class InstallerAccessGuard
{
    private const LOOPBACK = ['127.0.0.1', '::1', '0000:0000:0000:0000:0000:0000:0000:0001'];

    public function __construct(private readonly string $tokenPath)
    {
    }

    public function isAllowed(?string $remoteAddress, mixed $providedToken): bool
    {
        if ($remoteAddress !== null && in_array($remoteAddress, self::LOOPBACK, true)) {
            return true;
        }

        $expected = $this->expectedToken();
        if ($expected === null || ! is_string($providedToken) || $providedToken === '') {
            return false;
        }

        return hash_equals($expected, $providedToken);
    }

    private function expectedToken(): ?string
    {
        $token = getenv('INSTALLER_ACCESS_TOKEN');
        if (is_string($token) && trim($token) !== '') {
            return trim($token);
        }

        if (is_file($this->tokenPath) && is_readable($this->tokenPath)) {
            $contents = file_get_contents($this->tokenPath);
            if (is_string($contents) && trim($contents) !== '') {
                return trim($contents);
            }
        }

        return null;
    }
}
