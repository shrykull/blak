<?php
  require_once __DIR__.'/silex/vendor/autoload.php';
  
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  
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
  
  $app->get('/register', function() use ($app) {
    return $app['twig']->render('register.twig');
  })->bind('register');
  
  $app->post('/register', function(Request $request) use ($app) {
    if ($request->get('password') !== $request->get('verify_password')) {
      return new Response($app['twig']->render('register.twig'), 406);
    }
    
    require_once('src/main/register.php');
    register($request->get('username'), $request->get('email'), $request->get('password'));
    return new Response($app['twig']->render('home.twig'), 201);
  });
  
  $app->get('/verify/{username}/{verifyCode}', function($username, $verifyCode) use ($app) {
    if ( !isset($username) || !isset($verifyCode) ) {
      return $app['twig']->render('home.twig');
    }
    
    require_once('src/main/register.php');
    
    $array = array(
      "verified" => verify($username, $verifyCode)
    );
    
    return $app['twig']->render('verify_account.twig', $array);
  })->bind('verify');
  
  $app->run();