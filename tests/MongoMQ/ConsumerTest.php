<?php

namespace MongoMQ;

use MongoMQ\TestCommon;

class ConsumerTest extends TestCommon
{
    public function testConsume()
    {
        $data = array(
            'name' => 'test',
            'message' => 'message 1',
            'process' => true,
            'timestamp' => new \MongoDate(),
        );
        $this->dbCollection()->insert($data);
        $data = array(
            'name' => 'test',
            'message' => 'message 2',
            'process' => false,
            'timestamp' => new \MongoDate(),
        );
        $this->dbCollection()->insert($data);
        $data = array(
            'name' => 'test',
            'message' => 'message 3',
            'process' => true,
            'timestamp' => new \MongoDate(),
        );
        $this->dbCollection()->insert($data);
        $data = array(
            'name' => 'test',
            'message' => 'message 4',
            'process' => true,
            'timestamp' => new \MongoDate(),
        );
        $this->dbCollection()->insert($data);

        $consumer = $this->getConsumer('test');
        $consumer->setCallback(array($this, 'consumePassOne'));
        $consumer->consume(4);
        $events = $this->dbCollection()->find(array('process' => false));
        $this->assertSame(1, $events->count(), 'events not to be processed should remain');

        $events = $this->dbCollection()->find(array('process' => true));
        $this->assertSame(2, $events->count());
        foreach ($events as $event) {
            $this->assertSame(1, $event['tries']);
        }

        $consumer->setCallback(array($this, 'consumePassTwo'));
        $consumer->consume(3);
        $events = $this->dbCollection()->find(array('process' => false));
        $this->assertSame(1, $events->count(), 'events not to be processed should remain');

        $events = $this->dbCollection()->find(array('process' => true));
        $this->assertSame(1, $events->count());
        foreach ($events as $event) {
            $this->assertSame(2, $event['tries']);
        }

        $consumer->setCallback(array($this, 'consumePassThree'));
        $consumer->consume(3);
        $events = $this->dbCollection()->find(array('process' => false));
        $this->assertSame(2, $events->count(), 'three failures should add additional event not to be processed');
        foreach ($events as $event) {
            if ($event['message'] == 'message 4') {
                $this->assertSame(3, $event['tries'], 'the failing event should display 3 retries');
            }
        }
    }

    public function testConsumeWithFatalHandler()
    {
        $data = array(
            'name' => 'test',
            'message' => 'message 1',
            'process' => true,
            'timestamp' => new \MongoDate(),
        );
        $this->dbCollection()->insert($data);

        register_shutdown_function(array($this, 'passOneFatalHandler'));
        $consumer = $this->getConsumer('test');
        $consumer->setCallback(array($this, 'consumeWithFatalHandler'));
        $consumer->consume(1);
    }

    public function consumePassOne($message)
    {
        if ($message['message'] === 'message 1') {
            return true;
        }

        if ($message['message'] === 'message 3') {
            return false;
        }

        if ($message['message'] === 'message 4') {
            return false;
        }
    }

    public function consumePassTwo($message)
    {
        if ($message['message'] === 'message 3') {
            return true;
        }

        if ($message['message'] === 'message 4') {
            return false;
        }
    }

    public function consumePassThree($message)
    {
        if ($message['message'] === 'message 4') {
            return false;
        }
    }

    public function consumeWithFatalHandler($message)
    {
        if ($message['message'] === 'message 1') {
            die('test error');
        }
    }

    protected function passOneFatalHandler()
    {
        $this->assert();
    }
}