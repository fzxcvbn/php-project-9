<?php

namespace App;

class DbConnect
{
    private static ?self $conn = null;

    private function __construct()
    {
    }

    public function connect()
    {
        $databaseUrl = parse_url(getenv('DATABASE_URL') ? : '');

        $dsn = sprintf(
            "postgresql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $databaseUrl['host'],
            $databaseUrl['port'],
            ltrim($databaseUrl['path'], '/'),
            $databaseUrl['user'],
            $databaseUrl['pass']
        );

        $pdo = new \PDO($dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function get(): self
    {
        if (!self::$conn) {
            self::$conn = new self();
        }

        return self::$conn;
    }
}