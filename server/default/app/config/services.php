<?php

use Phalcon\Mvc\View\Simple as View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * Sets the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});

$di['acl'] = function() use($di) {
    $acl = new \Phalcon\Acl\Adapter\Database([
        'db' => $di['db'],
        'roles'             => 'roles',
        'rolesInherits'     => 'roles_inherits',
        'resources'         => 'resources',
        'resourcesAccesses' => 'resources_accesses',
        'accessList'        => 'access_list'
    ]);

    $acl->setDefaultAction(\Phalcon\Acl::DENY);
    $acl->addRole(new Phalcon\Acl\Role('Users'));
    $acl->addRole(new Phalcon\Acl\Role('Guests'));

// Create the resource with its accesses
    $acl->addResource('login',["a"]);
    $acl->addResource('logout',["a"]);
    $acl->addResource('abc',["a"]);
    $acl->addResource('error',["a"]);
    $acl->addResource('signup',["a"]);

// Allow Admins to insert products
    $acl->allow('Users', 'logout', 'a');
    $acl->allow('Users', 'abc', 'a');
    $acl->allow('Users', 'error', 'a');

    $acl->allow('Guests', 'login', 'a');
    $acl->allow('Guests', 'error', 'a');
    $acl->allow('Guests', 'signup', 'a');
    return $acl;
};
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});
