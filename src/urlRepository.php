<?php

namespace App;

use PDO;

class UrlRepository
{
    public function __construct(private PDO $pdoConnection)
    {
    }

    public function getEntities(): array
    {
        $urls = [];
        $sql = "SELECT * FROM urls";
        $stmt = $this->conn->query($sql);

        while ($row = $stmt->fetch()) {
            $url = Url::fromArray([$row['name'], $row['created_at']]);
            $url->setId($row['id']);
            $urls[] = $url;
        }

        return $urls;
    }


    public function findOneByName(string $name): ?array
    {
        $sql = 'SELECT * FROM urls WHERE name = :name';

        $stmt = $this->pdoConnection->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_shift($result);
    }    
    
    public function add(array $url): string
    {
        $sql = 'INSERT INTO urls (name, created_at) VALUES(:name, :created_at)';

        $stmt = $this->pdoConnection->prepare($sql);

        foreach ($url as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $this->pdoConnection->lastInsertId('urls_id_seq');
    }

    public function getOne(string $id): array
    {
        $sql = 'SELECT * FROM urls WHERE id = :id';

        $stmt = $this->pdoConnection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return array_shift($result);
    }

    public function get(): array
    {
        $sql = 'SELECT * FROM urls ORDER BY created_at DESC;';

        $stmt = $this->pdoConnection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}