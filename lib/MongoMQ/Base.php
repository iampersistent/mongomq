<?php

namespace MongoMQ;

abstract class Base
{
    /** @var \MongoCollection  */
    protected $collection;
    protected $collectionName;
    protected $db;
    protected $name;

    public function __construct(\MongoDB $db, $name, $collectionName = '_MongoMqQueue')
    {
        $this->collectionName = $collectionName;
        $this->db = $db;
        $this->name = $name;
        $this->collection = $db->selectCollection($collectionName);
    }
}