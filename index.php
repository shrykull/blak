<?php
  require_once __DIR__.'/silex/vendor/autoload.php';
  
  $app = new Silex\Application();
  
  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
  ));
  
  $app->get('/{home}', function() use ($app) {
    return $app['twig']->render('home.twig');
  })->assert("home", "^(home|)$")->bind('home');
  
  $app->get('/search', function() use ($app) {
    return $app['twig']->render('search.twig');
  })->bind('search');
  
  $app->run();