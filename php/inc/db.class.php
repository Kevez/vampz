<?php

class Db {

  private $host   = 'vampz-rds-dev.cfwbg1pys1mn.us-west-2.rds.amazonaws.com';
  private $user   = 'vampz001';
  private $pass   = 'DxV2Fc21gb2PJAM';
  private $dbname = 'vampz';

  private $dbh;
  private $error;
  private $stmt;

  public function __construct() {

    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
    
    $options = array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
    );

    try{
      $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
    }
    catch(PDOException $e){
      $this->error = $e->getMessage();
    }
  }

  public function query($query){
    $this->stmt = $this->dbh->prepare($query);
  }

  public function bind($param, $value, $type = null) {

    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
      }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  public function execute(){
    return $this->stmt->execute();
  }

  public function resultset(){
    $this->execute();
    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function single(){
    $this->execute();
    return $this->stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function rowCount(){
    $this->execute();
    return $this->stmt->rowCount();
  }
}