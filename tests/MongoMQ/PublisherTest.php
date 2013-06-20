<?php

namespace MongoMQ;

use MongoMQ\TestCommon;

class PublisherTest extends TestCommon
{
    public function testPublish()
    {
        $producer = $this->getProducer('test');
        $message = 'this is a test message';
        $producer->publish($message);

        $event = $this->dbCollection()->findOne(array('message' => $message));
        $this->assertArrayHasKey('name', $event);
        $this->assertSame('test', $event['name']);
        $this->assertArrayHasKey('process', $event);
        $this->assertArrayHasKey('timestamp', $event);
        $this->assertTrue($event['process'], 'a new event should be processed by default');
    }
}