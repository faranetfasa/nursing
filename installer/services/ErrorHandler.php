<?php

declare(strict_types=1);

final class ErrorHandler
{
    private const FATAL_TYPES = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING];

    private static bool $reported = false;

    public static function register(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');

        set_error_handler(static function (int $severity, string $message, string $file = '', int $line = 0): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler([self::class, 'report']);

        register_shutdown_function(static function (): void {
            $error = error_get_last();
            if ($error === null || ! in_array($error['type'], self::FATAL_TYPES, true)) {
                return;
            }

            self::report(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        });
    }

    public static function report(Throwable $throwable): void
    {
        error_log(sprintf(
            'installer: %s: %s in %s:%d%s%s',
            $throwable::class,
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            PHP_EOL,
            $throwable->getTraceAsString()
        ));

        if (self::$reported) {
            return;
        }

        self::$reported = true;
        self::renderFailurePage();
    }

    private static function renderFailurePage(): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        if (! headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }

        echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>Installer Error</title>'
            . '<body style="font-family:tahoma;padding:3rem;background:#f8fafc">'
            . '<h1>خطای غیرمنتظره در نصب</h1>'
            . '<p>عملیات نصب متوقف شد. جزئیات خطا در Error Log سرور ثبت شده است.</p>'
            . '</body></html>';
    }
}
