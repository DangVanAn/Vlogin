<?php
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Created by PhpStorm.
 * User: Sherman
 * Date: 2/28/2017
 * Time: 5:04 PM
 */
class Users extends \Phalcon\Mvc\Model
{
//    public function validation()
//    {
//        // Robot name must be unique
//        $this->validate(
//            new Uniqueness(
//                [
//                    "field" => "email",
//                    "message" => "The robot name must be unique",
//                ]
//            )
//        );
//
//        // Check if any messages have been produced
//        if ($this->validationHasFailed() === true) {
//            return false;
//        }
//    }
}