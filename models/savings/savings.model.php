<?php

class savingsModel {

  private $goal;
  private $target;
  private $amount;
  private $interest;
  private $interest_on_interest;
  private $interest_period;
  private $years;
  private $months;
  private $status;
  private $type;
  private $results;
  private $period;

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    try {
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

  public function calculateSavings($data) {
    $validate = $this->validateInput($data);
    if ($validate == 1) {
      echo json_encode($this->errors);
      return;
    }
    $savings = '/QFeed/compiled/savings/savings';
    $this->type = $data['P1'];

    if ($this->type === 'target') {
      $command = $savings . ' ' . $this->type . ' y' . $this->years . ' m' . $this->months . ' ' . $this->interest . ' ' . $this->target . ' ' . $this->interest_period;
    } else if ($this->type === 'term') {
      $command = $savings . ' ' . $this->type . ' y' . $this->years . ' m' . $this->months . ' ' . $this->interest . ' ' . $this->amount . ' ' . $this->interest_period . ' ' . $this->interest_on_interest;
    }

    $this->results = shell_exec($command);
    $this->years == 1 ? $years = 'YEAR' : $years = 'YEARS';
    $this->months == 1 ? $months = 'MONTH' : $months = 'MONTHS';
    $this->results == 'inf' ? $this->results = '0' : $this->results = $this->results;

    if ($this->months % 12 == 0) {
      $num = $this->months / 12;
      $this->years += $num;
      $this->months = 0;
    } else if ($this->months % 12 > 0) {
      $num = $this->months - ($this->months % 12);
      $this->years += $num / 12;
      $this->months = $this->months % 12;
    }
    $this->period = $this->years . '.' . $this->months;
    if ($this->type === 'term') {
      $this->target = $this->results;
      echo 'YOUR INVESTMENT WILL BE ' . $this->results . ' IN ' . $this->years . ' ' . $years . ' AND ' . $this->months . ' ' . $months;
    } else if ($this->type === 'target') {
      $this->amount = $this->results;
      $this->interest_on_interest = 'no';
      echo 'YOU MUST SAVE ' . $this->results . ' PER MONTH TO BUDGET ' . $this->target . ' IN ' . $this->years . ' ' . $years . ' AND ' . $this->months . ' ' . $months;
    }
    $this->status = 'uninitialized';
    $this->saveGoal();
  }

  public function validateInput($data) {
    // $this->errors = array();
    if (array_key_exists('goal', $data) && !empty($data['goal'])) {
      $this->goal = $this->sanitize($data['goal']);
    } else {
      $this->errors['goal_error'] = 'Please provide a valid goal<br>';
    }

    if (array_key_exists('interest', $data) && '' !== $data['interest']) {
      $this->interest = $this->sanitize($data['interest']);
    } else {
      $this->errors['interest_error'] = 'Please provide valid interest<br>';
    }

    if (array_key_exists('interest_period', $data) && '' !== $data['interest_period']) {
      $this->interest_period = $this->sanitize($data['interest_period']);
    } else {
      $this->errors['interest_period_error'] = 'Please select a valid interest period<br>';
    }

    if (array_key_exists('years', $data) && '' !== $data['years']) {
      $this->years = $this->sanitize($data['years']);
    } else {
      $this->errors['years_error'] = 'Please fix years in period<br>';
    }

    if (array_key_exists('months', $data) && '' !== $data['months']) {
      $this->months = $this->sanitize($data['months']);
    } else {
      $this->errors['months_error'] = 'Please fix months in period<br>';
    }

    if (array_key_exists('P1', $data) && "target" === $data['P1']) {
      if (array_key_exists('target', $data) && !empty($data['target']) && preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['target'])) {
        $this->target = $this->sanitize($data['target']);
      } else {
        $this->errors['target_error'] = 'Please provide a valid target value<br>';
      }
    }

    if (array_key_exists('P1', $data) && "term" === $data['P1']) {
      if (array_key_exists('amount', $data) && '' !== $data['amount'] && preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['amount'])) {
        $this->amount = $this->sanitize($data['amount']);
      } else {
        $this->errors['amount_error'] = 'please provide a valid amount<br>';
      }
      if (array_key_exists('ioi', $data) && '' !== $data['ioi']) {
        $this->interest_on_interest = $this->sanitize($data['ioi']);
      } else {
        $this->errors['ioi_error'] = 'Please select a valid interest option<br>';
      }
    }

