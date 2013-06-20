<?php

namespace MongoMQ;

use MongoMQ\Connection\MongoClientConnection;

abstract class TestCommon extends \PHPUnit_Framework_TestCase
{
    protected $collection;
    protected $collectionName = '_MongoMqQueue';
    protected $connection;

    protected function dbCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->getConnection()->getMongoDB()->createCollection($this->collectionName);
        }

        return $this->collection;
    }

    /**
     * @return MongoClientConnection
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new MongoClientConnection('mongodb://localhost:27017', 'test');
        }

        return $this->connection;
    }

    protected function getConsumer($name)
    {
        return new Consumer($this->getConnection(), $name, $this->collectionName);
    }

    protected function getProducer($name)
    {
        return new Producer($this->getConnection(), $name, $this->collectionName);
    }

    protected function setUp()
    {
        $this->dbCollection()->drop();
    }

    protected function tearDown()
    {
        $this->dbCollection()->drop();
    }
}
