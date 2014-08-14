<?php
  require_once __DIR__.'/silex/vendor/autoload.php';
  require_once __DIR__.'/src/main/login.php';
  
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\RedirectResponse;
  use Symfony\Component\HttpKernel\HttpKernelInterface;
  
  $app = new Silex\Application();
  
  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
  ));
  
  session_start();
  
  if (!isset($_SESSION['username']) && isset($_COOKIE['sessUsr'])) {
    tryAutoLogin($_COOKIE['sessUsr'], $_COOKIE['sessToken']);
  }
  
  $app['twig']->addGlobal('session', $_SESSION);
  
  $app->error(function (\Exception $e, $code) use ($app) {
    $error = array();
    switch ($code) {
      case 404:
        $error["message"] = "The requested page could not be found";
        break;
      default:
        $error["message"] = "Some bad Voodoo happened. Please try it again.";
        $error["info"]    = "If the Voodoo keeps happening please contact an Administrator";
    }
    
    return $app['twig']->render('error.twig', array('error' => $error));
  });

  $app->get('/error', function() use ($app) {
    return $app['twig']->render('error.twig');
  });
  
  $app->get('/{home}', function(Request $request) use ($app) {
    $a = array(
      'info' => $request->get('info')
    );
    return $app['twig']->render('home.twig', $a);
  })->assert("home", "^(home|)$")->bind('home');
  
  $app->get('/search', function() use ($app) {
    return $app['twig']->render('search.twig');
  })->bind('search');
  
  $app->get('/register', function() use ($app) {
    return $app['twig']->render('register.twig');
  })->bind('register');
  
  $app->post('/register', function(Request $request) use ($app) {
    if ($request->get('password') !== $request->get('verify_password')) {
      return new Response($app['twig']->render('register.twig', 406));
    }
    
    require_once('src/main/register.php');
    register($request->get('username'), $request->get('email'), $request->get('password'));
    return new Response($app['twig']->render('home.twig', 201));
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
  
  $app->post('/login', function(Request $request) use ($app) {
    if (empty($request->get('username'))) {
      $error = array(
        'error' => array(
          'message' => 'Username may not be empty.'
        )
      );
      $subRequest = Request::create('/login', 'GET', $error);
      return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    
    if (empty($request->get('password'))) {
      $error = array(
        'error' => array(
          'message' => 'Password may not be empty.'
        )
      );
      $subRequest = Request::create('/login', 'GET', $error);
      return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    
    $remember = $request->get('remember')==="1";
    switch (doLogin($request->get('username'), $request->get('password'), $remember)) {
      case SUCCESS:
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
        break;
      case ERROR_NOT_EXISTANT:
        $message = "There is no user with this username";
        break;
      case ERROR_NOT_ENABLED:
        $message = "This account is not verified yet.";
        break;
      case ERROR_PASSWORD:
        $message = "You entered the wrong password.";
        break;
      default:
        $message = "Unknown error. Please try again.";
    }
    
    $error = array(
      'error' => array(
        'message' => $message
      )
    );
    $subRequest = Request::create('/login', 'GET', $error);
    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    
  });
  
  $app->get('/login', function(Request $request) use ($app) {
    if(isset($_SESSION['username'])) {
      return new RedirectResponse('/home');
    }
    $a = array(
      "error" => $request->get('error')
    );
    return $app['twig']->render('login.twig', $a);
  });
  
  $app->get('/logout', function(Request $request) use ($app) {
    if (doLogout() === SUCCESS) {
      return $app->redirect('/home');
    }
  });
  
  $app->run();