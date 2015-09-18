<?php

use Kilte\Silex\Captcha\CaptchaServiceProvider;

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
$app->register(new CaptchaServiceProvider());

// index
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
})->bind('homepage');


// partners
$app->get('/partners/', function () use ($app) {
    return $app['twig']->render('partners.html.twig');
})->bind('partners');

$app->get('/partners/{partner}', function ($partner) use ($app) {
    return $app['twig']->render('partners/'. $partner . '.html.twig');
})->bind('partner_detail');


// stories
$app->get('/stories/', function () use ($app) {
    return $app['twig']->render('stories/h1.html.twig');
})->bind('stories');

$app->get('/stories/{story}', function ($story) use ($app) {
    return $app['twig']->render('stories/' . $story . '.html.twig');
})->bind('story');


// use-cases
$app->get('/solutions/', function () use ($app) {
    return $app['twig']->render('solutions.html.twig');
})->bind('solutions');

$app->get('/solutions/{case}', function ($case) use ($app) {
    return $app['twig']->render('solutions/' . $case . '.html.twig');
})->bind('solution');

// jobs
$app->get('/jobs/', function () use ($app) {
    return $app['twig']->render('jobs.html.twig');
})->bind('jobs');

$app->get('/jobs/{job}', function ($job) use ($app) {
    return $app['twig']->render('jobs/' . $job . '.html.twig');
})->bind('job');


// contact
$app->get('/contact/', function () use ($app) {

    /** @var \Gregwar\Captcha\CaptchaBuilder $captchaBuilder */
    $captchaBuilder = $app['captcha.builder'];
    $captchaBuilder->setMaxFrontLines(2);
    $captchaBuilder->setMaxBehindLines(3);
    $captchaBuilder->build();

    $app['session']->set('phrase', $captchaBuilder->getPhrase());

    return $app['twig']->render('contact.html.twig', [
        'captcha' => $captchaBuilder
    ]);
})->bind('contact');

$app->post('/contact/', function () use ($app) {
    $request = $app['request'];

    if ($app['session']->get('phrase') == $request->get('item-captcha')) {
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
            ->setTo(['info@keboola.com'])
            ->setBody($body);

        $app['mailer']->send($message);

        $app['session']->getFlashBag()->add('success', "Thank you for contacting us! We'll get back to you soon.");
    } else {
        $app['session']->getFlashBag()->add('error', "Image code didn't match, plesae try again.");
    }

    return $app->redirect('/contact/');
});

// tableau
$app->get('/tableau/', function () use ($app) {
    return $app['twig']->render('tableau.html.twig');
})->bind('tableau');

// docs
$app->get('/docs/{doc}', function ($doc) use ($app) {
    return $app['twig']->render('docs/' . $doc . '.html.twig');
})->bind('doc');

// error page
$app->error(function (\Exception $e, $code) use ($app) {
    return $app['twig']->render('error.html.twig', [
        'code' => $code
    ]);
});

// debug
$app['debug'] = true;

$app->run();
