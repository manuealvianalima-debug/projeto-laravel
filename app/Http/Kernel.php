protected $routeMiddleware = [
    // ... outros
    'admin' => \App\Http\Middleware\Admin::class,

];
protected $middlewareGroups = [
    'web' => [
    ...
    ],
    'api' => [
    ...
    ],
    'saml' => [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
],
];
