<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    
    // https://stackoverflow.com/questions/31627477/access-laravel-app-from-android-app-with-csrf-token
    protected $except = [
        '*'
        // 'mobile/*',
        // 'news/articles',
    ];
}
