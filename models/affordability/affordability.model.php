<?php

class affordabilityModel {
  private $category;
  private $net_salary;
  private $amount;
  private $errors;
  private $result;

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    try {
// CONSTRUCTING AN OBJECT TO CONNECT TO THE QFEED DATABSE
      $con = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
      $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->con = $con;
    } catch (PDOException $e) {
      $e->getMessage();
    }
  }

  private function sanitize($var) {
    $var1 = strip_tags($var);
    $var3 = trim($var1);
    return ($var3);
  }

  public function getSavedTest($id) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Affordability WHERE id_affordability =:id_affordability LIMIT 1";
      $get = $this->con->prepare($sql);
      $get->bindValue(':id_affordability', $id, PDO::PARAM_STR);
      $get->execute();
      if ($get->rowCount() > 0) {
        $tests = $get->fetch(PDO::FETCH_ASSOC);
        return $tests;
      } else {
        $none['none'] = 'Nothing found.';
        return $none;
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
  
  public function getSavedTests() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Affordability WHERE userid =:userid";
      $get = $this->con->prepare($sql);
      $get->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $get->execute();
      if ($get->rowCount() > 0) {
        $tests = $get->fetchAll(PDO::FETCH_OBJ);
        return $tests;
      } else {
        $none['none'] = 'Nothing saved.';
        return $none;
      }
    } catch (Exception $ex) {
      $ex->getMessage();
    }
  }

  public function calculateAffordability($data) {
    $validate = $this->validateInput($data);
    if ($validate == 1) {
      echo json_encode($this->errors);
      return;
    }
    $affordabilty = '/QFeed/compiled/affordability/affordability';
    $sector = $_SESSION['sector'];
    $command = $affordabilty.' '.$sector.' '.$this->category.' '.$this->net_salary.' '.$this->amount;
    $this->result = shell_exec($command);
    $done['amount'] = $this->amount;
    $done['net_salary'] = $this->net_salary;
    $done['category'] = $this->category;
    if ($this->result == 0) {
      $done['outcome'] = 'pass';
    } else if ($this->result == 1) {
      $done['outcome'] = 'failed';
    } else {
      $done['outcome'] = 'Internal error, undesired results: '.$this->result;
    }
    return $done;
  }

  public function validateInput($data) {
    $this->errors = [];
    if (array_key_exists('category', $data) && null !== $data['category']) {
      $this->category = $this->sanitize($data['category']);
    } else {
      $this->errors['category_error'] = 'Please provide a valid category<br>';
    }
    
    if (array_key_exists('net_salary', $data) && null !== $data['net_salary'] 
            && preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['net_salary'])) {
      $this->net_salary = $this->sanitize($data['net_salary']);
    } else {
      $this->errors['net_salary_error'] = 'Please provide a valid net salary<br>';
    }
    
    if (array_key_exists('amount', $data) && null !== $data['amount'] 
            && preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['amount'])) {
      $this->amount = $this->sanitize($data['amount']);
    } else {
      $this->errors['amount_error'] = 'please provide a valid amount<br>';
    }
    
    if (empty($this->errors)) {
      return 0;
    } else {
      return 1;
    }
  }

  public function saveResults($data) {
    try {
      $sql = "INSERT INTO Q_FeedDB.Q2Affordability (userid, amount, net_salary, service, category, outcome)"
              . "VALUES (:userid, :amount, :net_salary, :service, :category, :outcome)";
      $save = $this->con->prepare($sql);
      $save->bindParam(":userid", $_SESSION['userid'], PDO::PARAM_STR);
      $save->bindParam(":amount", $data['cost'], PDO::PARAM_STR);
      $save->bindParam(":net_salary", $data['current_net'], PDO::PARAM_STR);
      $save->bindParam(":service", $data['service_name'], PDO::PARAM_STR);
      $save->bindParam(":category", $data['category'], PDO::PARAM_STR);
      $save->bindParam(":outcome", $data['outcome'], PDO::PARAM_STR);
      $save->execute();
      if ($save->rowCount() > 0) {
        echo 'success';
      } else {
        echo 'failure';
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function updateResults($data) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Affordability SET amount = :amount, net_salary = :net_salary, service = :service, category = :category, outcome = :outcome WHERE id_affordability = :id_affordability AND userid =:userid LIMIT 1";
      $save = $this->con->prepare($sql);
      $save->bindValue(":userid", $_SESSION['userid'], PDO::PARAM_STR);
      $save->bindValue(":amount", $data['amount'], PDO::PARAM_STR);
      $save->bindValue(":net_salary", $data['net_salary'], PDO::PARAM_STR);
      $save->bindValue(":service", $data['service_name'], PDO::PARAM_STR);
      $save->bindValue(":category", $data['category'], PDO::PARAM_STR);
      $save->bindValue(":outcome", $data['outcome'], PDO::PARAM_STR);
      $save->bindValue(":id_affordability", $data['id_affordability'], PDO::PARAM_STR);
      $save->execute();
      if ($save->rowCount() > 0) {
        $done['success'] = 'success';
        $done['service_name'] = $data['service_name'];
        $done['amount'] = $data['amount'];
        $done['net_salary'] = $data['net_salary'];
        $done['category'] = $data['category'];
        $done['outcome'] = $data['outcome'];
      } else {
        $done['failure'] = 'failure';
      }
      echo json_encode($done);
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
  
  public function deleteExistingTest($data) {
    try {
      $id = $this->sanitize($data['P1']);
      $sql = "DELETE FROM Q_FeedDB.Q2Affordability WHERE userid =:userid AND id_affordability =:id_affordability";
      $delete = $this->con->prepare($sql);
      $delete->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $delete->bindValue(':id_affordability', $id, PDO::PARAM_STR);
      $delete->execute();
      if ($delete->rowCount() > 0) {
        $done['success'] = 'success';
      } else {
        $done['failure'] = 'failure';
      }
      echo json_encode($done);
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
}
