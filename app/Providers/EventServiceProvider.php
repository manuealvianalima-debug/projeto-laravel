/**
 * The event listener mappings for the application.
 *
 * @var array
 */
 protected $listen = [
 Registered::class => [
 SendEmailVerificationNotification::class,
 ],
 'MrBrownNL\\Saml2\\Events\\Saml2LoginEvent' => [
 'App\\Listeners\\LoginListener',
 ],
 ];
- URLs:
 login:
 .../saml2/login
 logout:
 .../saml2/logout