<?php

namespace MongoMQ;

class Producer extends Base
{
    public function publish($message)
    {
        $data = array(
            'name' => $this->name,
            'message' => $message,
            'process' => true,
            'timestamp' => new \MongoDate(),
        );
        $this->collection->insert($data);
    }
}