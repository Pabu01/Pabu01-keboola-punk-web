<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

// for use with php built-in webserver
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../resources/views',
]);


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
$app->post('/contact', function () use ($app) {
    $request = $app['request'];

    if ($request->get('crumb') == 2) {
        $body = "Name: " . $request->get('item-name') . PHP_EOL
            . "Email: " . $request->get('item-email') . PHP_EOL
            . "Phone: " . $request->get('item-phone') . PHP_EOL
            . "Demo: " . $request->get('demo') . PHP_EOL
            . "Webinar: " . $request->get('webinar') . PHP_EOL
            . "Trial: " . $request->get('trial') . PHP_EOL
            . "Coffee: " . $request->get('coffee') . PHP_EOL
            . "Message: " . $request->get('item-message') . PHP_EOL;

        $message = \Swift_Message::newInstance()
            ->setSubject('Keboola Contact Form')
            ->setFrom([$request->get('item-email')])
            ->setTo(['miro@keboola.com'])
            ->setBody($body);

        $app['mailer']->send($message);

        $app['session']->getFlashBag()->add('info', "Thank you for contacting us! We'll get back to you soon.");
    }

    return $app['twig']->render('contact.html.twig');
});


// debug
$app['debug'] = true;

$app->run();
