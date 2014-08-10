<?php
  namespace table;
  
  require(__DIR__.'/../model/User.php');

  use model\User as UserModel;
  
  /**
   * This class provides the connection to a MySQL database.
   *
   * @author Georg Steinmetz
   */
  class User {
    private $connection;

    const TABLENAME = "user";
    const COL_ID = "id";
    const COL_USERNAME = "username";
    const COL_EMAIL = "email";
    const COL_PASSWORD = "password";
    const COL_CREATE_TIME = "create_time";
    const COL_VERIFY_CODE = "verify_code";
    const COL_LAST_LOGIN = "last_login";
    const COL_SALT = "salt";
    const COL_SESSION_KEY = "session_key";

    function __construct( $connection ) {
      $this->connection = $connection;
    }

    private function createNewUser( $queryResult, $getPassword ) {
      $userData = $queryResult[0];
      if (!$getPassword) {
        $userData["password"] = null;
      }
        
      return new UserModel(
        $userData["id"],
        $userData["enabled"],
        $userData["username"],
        $userData["email"],
        $userData["password"],
        $userData["create_time"],
        $userData["verify_code"],
        $userData["last_login"],
        $userData["salt"],
        $userData["session_key"]
      );
    }

    public function getUserById( $id, $getPassword=true ) {
      $queryResult = $this->connection->select("* FROM %s WHERE id = '%d' LIMIT 1", $this::TABLENAME, $id);
      if (!empty($queryResult)) {
        return $this->createNewUser($queryResult, $getPassword);
      } else {
        return null;
      }
    }

    public function getUserByUsername( $username, $getPassword=true  ) {
      $queryResult = $this->connection->select("* FROM %s WHERE username = '%s' LIMIT 1", $this::TABLENAME, $username);
      if (!empty($queryResult)) {
        return $this->createNewUser($queryResult, $getPassword);
      } else {
        return null;
      }
    }
    
    public function addUser( $user ) {
      // Check if username already exists
      if ($this->getUserByUsername($user->getUsername()) !== null) {
        return null;
      }
      
      // it does not exist, so go on
      if (
        $this->connection->insert(
        "INTO %s (enabled, username, email, password, verify_code, salt) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
        $this::TABLENAME,
        $user->getEnabled() == true ? "1" : "0",
        $user->getUsername(),
        $user->getEmail(),
        $user->getPassword(),
        $user->getVerify_code(),
        $user->getSalt()
        )
      ) {
          return $user;
        } else {
          return null;
        }
    }
    
    public function updateUser( $user ) {
      if (
        $this->connection->update(
          "%s SET enabled='%s', email='%s', password='%s', last_login='%s', session_key='%s' WHERE id = '%d'",
          $this::TABLENAME,
          $user->getEnabled() == true ? "1" : "0",
          $user->getEmail(),
          $user->getPassword(),
          $user->getLast_login(),
          $user->getSession_key(),
          $user->getId()
          )
      ) {
        return $user;
      } else {
        return null;
      }
    }

  }