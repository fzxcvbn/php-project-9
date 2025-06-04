<?php

use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;
use App\DbConnect;
use Slim\Factory\AppFactory;
use App\ShowUrlsHandler;
use App\UrlRepository;
use App\UrlNormal;
use App\UrlValidator;
use Slim\Interfaces\RouteCollectorInterface;
use App\ListUrlsHandler;
use GuzzleHttp\Client;
use DiDom\Document;
use App\UrlChecker;
use App\UrlCheckRepository;

return [
    'flash' => function () {
        return new Messages();
    },
    'renderer' => function () {
        return new PhpRenderer(__DIR__ . '/../templates');
    },
    \PDO::class => function () {
        return DbConnect::get()->connect();
    },
    ShowUrlsHandler::class => function ($container) {
        return new ShowUrlsHandler(
            $container->get('flash'),
            $container->get(UrlRepository::class),
            $container->get(UrlCheckRepository::class),
            $container->get('renderer')
        );
    },
    ListUrlsHandler::class => function ($container) {
        return new ListUrlsHandler(
            $container->get('renderer'),
            $container->get(\PDO::class)
        );
    },
    UrlChecker::class => function ($container) {
        return new UrlChecker(
            $container->get(Client::class),
            $container->get(Document::class)
        );
    }
];