<?php
namespace apps\cron\exception;

use Phalcon\Mvc\Model;

class CronException extends \Exception
{
    const INFO = 10;
    const LOGIC = 20;
    const DEBUG = 30;
    const WARNING = 40;
    const ERROR = 50;
    const MANAGED_ERROR = 60;
    private $entity;

    public function __construct($message, $code, $entity = null, Exception $previous = null)
    {
        if ($entity instanceof Model) $entity = $entity->toArray();
        $this->entity = json_encode($entity);
        parent::__construct($message, $code, $previous);
    }

    public function getEntity()
    {
        return $this->entity;
    }
}