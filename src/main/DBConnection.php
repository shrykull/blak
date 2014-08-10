<?php
  /**
   * This class provides the connection to a MySQL database.
   *
   * @author Georg Steinmetz
   */
  class DBConnection {
    private $host;
    private $user;
    private $password;
    private $dbname;

    protected $connection;

    /**
     * Connect to a MySQL database.
     *
     * @param host The hostname to reach the database.
     * @param user The username to log in at the database.
     * @param password The password to log in at the database.
     * @param dbname (optional) The name of the database you want to connect to.
     */
    function __construct( $host, $user, $password, $dbname="" ) {
      $this->host = $host;
      $this->user = $user;
      $this->password = $password;
      $this->dbname = $dbname;

      if ( empty($this->dbname) ) {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password);
      } else {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
      }

    }

    /**
     * Close database connection.
     */
    public function __destruct() {
      mysqli_close($this->connection);
    }

    /**
     * Reconnect to the currently configured database.
     */
    public function reconnect() {
      mysqli_close($this->connection);
      if ( empty($this->dbname) ) {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password);
      } else {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
      }
    }
    
    /**
     * Create a new database.
     *
     * @param dbname The name of the database that will be created.
     */
    public function createDatabase( $dbname ) {
      $sql = "CREATE DATABASE '$dbname'";

      mysqli_query($sql);

      if (mysqli_connect_errno()) {
        return mysqli_connect_error();
      } else {
        return 0;
      }
    }

    /**
     * Send a select query with your parameters
     *
     * @param query The SQL query. (Example: * FROM user WHERE uid=%d)
     * @param * The params for your SQL query (Example select("* FROM user WHERE uid=%d", 42); )
     */
    public function select($query) {
      $args = func_get_args();
      unset($args[0]);
      
      $queryString = vsprintf("SELECT $query", $args);
      $data = mysqli_query($this->connection, $queryString);
      $result = array();
      while ($row = $data->fetch_assoc()) {
        $result[] = $row;
      }
      
      return $result !== null ? $result : null;
    }

    public function insert($query) {
      $args = func_get_args();
      unset($args[0]);
      
      $queryString = vsprintf("INSERT $query", $args);
      return mysqli_query($this->connection, $queryString);
    }
    
    public function update($query) {
      $args = func_get_args();
      unset($args[0]);
      
      $queryString = vsprintf("UPDATE $query", $args);
      return mysqli_query($this->connection, $queryString);
    }

    public function getDatabaseName() {
      return $this->dbname;
    }

    public function getHost() {
      return $this->host;
    }

    public function getUser() {
      return $this->user;
    }

    public function selectDatabase( $dbname ) {
      $this->dbname = $dbname;
      mysqli_select_db($this->connection, $this->dbname);
    }

    public function setUser( $user, $reconnect=true ) {
      $this->user = $user;
      if ($reconnect)
        reconnect();
    }

    public function setPassword( $user, $reconnect=true ) {
      $this->password = $password;
      if ($reconnect)
        reconnect();
    }

    public function getConnection() {
      return $this->connection;
    }
  }