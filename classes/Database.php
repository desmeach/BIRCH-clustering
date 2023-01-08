<?php

class Database {
    private PDO $connection;
    public function __construct() {
        require_once 'DBConfig.php';
        /** @var string $host */
        /** @var string $dbname */
        /** @var string $user */
        /** @var string $pass */
        $this->connection = new PDO('mysql:host='. $host .';dbname='. $dbname, $user, $pass);
    }
    public function getClients() {
        $query = $this->connection->query('SELECT name, age, income, credit_history, marital_status, education FROM clients');
        return $query->fetchAll();
    }
}