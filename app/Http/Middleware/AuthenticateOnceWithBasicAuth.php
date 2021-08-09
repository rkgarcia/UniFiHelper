<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class AuthenticateOnceWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $username = @$_SERVER['PHP_AUTH_USER'];
        $password = @$_SERVER['PHP_AUTH_PW'];
        $has_credentials = !(empty($username) && empty($password));
        $user_valid = false;
        if($has_credentials) {
            $user = User::where('name', $username)->first();
            if(!is_null($user)) {
                if(password_verify($password, $user->x_shadow)) {
                    $user_valid = true;
                }
            }
        }
        $is_not_authenticated = (!$has_credentials || !$user_valid);
        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit;
        }
        return $next($request);
    }
}
