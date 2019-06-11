<?php
class CronController extends ControllerBase{

    /**
     * @var DateTime
     */
    protected $now;

    /**
     * @var \Phalcon\Db\Adapter\Pdo\Mysql
     */
    protected $connection;

    public function initialize(){
        parent::initialize();
        ini_set('max_execution_time', 0);
        set_time_limit(0);
    }

    /**
     * Cron Actions Here
     */

}