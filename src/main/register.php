<?php
  require_once('src/main/DBConnection.php');
  require_once('src/main/tables/User.php');
  require_once('src/main/model/User.php');
  
  use table\User as UserTable;
  use model\User as UserModel;
  
  const SUCCESS = 0;
  const ERROR_USERNAME = 1;
  
  function register($username, $email, $password) {
    $config = parse_ini_file(__DIR__.'/../res/db.ini');
    $conn = new DBConnection($config["host"], $config["username"], $config["password"], "blak");
    
    $userTable = new UserTable($conn);
    if ($userTable->getUserByUsername($username) !== null) {
      return ERROR_USERNAME;
    }
    $salt = substr(str_shuffle(sha1(substr($username, 0, 5).substr($password ,0,5).time())),0, 16);
    // SHA-256
    $generatedPassword = explode('$', crypt($password, '$5$rounds=5000$'.$salt.'$'));
    
    $verifyCode = substr(str_shuffle(sha1(time())),0, 20).substr(str_shuffle(sha1(time())),0, 20);
    
    $newUser = new UserModel(null, false, $username, $email, $generatedPassword[4], null, $verifyCode, null, $salt, null);

    return $userTable->addUser($newUser) !== null ? SUCCESS : null;
  }
  
  function verify($username, $verifyCode) {
    $config = parse_ini_file(__DIR__.'/../res/db.ini');
    $conn = new DBConnection($config["host"], $config["username"], $config["password"], "blak");
    
    $userTable = new UserTable($conn);
    
    $user = $userTable->getUserByUsername($username);
    if ($user->getVerify_code() !== $verifyCode) {
      return false;
    }
    
    $user->setEnabled(true);
    return $userTable->updateUser($user)->isEnabled();
  }