<?php

require_once('settings/Database.php');

class BaseModel
{
    protected $connection = null;
    protected $table = '';
    protected $pk = '';

    function __construct()
    {
        $instance = Database::getInstance();
        $this->connection = $instance->getConnection();
    }
}

