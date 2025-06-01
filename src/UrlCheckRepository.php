<?php

namespace App;

use PDO;

class UrlCheckRepository
{
    public function __construct(private PDO $pdoConnection)
    {
    }

    public function add(array $check): string
    {
        $sql = 'INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at) 
            VALUES(:url_id, :status_code, :h1, :title, :description, :created_at)';

        $stmt = $this->pdoConnection->prepare($sql);

        foreach ($check as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $this->pdoConnection->lastInsertId('url_checks_id_seq');
    }

    public function get(string $urlId): array
    {
        $sql = 'SELECT * FROM url_checks WHERE url_id = :url_id ORDER BY created_at DESC;';

        $stmt = $this->pdoConnection->prepare($sql);
        $stmt->bindValue(':url_id', $urlId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}