<?php

use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;
use App\DbConnect;
use Slim\Factory\AppFactory;
use App\ShowUrls;
use App\UrlRepository;
use App\UrlNormal;
use App\UrlValidator;
use Slim\Interfaces\RouteCollectorInterface;
use App\ListUrls;
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
    ShowUrls::class => function ($container) {
        return new ShowUrls(
            $container->get('flash'),
            $container->get(UrlRepository::class),
            $container->get(UrlCheckRepository::class),
            $container->get('renderer')
        );
    },
    ListUrls::class => function ($container) {
        return new ListUrls(
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