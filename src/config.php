<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
PHPQueue\Base::$queue_path = __DIR__ . '/queues/';
PHPQueue\Base::$worker_path = __DIR__ . '/workers/';

class FBPostConfig
{
    static public $backend_types = array(
          'MainQueue' => array(
                  'backend'   => 'Beanstalkd'
                , 'server'    => '127.0.0.1'
                , 'tube'      => 'fbposts'
            )
        , 'RecipientStore' => array(
                  'backend'   => 'MongoDB'
                , 'server' => 'mongodb://localhost'
                , 'db'  => 'fbposts'
                , 'collection' => 'recipients'
            )
    );

    static public function getConfig($type=null)
    {
        $config = isset(self::$backend_types[$type]) ? self::$backend_types[$type] : array();
        return $config;
    }
}