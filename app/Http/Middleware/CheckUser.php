<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $post = $request->route('anyPost');

        if (! $post) {
            $user = $request->route('user');

            if ($request->user()->username == $user->username) {
                return $next($request);
            }
        } elseif ($request->user()->username == $post->author->username) {
            return $next($request);
        }

        return abort(403);
    }
}