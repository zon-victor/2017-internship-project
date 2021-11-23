<?php

class institutionsModel extends errorsAndExceptionsLogger {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    try {
      parent::__construct($DB_DSN, $DB_USER, $DB_PASSWORD);
    } catch (PDOException $ex) {
      $this->logException($ex);
    }
  }

  private function checkIfUserIsMember($employee_no) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE employee_no=:employee_no LIMIT 1";
      $login = $this->con->prepare($sql);
      $login->bindValue(":employee_no", $employee_no, PDO::PARAM_STR);
      $login->execute();
      if ($login->rowCount() > 0) {
        $employee = $login->fetch(PDO::FETCH_OBJ);
        return $employee->fullname;
      } else {
        return 'N/A';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getDeductionsPerYear() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Deductions Deductions"
              . " INNER JOIN Q_FeedDB.Q2Payroll Employer ON Employer.pid = Deductions.pid"
              . " WHERE Deductions.inst_id=:id AND Deductions.salary_month LIKE :salary_month";
      $stmt = $this->con->prepare($sql);
      $stmt->bindParam(':id', $_SESSION['inst_id'], PDO::PARAM_STR);
      $stmt->bindValue(':salary_month', $_SESSION['year']."%", PDO::PARAM_STR);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($data as $user) {
          $user->name = $this->checkIfUserIsMember($user->employee_no);
        }
        return $data;
      } else {
        return "no data";
      }
    } catch (PDOException $ex) {
      $this->logException($ex);
    }
  }

}
