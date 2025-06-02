<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use App\UrlRepository;
use App\UrlValidator;
use App\UrlNormal;
use App\AddUrls;
use DI\ContainerBuilder;
use Slim\Interfaces\RouteCollectorInterface;
use App\ShowUrls;
use App\ListUrls;
use App\CheckUrls;
use App\UrlCheckRepository;
use App\UrlChecker;

session_start();

$container = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions(__DIR__ . '/../config/container.php')
    ->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$container->set(RouteCollectorInterface::class, fn() => $app->getRouteCollector());

$container->set(AddUrls::class, function ($container) {
    return new AddUrls(
        $container->get('flash'),
        $container->get(UrlNormal::class),
        $container->get(UrlRepository::class),
        $container->get(UrlValidator::class),
        $container->get('renderer'),
        $container->get(RouteCollectorInterface::class)
    );
});

$container->set(CheckUrls::class, function ($container) {
    return new CheckUrls(
        $container->get(RouteCollectorInterface::class),
        $container->get('renderer'),
        $container->get('flash'),
        $container->get(UrlRepository::class),
        $container->get(UrlCheckRepository::class),
        $container->get(UrlChecker::class)
    );
});

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'home.phtml');
})->setName('home');
$app->post('/urls', AddUrls::class)->setName('addUrl');
$app->get('/urls', ListUrls::class)->setName('urls');
$app->get('/urls/{id}', ShowUrls::class)->setName('url');
$app->post('/urls/{id}/checks', CheckUrls::class)->setName('checkUrl');

$app->run(); 