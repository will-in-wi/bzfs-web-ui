<?php

namespace BzfsWebUi;

require __DIR__ . '/../vendor/autoload.php';

define('APP_ROOT', realpath( dirname(dirname( __FILE__ ) ).'/'));
session_start();

$container = new \DI\Container();
\Slim\Factory\AppFactory::setContainer($container);

$flashMessages = new \Slim\Flash\Messages();

$container->set('flash', function () use ($flashMessages) {
    return $flashMessages;
});

$container->set('view', function() use ($flashMessages) {
    $view = \Slim\Views\Twig::create('../app/templates');

    $view->addExtension(new Lib\TwigMessages($flashMessages));

    return $view;
});

$app = \Slim\Factory\AppFactory::create();
$app->add(\Slim\Views\TwigMiddleware::createFromContainer($app));

// $app->add(new \Slim\Middleware\SessionCookie(array(
//     'expires' => '20 minutes',
//     'path' => '/',
//     'domain' => null,
//     'secure' => false,
//     'httponly' => false,
//     'name' => 'slim_session',
//     'secret' => 'ysR1HGtt6UwDExoJcmq069Egxidfsm',
//     'cipher' => MCRYPT_RIJNDAEL_256,
//     'cipher_mode' => MCRYPT_MODE_CBC
// )));

$app->get('/', Actions\Management::class);
$app->get('/start', Actions\StartFormView::class);
$app->post('/start', Actions\StartFormSubmit::class);

$app->run();
