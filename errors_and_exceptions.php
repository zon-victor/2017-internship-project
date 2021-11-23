<?php

class errorsAndExceptionsLogger {

  public $name;
  public $username;
  public $account;

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    try {
      $con = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->con = $con;
    } catch (PDOException $e) {
      $e->getMessage();
    }
  }

  public function logError($error_type, $error_message, $username, $name, $account) {
    try {
      $sql = "INSERT INTO Q_FeedDB.Q2Errors (error_type, error_message, username, name, account) VALUE (:error_type, :error_message, :username, :name, :account)";
      $error = $this->con->prepare($sql);
      $error->bindParam(':error_type', $error_type, PDO::PARAM_STR);
      $error->bindParam(':error_message', $error_message, PDO::PARAM_STR);
      $error->bindParam(':username', $username, PDO::PARAM_STR);
      $error->bindParam(':name', $name, PDO::PARAM_STR);
      $error->bindParam(':account', $account, PDO::PARAM_STR);
      $error->execute();
      if ($error->rowCount() > 0) {
        return 0;
      } else {
        return 1;
      }
    } catch (PDOException $exc) {
      echo $exc->getMessage();
    }
  }
  
  public function logException($ex) {
    try {
      if (isset($_SESSION['email'])) {
        $this->username = $_SESSION['email'];
        $this->name = $_SESSION['name'] !== NULL ? $_SESSION['name'] : 'Guest User';
      } elseif (isset($_SESSION['username'])) {
        $this->username = $_SESSION['username'];
        $this->name = $_SESSION['username'] !== NULL ? $_SESSION['name'] : 'Guest User';
      }
      $this->account = $_SESSION['account'] !== NULL ? $_SESSION['account'] : 'guest';
      $this->storeException($ex->getCode(), $ex->getMessage(), $ex->getLine(), $ex->getFile(), $ex->getTraceAsString(), $this->username, $this->name, $this->account);
    } catch (Exception $exc) {
      echo $exc->getTraceAsString();
    }
  }
  
  public function storeException($code, $message, $line, $filename, $trace, $username, $name, $account) {
    try {
      $sql = "INSERT INTO Q_FeedDB.Q2Exceptions (code, message, line, filename, trace, username, name, account) VALUE (:code, :message, :line, :filename, :trace, :username, :name, :account)";
      $error = $this->con->prepare($sql);
      $error->bindParam(':code', $code, PDO::PARAM_INT);
      $error->bindParam(':message', $message, PDO::PARAM_STR);
      $error->bindParam(':line', $line, PDO::PARAM_INT);
      $error->bindParam(':filename', $filename, PDO::PARAM_STR);
      $error->bindParam(':trace', $trace, PDO::PARAM_STR);
      $error->bindParam(':username', $username, PDO::PARAM_STR);
      $error->bindParam(':name', $name, PDO::PARAM_STR);
      $error->bindParam(':account', $account, PDO::PARAM_STR);
      $error->execute();
      if ($error->rowCount() > 0) {
        return 0;
      } else {
        return 1;
      }
    } catch (Exception $ex) {
      $ex->getMessage();
    }
  }

}
