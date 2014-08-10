<?php
  require_once('src/main/DBConnection.php');
  require_once('src/main/tables/User.php');
  require_once('src/main/model/User.php');
  
  use table\User as UserTable;
  use model\User as UserModel;
  
  function register($username, $email, $password) {
    $salt = substr(str_shuffle(sha1("blak")),0, 16);
    // SHA-256
    $generatedPassword = explode('$', crypt($password, '$5$rounds=5000$'.$salt.'$'));
    $verifyCode = substr(str_shuffle(sha1(time())),0, 20).substr(str_shuffle(sha1(time())),0, 20);
    
    $newUser = new UserModel(null, false, $username, $email, $generatedPassword[4], null, $verifyCode, null, $salt, null);
    
    $conn = new DBConnection("0.0.0.0", "shrykull", "", "blak");
    $userTable = new UserTable($conn);

    return $userTable->addUser($newUser);
  }
  
  function verify($username, $verifyCode) {
    $conn = new DBConnection("0.0.0.0", "shrykull", "", "blak");
    $userTable = new UserTable($conn);
    
    $user = $userTable->getUserByUsername($username);
    if ($user->getVerify_code() !== $verifyCode) {
      return false;
    }
    
    $user->setEnabled(true);
    return $userTable->updateUser($user)->getEnabled();
  }