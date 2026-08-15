<?php

namespace App\Support;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Gives every unexpected exception a short, user facing error id and writes it
 * to the log together with the request context (specification item 131).
 */
class ErrorReporter
{
    /** @var array<string, string> */
    private array $ids = [];

    public function report(Throwable $exception): bool
    {
        if (self::isExpected($exception)) {
            return true;
        }

        $errorId = $this->errorId($exception);
        $request = request();

        Log::error('['.$errorId.'] '.$exception->getMessage(), [
            'error_id' => $errorId,
            'exception' => $exception::class,
            'file' => $exception->getFile().':'.$exception->getLine(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'user_id' => auth()->id(),
            'ip' => $request?->ip(),
            'trace' => collect($exception->getTrace())->take(15)->map(
                static fn (array $frame): string => ($frame['file'] ?? '?').':'.($frame['line'] ?? '?')
            )->all(),
        ]);

        // Reporting is fully handled here, the default handler is skipped.
        return false;
    }

    public function errorId(Throwable $exception): string
    {
        $hash = spl_object_hash($exception);

        return $this->ids[$hash] ??= strtoupper('ERR-'.now()->format('ymd').'-'.substr(bin2hex(random_bytes(4)), 0, 8));
    }

    /** Framework exceptions that already have a proper HTTP response. */
    public static function isExpected(Throwable $exception): bool
    {
        return $exception instanceof HttpExceptionInterface
            || $exception instanceof ValidationException
            || $exception instanceof AuthenticationException
            || $exception instanceof AuthorizationException
            || $exception instanceof HttpResponseException
            || $exception instanceof ModelNotFoundException;
    }
}
