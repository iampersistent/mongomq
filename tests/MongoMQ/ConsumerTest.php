<?php

namespace MongoMq;

use MongoMq\TestCommon;

class ConsumerTest extends TestCommon
{
    public function testVersion()
    {
        $producer = $this->getProducer('test');
        $consumer = $this->getConsumer('test');
    }
}