<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();
$vendorDir = dirname(dirname(dirname(__FILE__)));
$loader->registerNamespaces([
    'Phalcon' => array($vendorDir .'/vendor/phalcon/incubator/Library/Phalcon/')
]);
$loader->registerDirs(
    [
        $config->application->modelsDir
    ]
)->register();
