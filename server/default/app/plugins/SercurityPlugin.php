<?php

/**
 * Created by PhpStorm.
 * User: Sherman
 * Date: 2/28/2017
 * Time: 5:46 PM
 */
class SercurityPlugin extends \Phalcon\Mvc\User\Plugin
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        // Check whether the "auth" variable exists in session to define the active role
        $auth = $this->session->get('auth');
        if (!$auth) {
            $role = 'Guests';
        } else {
            $role = 'Users';
        }

        // Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // Obtain the ACL list
        $acl = $this->getAcl();

        // Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed != Acl::ALLOW) {

            // If he doesn't have access forward him to the index controller
            $this->flash->error("You don't have access to this module");
            $dispatcher->forward(
                array(
                    'controller' => 'index',
                    'action'     => 'index'
                )
            );

            // Returning "false" we tell to the dispatcher to stop the current operation
            return false;
        }
    }

    public function getAcl() {
        // setup acl at first time
        if (! isset ( $this->persistent->acl )) {
            // create acl list for type of user
            $acl = new AclList();
            // deny is default acl
            $acl->setDefaultAction ( Acl::DENY );
            // Create 2 roler for two user type: guest and user
            $roles = array (
                'users' => new Role ( 'Users' ),
                'guests' => new Role ( 'Guests' )
            );
            foreach ( $roles as $role ) {
                $acl->addRole ( $role );
            }
            // private resource area
            $privateResources = array (
                'users' => array (
                    'index',
                    'search',
                    'edit',
                    'delete'
                ),
                'companies' => array (
                    'index',
                    'search',
                    'new',
                    'edit',
                    'create',
                    'delete'
                ),
                'products' => array (
                    'index',
                    'search',
                    'new',
                    'edit',
                    'create',
                    'delete'
                ),
                'producttypes' => array (
                    'index',
                    'search',
                    'new',
                    'edit',
                    'save',
                    'create',
                    'delete'
                ),
                'invoices' => array (
                    'index',
                    'profile'
                )
            );
            // add private area
            foreach ( $privateResources as $resource => $actions ) {

                $acl->addResource ( new Resource ( $resource ), $actions );
            }
            // public area
            $publicResource = array (
                'index' => array (
                    'index'
                ),
                'about' => array (
                    'index'
                ),
                'register' => array (
                    'index',
                    'regis'
                ),
                'session' => array (
                    'index',
                    'register',
                    'start',
                    'end'
                ),
                'users' => array (
                    'create',
                    'new'
                ),
                'example' => array (
                    'test1',
                    'signup'
                )
            );
            // add public area
            foreach ( $publicResource as $resource => $actions ) {
                $acl->addResource ( new Resource ( $resource ), $actions );
            }

            // grant all user have access to get public area
            foreach ( $roles as $role ) {
                foreach ( $publicResource as $resource => $actions ) {
                    foreach ( $actions as $action ) {
                        $acl->allow ( $role->getName (), $resource, $action );
                    }
                }
            }
            // grant for only user have access to private area
            foreach ( $privateResources as $resource => $actions ) {
                foreach ( $actions as $action ) {
                    $acl->allow ( 'Users', $resource, $action );
                }
            }
            $this->persistent->acl = $acl;
        }
        return $this->persistent->acl;
    }
}