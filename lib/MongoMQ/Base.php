<?php

namespace MongoMQ;

use MongoMQ\Connection\MongoMQConnectionInterface;

abstract class Base
{
    /** @var \MongoCollection  */
    protected $collection;
    protected $collectionName;
    protected $db;
    protected $name;

    public function __construct(MongoMQConnectionInterface $connection, $name, $collectionName = '_MongoMqQueue')
    {
        $this->collectionName = $collectionName;
        $this->db = $connection->getMongoDB();
        $this->name = $name;
        $this->collection = $this->db->selectCollection($collectionName);
    }
}