    if (empty($this->errors)) {
      return 0;
    } else {
      return 1;
    }
  }

  public function saveGoal() {
    try {
      $sql = "INSERT INTO Q_FeedDB.Q2Budget (userid, goal, target, interest, interest_period, ioi, period, amount, status, type)"
              . "VALUES (:userid, :goal, :target, :interest, :interest_period, :ioi, :period, :amount, :status, :type)";
      $save = $this->con->prepare($sql);
      $save->bindParam(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $save->bindParam(':goal', $this->goal, PDO::PARAM_STR);
      $save->bindParam(':target', $this->target, PDO::PARAM_STR);
      $save->bindParam(':interest', $this->interest, PDO::PARAM_STR);
      $save->bindParam(':interest_period', $this->interest_period, PDO::PARAM_STR);
      $save->bindParam(':ioi', $this->interest_on_interest, PDO::PARAM_STR);
      $save->bindParam(':period', $this->period, PDO::PARAM_STR);
      $save->bindParam(':amount', $this->amount, PDO::PARAM_STR);
      $save->bindParam(':status', $this->status, PDO::PARAM_STR);
      $save->bindParam(':type', $this->type, PDO::PARAM_STR);
      $save->execute();
      if ($save->rowCount() > 0) {
        return TRUE;
      } else {
        return FALSE;
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function getGoals() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Budget WHERE userid = :userid";
      $get = $this->con->prepare($sql);
      $get->bindvalue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $get->execute();
      if ($get->rowCount() > 0) {
        $goals = $get->fetchAll(PDO::FETCH_ASSOC);
        return $goals;
      } else {
        $none['none'] = 'Nothing found.';
        return $none;
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function changeGoalStatus($id, $status) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Budget SET status = :status WHERE userid = :userid AND id_budget = :id_budget LIMIT 1";
      $update = $this->con->prepare($sql);
      $update->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $update->bindValue(':id_budget', $id, PDO::PARAM_STR);
      $update->bindValue(':status', $status, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        echo 'update successful';
      } else {
        echo 'Update failed!';
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function getSpecificGoals($status) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Budget WHERE userid =:userid AND status =:status";
      $get = $this->con->prepare($sql);
      $get->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $get->bindValue(':status', $status, PDO::PARAM_STR);
      $get->execute();
      if ($get->rowCount() > 0) {
        $goals = $get->fetchAll(PDO::FETCH_ASSOC);
        return $goals;
      } else {
        $none['none'] = "<div style='width: 50%; height: auto; margin: 40px auto; color: #5d8aa8; text-transform: capitalize; font-size: 24px; padding-top: 48px;'>$status goals not found<div>";
        return $none;
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function getGoal($id) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Budget WHERE userid =:userid AND id_budget =:id_budget LIMIT 1";
      $get = $this->con->prepare($sql);
      $get->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $get->bindValue(':id_budget', $id, PDO::PARAM_STR);
      $get->execute();
      if ($get->rowCount() > 0) {
        $goals = $get->fetch(PDO::FETCH_ASSOC);
        return $goals;
      } else {
        $none['none'] = "<div style='width: 50%; height: auto; margin: 40px auto; color: #5d8aa8; text-transform: capitalize; font-size: 24px; padding-top: 48px;'>Goal not found<div>";
        return $none;
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
  
  public function deleteGoal($id, $status) {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Budget WHERE userid =:userid AND id_budget =:id_budget";
      $delete = $this->con->prepare($sql);
      $delete->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $delete->bindValue(':id_budget', $id, PDO::PARAM_STR);
      $delete->execute();
      if ($delete->rowCount() > 0) {
        $test = $this->getSpecificGoals($status);
        if (array_key_exists('none', $test)) {
          echo json_encode($test);
        } else {
          echo 'successful';
        }
      } else {
        echo 'failed';
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
  
  public function deleteAllGoals($status) {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Budget WHERE userid =:userid AND status =:status";
      $delete = $this->con->prepare($sql);
      $delete->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $delete->bindValue(':status', $status, PDO::PARAM_STR);
      $delete->execute();
      if ($delete->rowCount() > 0) {
        echo "<div style='width: 50%; height: auto; margin: 40px auto; color: #5d8aa8; text-transform: capitalize; font-size: 24px; padding-top: 48px;'>all $status goals successfully deleted!</div>";
      } else {
        echo "<div style='width: 50%; height: auto; margin: 40px auto; color: #5d8aa8; text-transform: capitalize; font-size: 24px; padding-top: 48px;'>$status goals not found<div>";
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
  
  public function endOfInvestment($start_date, $period) {
    $date = date_create($start_date);
    date_add($date, date_interval_create_from_date_string($period));
    return date_format($date,"Y-m-d");
  }
  
  public function initializeGoal($data) {
    try {
      $start_date = date("Y-m-d");
      $end_date = $this->endOfInvestment($start_date, $data['period']);
      $sql = "UPDATE Q_FeedDB.Q2Budget SET start_date = :start_date, end_date = :end_date, status = :status WHERE userid = :userid AND id_budget = :id_budget LIMIT 1";
      $update = $this->con->prepare($sql);
      $update->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $update->bindValue(':id_budget', $data['id'], PDO::PARAM_STR);
      $update->bindValue(':start_date', $start_date, PDO::PARAM_STR);
      $update->bindValue(':end_date', $end_date, PDO::PARAM_STR);
      $update->bindValue(':status', 'initialized', PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        echo 'update successful';
      } else {
        echo 'Update failed!';
      }
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }
}
