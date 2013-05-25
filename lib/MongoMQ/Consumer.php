<?php

namespace MongoMQ;

class Consumer extends Base
{
    protected $callback;
    protected $messages;

    public function consume($count)
    {
        $this->retrieveMessages($count);
        foreach($this->messages as $key => $message) {
            if ($this->callback->execute($message)) {
                unset($this->messages[$key]);
                $this->collection->remove(array('_id' => $message['_id']));
            }
        }
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    protected function retrieveMessages($count)
    {
        if (empty($this->messages)) {
            $aggregate = array(
                array('$match' => array('name' => $$this->name)).
                array('$sort' => array('timestamp' => -1)),
            );

            if ($count > 0) {
                $aggregate[] = array('$limit' => $count);
            }

            $this->messages = $this->collection->aggregate($aggregate);
        }
    }
}