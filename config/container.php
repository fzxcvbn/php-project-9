<?php

use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;
use PDO;
use App\DbConnect;
use Slim\Factory\AppFactory;
use Slim\App;
use App\ShowUrlsController;
use App\UrlRepository;
use App\UrlNormal;
use App\UrlValidator;
use Slim\Interfaces\RouteCollectorInterface;

return [
    App::class => function () {
        return AppFactory::create();
    },
    'flash' => function () {
        return new Messages();
    },
    'renderer' => function () {
        return new PhpRenderer(__DIR__ . '/../templates');
    },
    PDO::class => function () {
        return DbConnect::get()->connect();
    },
    ShowUrlsController::class => function ($container) {
        return new ShowUrlsController(
            $container->get('flash'),
            $container->get(UrlRepository::class),
            /*$container->get(UrlCheckRepository::class),*/
            $container->get('renderer')
        );
    }
];