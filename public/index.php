<?php

require_once __DIR__.'/../vendor/autoload.php';

// for use with php built-in webserver
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


// index
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
})->bind('homepage');


// partners
$app->get('/partners', function () use ($app) {
    return $app['twig']->render('partners.html.twig');
})->bind('partners');

$app->get('/partners/{partner}', function ($partner) use ($app) {
    return $app['twig']->render('partners/'. $partner . '.html.twig');
})->bind('partner_detail');


// stories
$app->get('/stories', function () use ($app) {
    return $app['twig']->render('stories/h1.html.twig');
})->bind('stories');

$app->get('/stories/{story}', function ($story) use ($app) {
    return $app['twig']->render('stories/' . $story . '.html.twig');
})->bind('story');


// use-cases
$app->get('/solutions', function () use ($app) {
    return $app['twig']->render('solutions.html.twig');
})->bind('solutions');

$app->get('/solutions/{case}', function ($case) use ($app) {
    return $app['twig']->render('solutions/' . $case . '.html.twig');
})->bind('solution');


// contact
$app->get('/contact', function () use ($app) {
    return $app['twig']->render('contact.html.twig');
})->bind('contact');


// debug
$app['debug'] = true;

$app->run();
