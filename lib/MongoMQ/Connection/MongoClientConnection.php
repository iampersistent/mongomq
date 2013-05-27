<?php

namespace MongoMQ\Connection;

class MongoClientConnection implements MongoMQConnectionInterface
{
    protected $db;
    protected $dbName;
    protected $server;

    public function __construct($server, $dbName)
    {
        $this->dbName = $dbName;
        $this->server = $server;
    }

    public function getMongoDB()
    {
        if (!$this->db) {
            $client = new \MongoClient($this->server);
            $this->db = $client->selectDB($this->dbName);
        }

        return $this->db;
    }
}