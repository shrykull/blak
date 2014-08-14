<?php
  require_once('src/main/DBConnection.php');
  require_once('src/main/tables/User.php');
  require_once('src/main/model/User.php');
  
  use table\User as UserTable;
  use model\User as UserModel;
  
  const SUCCESS = 0;
  const ERROR_NOT_EXISTANT = 1;
  const ERROR_NOT_ENABLED = 2;
  const ERROR_PASSWORD = 3;
  const ERROR_NOT_LOGGED_IN = 4;
  const ERROR_SESSION = 5;
  
  
  function doLogin($username, $password, $remember=false, $newSession=true, $checkPassword=true) {
    $config = parse_ini_file(__DIR__.'/../res/db.ini');
    $conn = new DBConnection($config["host"], $config["username"], $config["password"], "blak");
    $userTable = new UserTable($conn);
    
    $user = $userTable->getUserByUsername($username);
    
    // check if user exists
    if ($user === null) {
      // user does not exist
      return ERROR_NOT_EXISTANT;
    }
    
    // check if user is enabled
    if (!$user->isEnabled()) {
      return ERROR_NOT_ENABLED;
    }
    
    // check password
    if ($checkPassword) {
      $salt = $user->getSalt();
      $generatedPassword = explode('$', crypt($password, '$5$rounds=5000$'.$salt.'$'));
      if ($generatedPassword[4] !== $user->getPassword()) {
        // password not correct
        return ERROR_PASSWORD;
      }
    }
    
    // update lastLogin of user
    $time = date("Y-m-d H:i:s");
    $user->setLast_login($time);
    
    // generate sessionId
    if ($newSession) {
      $sessionId = md5($salt.$username.substr($generatedPassword[4], 0, 5).time());
      $user->setSession_key($sessionId);
    }
    
    
    // set username
    session_unset();
    $_SESSION['username'] = $username;
    
    // write new userData to database
    $userTable->updateUser($user);
    
    // if remidMe is set cookies will overlive current session
    if ($remember) {
      // (cookie will live for 3 days but will be renewed after every autoLogin)
      $expire = time() + 3600 * 24 * 3;
      setcookie('sessUsr', base64_encode($user->getId()), $expire);
    } else {
      // cookie will die after session
      $expire = 0;
    }
    setcookie('sessToken', $user->getSession_key(), $expire);
    
    // redirect to page
    return SUCCESS;
  }
  
  function doLogout() {
    if (!isset($_SESSION['username'])) {
      return ERROR_NOT_LOGGED_IN;
    }
    
    $username = $_SESSION['username'];
    
    $config = parse_ini_file(__DIR__.'/../res/db.ini');
    $conn = new DBConnection($config["host"], $config["username"], $config["password"], "blak");
    $userTable = new UserTable($conn);
    
    $user = $userTable->getUserByUsername($username);
    
    // should not happen!
    if ($user === null) {
      // user does not exist
      return ERROR_NOT_EXISTANT;
    }
    
    // unset session_key in database
    $user->setSession_key("");
    
    // unset session
    unset($_SESSION['username']);
    
    // delete cookies
    setcookie('sessUsr', null, -1);
    setcookie('sessToken', null, -1);
    
    $userTable->updateUser($user);
    
    return SUCCESS;
  }
  
  function tryAutoLogin($userId, $sessionId) {
    $uid = base64_decode($userId);
    // $_COOKIE['sessToken'];
    
    $config = parse_ini_file(__DIR__.'/../res/db.ini');
    $conn = new DBConnection($config["host"], $config["username"], $config["password"], "blak");
    $userTable = new UserTable($conn);
    
    $user = $userTable->getUserById($uid);
    
    if ($sessionId !== $user->getSession_key()) {
      return ERROR_SESSION;
    }
    
    return doLogin($user->getUsername(), $user->getPassword(), true, false, false);
  }