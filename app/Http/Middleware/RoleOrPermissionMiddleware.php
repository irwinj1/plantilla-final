<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleOrPermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$rolesPermissions): Response
    {
        $user = $request->user(); // auth('api')->user() si usas JWT u otro guard

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Permite pasar roles separados por coma en un solo parámetro
        $rolesPermissions = collect($rolesPermissions)
            ->flatMap(fn($item) => explode(',', $item)) // "Admin,Super Admin" => ["Admin", "Super Admin"]
            ->map(fn($item) => trim($item))
            ->filter()
            ->all();

        $hasAccess = false;

        foreach ($rolesPermissions as $roleOrPermission) {
            if ($user->hasRole($roleOrPermission) || $user->can($roleOrPermission)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a esta ruta.'
            ], 403);
        }

        return $next($request);
    }
}