<?php
namespace App\Http\Middleware;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsurePermission {
 public function __construct(private TenantContext $context) {}
 public function handle(Request $request, Closure $next, string $permission): Response {
   $user=$request->user(); abort_unless($user && $user->hasPermission($permission,$this->context->id()),403);
   return $next($request);
 }
}
