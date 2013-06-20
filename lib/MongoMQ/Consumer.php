<?php

namespace MongoMQ;

use MongoMQ\Connection\MongoMQConnectionInterface;

class Consumer extends Base
{
    protected $callback;
    protected $currentMessage;
    protected $messages;

    public function __construct(MongoMQConnectionInterface $connection, $name, $collectionName = '_MongoMqQueue')
    {
        parent::__construct($connection, $name, $collectionName);
        register_shutdown_function(array($this, 'fatalHandler'));
    }

    public function consume($count)
    {
        $this->retrieveMessages($count);
        $this->process();
    }

    public function process()
    {
        $this->currentMessage = null;

        foreach($this->messages as $key => $message) {
            $this->currentMessage = $message;
            if (call_user_func($this->callback, $message)) {
                $this->collection->remove(array('_id' => $message['_id']));
            } else {
                $this->handleFailure();
            }
            unset($this->messages[$key]);
        }
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    protected function handleFailure()
    {
        $newObj = $this->currentMessage;
        if (isset($newObj['tries'])) {
            $newObj['tries'] = $newObj['tries'] + 1;
            if ($newObj['tries'] > 2) {
                $newObj['process'] = false;
            }
        } else {
            $newObj['tries'] = 1;
        }
        $criteria = ['_id' => $newObj['_id']];

        $this->collection->update($criteria, $newObj);
    }

    protected function retrieveMessages($count)
    {
        if (empty($this->messages)) {
            $aggregate = array(
                array('$match' =>
                    array(
                        'name' => $this->name,
                        'process' => true,
                    ),
                ),
                array('$sort' => array('timestamp' => -1)),
            );

            if ($count > 0) {
                $aggregate[] = array('$limit' => (integer) $count);
            }

            $results = $this->collection->aggregate($aggregate);
            if ($results['ok'] == 0) {
                throw new \Exception($results['errmsg']);
            }
            $this->messages = $results['result'];
        }
    }

    function fatalHandler()
    {
        $this->handleFailure();
        foreach ($this->messages as $key => $message) {
            if ($message == $this->currentMessage) {
                unset($this->messages[$key]);
            }
        }
        $this->process();
    }
}