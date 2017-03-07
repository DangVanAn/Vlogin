<?php

use \Phalcon\Events\Event;
use \Phalcon\Mvc\Dispatcher;

/**
 * Created by PhpStorm.
 * User: Sherman
 * Date: 2/28/2017
 * Time: 5:47 PM
 */
class NotFoundPlugin extends \Phalcon\Mvc\User\Plugin
{
    public function beforeException(Event $event, Dispatcher $dispatcher)
    {

    }
}