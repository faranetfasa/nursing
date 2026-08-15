<?php

namespace App\Modules\Core\Http\Middleware;

use App\Modules\Core\Models\User;
use App\Modules\Core\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route level permission check (specification item 178). Usage:
 *
 *   Route::get(...)->middleware('permission:core.users.view');
 *
 * Several permissions may be passed with "|", the user needs one of them.
 * Denied attempts are written to the audit trail.
 */
class EnsurePermission
{
    public function __construct(private readonly AuditService $audit) {}

    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        $required = collect($permissions)
            ->flatMap(static fn (string $permission): array => explode('|', $permission))
            ->filter()
            ->values();

        if ($user->is_super_admin || $required->isEmpty() || $required->contains(static fn (string $permission): bool => $user->can($permission))) {
            return $next($request);
        }

        $this->audit->log('permission_denied', [
            'module' => 'Core',
            'description' => 'تلاش برای دسترسی غیرمجاز: '.$required->implode(', '),
        ]);

        abort(403, 'شما دسترسی لازم برای این عملیات را ندارید.');
    }
}
