<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            $toast_message = ['You are not authorized to access that page. Please login first!'];
            Session::flash('auth_errors', $toast_message);

            return null;
        } else {
            $toast_message = ['You are not authorized to access that page. Please login first!'];
            Session::flash('auth_errors', $toast_message);
            $this->clearFlashMessages();
            return route('login.page');
        }
    }

    /**
     * Clear flash messages from the session.
     */
    protected function clearFlashMessages()
    {
        if (Session::has('flash.new')) {
            $flashMessages = Session::get('flash.new');
            foreach ($flashMessages as $key => $message) {
                Session::forget('flash.new.' . $key);
            }
        }
    }

}
