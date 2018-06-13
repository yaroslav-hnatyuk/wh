<?php

use Silex\Application;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\ServicesLoader;
use App\RoutesLoader;
use Carbon\Carbon;

date_default_timezone_set('Europe/Kiev');

//accepting JSON
$app->before(function (Request $request) {
    // Get route and check RBAC -> $request->attributes->get('_route'));
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->register(new \Euskadi31\Silex\Provider\CorsServiceProvider);

$app->register(new ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => ROOT_PATH . '/web/views',
));

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => '%s?version=%s',
    'assets.base_path' => 'views/'
));

$app->register(new DoctrineServiceProvider(), array(
    "db.options" => $app["db.options"]
));

$app->register(new HttpCacheServiceProvider(), array("http_cache.cache_dir" => ROOT_PATH . "/storage/cache",));

$app->register(new Silex\Provider\SessionServiceProvider());

$app['app.token_authenticator'] = function ($app) {
    return new App\Security\TokenAuthenticator($app['security.encoder_factory']);
};

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
        ),
        'secured' => array(
            'pattern' => '^.*$',
            'guard' => array(
                'authenticators' => array(
                    'app.token_authenticator'
                ),
            ),
            'users' => function () use ($app) {
                return new App\Security\UserProvider($app['db']);
            }
        )
    )
));

$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/storage/logs/" . Carbon::now('Europe/London')->format("Y-m-d") . ".log",
    "monolog.level" => $app["log.level"],
    "monolog.name" => "application"
));

$resources = array(
    'users',
    'companies',
    'offices',
    'dishgroups',
    'dishes',
    'menudishes',
    'orders',
    'ratings',
    'feedback'
);

//load services
$servicesLoader = new App\ServicesLoader($app, $resources);
$servicesLoader->bindServicesIntoContainer();

//load routes
$routesLoader = new App\RoutesLoader($app, $resources);
$routesLoader->bindRoutesToControllers();

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new JsonResponse(array("statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});

return $app;
