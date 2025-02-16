<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Rbac\RoleUserModel;
use App\Models\Rbac\RoleModel;
use App\Traits\MyAlert;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    use MyAlert;

    public function handle(Request $request, Closure $next): Response
    {
        $getRole = RoleUserModel::where('user_id', Auth::user()->id)->with('role')
        ->get()
        ->map(function ($roleUser) {
            return [
                'apps_id' => $roleUser->role->apps_id,
                'role_name' => $roleUser->role->name,
            ];
        });
        // ->pluck('role.apps_id');
        $checkApps = false;

        foreach ($getRole as $roleUser) {
            // dd($roleUser);
            if($roleUser['apps_id'] == 1) {
                $checkApps = true;
                Session::put('role', $roleUser['role_name']);
            }
        }

        if(!$checkApps) {
            Session::flush();
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
