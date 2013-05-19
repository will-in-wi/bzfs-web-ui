<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require '../vendor/autoload.php';

define('APP_ROOT', realpath( dirname(dirname( __FILE__ ) ).'/'));

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */

// Setup custom Twig view
$twigView = new \Slim\Extras\Views\Twig();

// Instantiate application
$app = new \Slim\Slim(array(
    'view' => $twigView
));
$twigView::$twigTemplateDirs = array('../app/templates');

$app->view()->appendData(array(
    'base_url' => $app->request()->getUrl() . str_replace('//', '/', dirname($_SERVER['SCRIPT_NAME']) . '/')
));


$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => '20 minutes',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => 'ysR1HGtt6UwDExoJcmq069Egxidfsm',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));

include_once('../app/lib/helpers.php');

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */

// Load all the routes.
_require_all('../app/routes');

$app->run();
