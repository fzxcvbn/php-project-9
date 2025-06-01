<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use App\UrlRepository;
use App\UrlValidator;
use App\UrlNormal;
use App\AddUrlsController;
use DI\ContainerBuilder;
use Slim\Interfaces\RouteCollectorInterface;
use App\ShowUrlsController;

session_start();

$container = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions(__DIR__ . '/../config/container.php')
    ->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$container->set(RouteCollectorInterface::class, fn() => $app->getRouteCollector());

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'home.phtml');
})->setName('home');


$app->post('/urls', AddUrlsController::class)->setName('addUrl');
$app->get('/urls', ListUrlsController::class)->setName('urls');
$app->get('/urls/{id}', ShowUrlsController::class)->setName('url');

$app->run(); 