<?php

namespace App;

use Slim\Views\PhpRenderer;
use PDO;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class ListUrlsHandler
{
    public function __construct(private PhpRenderer $renderer, private PDO $pdoConnection)
    {
    }

    public function getAllDetail(): array
    {
        $sql = '
            SELECT urls.id,
               urls.name,
               checks.created_at AS last_check_date,
               checks.status_code
            FROM urls
            LEFT JOIN (
                SELECT DISTINCT ON (url_id) url_id, created_at, status_code
                FROM url_checks
                ORDER BY url_id, created_at DESC) checks
            ON urls.id = checks.url_id
            ORDER BY urls.created_at DESC;';

        $stmt = $this->pdoConnection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __invoke(ServerRequest $request, Response $response)
    {
        $params = [
            'urls' => $this->getAllDetail(),
        ];

        return $this->renderer->render($response, 'list.phtml', $params);
    }
}
