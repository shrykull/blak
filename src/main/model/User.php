<?php
  namespace model;

  /**
   * This class provides the connection to a MySQL database.
   *
   * @author Georg Steinmetz
   */
  class User {
    private $id;
    private $enabled;
    private $username;
    private $email;
    private $password;
    private $create_time;
    private $verify_code;
    private $last_login;
    private $salt;
    private $session_key;

    function __construct( $id, $enabled, $username, $email, $password, $create_time, $verify_code, $last_login, $salt, $session_key ) {
      $this->id = (int) $id;
      $this->enabled = $enabled === "1" ? true : false;
      $this->username = $username;
      $this->email = $email;
      $this->password = $password;
      $this->create_time = $create_time;
      $this->verify_code = $verify_code;
      $this->last_login = $last_login;
      $this->salt = $salt;
      $this->session_key = $session_key;
    }

    public function getId() {
        return $this->id;
    }
    
    public function getEnabled() {
        return $this->enabled;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getCreate_time() {
        return $this->create_time;
    }

    public function getVerify_code() {
        return $this->verify_code;
    }

    public function getLast_login() {
        return $this->last_login;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getSession_key() {
        return $this->session_key;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setCreate_time($create_time) {
        $this->create_time = $create_time;
    }

    public function setVerify_code($verify_code) {
        $this->verify_code = $verify_code;
    }

    public function setLast_login($last_login) {
        $this->last_login = $last_login;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
    }

    public function setSession_key($session_key) {
        $this->session_key = $session_key;
    }
  }