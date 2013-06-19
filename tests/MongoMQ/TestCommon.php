<?php

namespace MongoMQ;

use MongoMQ\Connection\MongoClientConnection;

abstract class TestCommon extends \PHPUnit_Framework_TestCase
{
    protected function getConnection()
    {
        return new MongoClientConnection('mongodb://localhost:27017', 'test');
    }

    protected function getConsumer($name)
    {
        $connection = $this->getConnection();

        return new Consumer($connection, $name, '_MongoMqQueue');
    }

    protected function getProducer($name)
    {
        $connection = $this->getConnection();

        return new Producer($connection, $name, '_MongoMqQueue');
    }
}
