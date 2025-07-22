<?php

namespace App\Http;

class Kernel
{
    protected $middlewareAliases = [
        // ... autres middlewares
        'frontend.auth' => \App\Http\Middleware\FrontendAuth::class,
    ];
}
