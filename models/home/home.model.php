<?php

class homeModel extends errorsAndExceptionsLogger {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    parent::__construct($DB_DSN, $DB_USER, $DB_PASSWORD);
  }

  public function countRegisteredAccounts($verified) {
    try {
      $sql = "SELECT verified FROM Q_FeedDB.Q2Registry WHERE verified = :verified";
      $count = $this->con->prepare($sql);
      $count->bindValue(":verified", $verified, PDO::PARAM_INT);
      $count->execute();
      return $count->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countActiveAccounts($access) {
    try {
      $sql = "SELECT access FROM Q_FeedDB.Q2Registry WHERE access = :access";
      $active = $this->con->prepare($sql);
      $active->bindValue(":access", $access, PDO::PARAM_STR);
      $active->execute();
      return $active->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countErrors($error_type) {
    try {
      $sql = "SELECT id_errors FROM Q_FeedDB.Q2Errors WHERE error_type = :error_type";
      $errors = $this->con->prepare($sql);
      $errors->bindValue(":error_type", $error_type, PDO::PARAM_STR);
      $errors->execute();
      return $errors->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countExceptions($exception_status) {
    try {
      $sql = "SELECT id_exceptions FROM Q_FeedDB.Q2Exceptions WHERE status = :status";
      $exceptions = $this->con->prepare($sql);
      $exceptions->bindValue(":status", $exception_status, PDO::PARAM_STR);
      $exceptions->execute();
      return $exceptions->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countFeedbacks($status) {
    try {
      $sql = "SELECT id_feedback FROM Q_FeedDB.Q2Feedback WHERE status = :status";
      $feedbacks = $this->con->prepare($sql);
      $feedbacks->bindValue(":status", $status, PDO::PARAM_STR);
      $feedbacks->execute();
      return $feedbacks->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countQueriesByState($state) {
    try {
      $sql = "SELECT id_help FROM Q_FeedDB.Q2Help WHERE state = :state AND (type = :typex OR type = :typey)";
      $queries = $this->con->prepare($sql);
      $queries->bindValue(":state", $state, PDO::PARAM_STR);
      $queries->bindValue(":typex", 'query', PDO::PARAM_STR);
      $queries->bindValue(":typey", 'followup', PDO::PARAM_STR);
      $queries->execute();
      return $queries->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function countQueriesByStatus($status) {
    try {
      $sql = "SELECT id_help FROM Q_FeedDB.Q2Help WHERE status = :status AND (type = :typex OR type = :typey)";
      $queries = $this->con->prepare($sql);
      $queries->bindValue(":status", $status, PDO::PARAM_STR);
      $queries->bindValue(":typex", 'query', PDO::PARAM_STR);
      $queries->bindValue(":typey", 'followup', PDO::PARAM_STR);
      $queries->execute();
      return $queries->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function checkIfIsMember($email) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE email=:email OR altmail=:email LIMIT 1";
      $login = $this->con->prepare($sql);
      $login->bindValue(":email", $email, PDO::PARAM_STR);
      $login->execute();
      if ($login->rowCount() > 0) {
        $employee = $login->fetch(PDO::FETCH_OBJ);
        return $employee->fullname;
      } else {
        return 0;
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }
  
    public function countQueriesByMembership($account) {
    try {
      $sql = "SELECT id_help FROM Q_FeedDB.Q2Help WHERE account = :account AND type = :query";
      $queries = $this->con->prepare($sql);
      $queries->bindValue(":account", $account, PDO::PARAM_STR);
      $queries->bindValue(":query", 'query', PDO::PARAM_STR);
      $queries->execute();
      return $queries->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function randomIdGenerator() {
    return mt_rand(100001, 960000);
  }

  private function cssClassGenerator($line, $filename, $salt) {
    $class = substr(hash('sha256', $line . $filename . $salt), 0, 7);
    return $class;
  }

  public function getUsers() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Registry";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $users = $all->fetchAll(PDO::FETCH_OBJ);
        foreach ($users as $user) {
          $user->acc_status_id = $this->randomIdGenerator();
          $employer = $this->getEmployer($user->payroll);
          $user->employer = $employer->payroll;
        }
      } else {
        $users['none'] = 'USERS NOT FOUND';
      }
      return $users;
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getEmployer($pid) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Payroll WHERE pid = :pid LIMIT 1";
      $emp = $this->con->prepare($sql);
      $emp->bindParam(':pid', $pid, PDO::PARAM_STR);
      $emp->execute();
      if ($emp->rowCount() > 0) {
        $e = $emp->fetch(PDO::FETCH_OBJ);
      }
      return $e;
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getInstitutions() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Institution";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $institutions = $all->fetchAll(PDO::FETCH_OBJ);
        foreach ($institutions as $institution) {
          $institution->acc_status_id = $this->randomIdGenerator();
          if ($institution->institution_abbr !== 'none') {
            $institution->name = $institution->institution_abbr;
          } else {
            $institution->name = $institution->institution;
          }
        }
      } else {
        $institutions['none'] = 'INSTITUTIONS NOT FOUND';
      }
      return $institutions;
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getErrors() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Errors";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $errors = $all->fetchAll(PDO::FETCH_OBJ);
      } else {
        $errors[0]->none = 'ERRORS NOT FOUND';
      }
      return $errors;
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function deleteError($id_errors) {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Errors WHERE id_errors = :id_errors";
      $delete = $this->con->prepare($sql);
      $delete->bindValue(':id_errors', $id_errors, PDO::PARAM_STR);
      $delete->execute();
      return $delete->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function deleteErrors() {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Errors";
      $delete = $this->con->prepare($sql);
      $delete->execute();
      return $delete->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getExceptions() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Exceptions";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $exceptions = $all->fetchAll(PDO::FETCH_OBJ);
        foreach ($exceptions as $exception) {
          $exception->exc_status = $this->cssClassGenerator($exception->line, $exception->filename, "exception status common css class");
          $exception->exc_status_btn = $this->cssClassGenerator($exception->line, $exception->filename, "exception status button common css class");
        }
      } else {
        $exceptions[0]->none = 'EXCEPTIONS NOT FOUND';
      }
      return $exceptions;
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function deleteExceptionsByLineAndFilename($line, $filename) {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Exceptions WHERE line = :line AND filename = :filename";
      $delete = $this->con->prepare($sql);
      $delete->bindValue(':line', $line, PDO::PARAM_STR);
      $delete->bindValue(':filename', $filename, PDO::PARAM_STR);
      $delete->execute();
      return $delete->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function deleteAllExceptions() {
    try {
      $sql = "DELETE FROM Q_FeedDB.Q2Exceptions";
      $delete = $this->con->prepare($sql);
      $delete->execute();
      return $delete->rowCount();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function setExceptionStatus($line, $filename, $status) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Exceptions SET status = :status WHERE line = :line AND filename = :filename";
      $update = $this->con->prepare($sql);
      $update->bindValue(':status', $status, PDO::PARAM_STR);
      $update->bindValue(':line', $line, PDO::PARAM_STR);
      $update->bindValue(':filename', $filename, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        echo 'success';
      } else {
        echo 'failure';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function toggleUserAccess($access, $email) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Registry SET access = :access WHERE email = :email LIMIT 1";
      $update = $this->con->prepare($sql);
      $update->bindValue(':access', $access, PDO::PARAM_STR);
      $update->bindValue(':email', $email, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        echo 'success';
      } else {
        echo 'failure';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function toggleInstitutionAccess($access, $username) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Institution SET access = :access WHERE username = :username LIMIT 1";
      $update = $this->con->prepare($sql);
      $update->bindValue(':access', $access, PDO::PARAM_STR);
      $update->bindValue(':username', $username, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        echo 'success';
      } else {
        echo 'failure';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }
  
  private function updateMembership($name, $account, $email) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Help SET account = :account, name = :name WHERE userid = :userid";
      $update = $this->con->prepare($sql);
      $update->bindValue(':account', $account, PDO::PARAM_STR);
      $update->bindValue(':name', $name, PDO::PARAM_STR);
      $update->bindValue(':userid', $email, PDO::PARAM_STR);
      $update->execute();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getHelpData() {
    try {
      $sql = "SELECT userid, Q_FeedDB.Q2Help.* FROM Q_FeedDB.Q2Help";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $data = $all->fetchAll(PDO::FETCH_OBJ | PDO::FETCH_GROUP);
        foreach ($data as $user) {
          $is_member = $this->checkIfIsMember($user[0]->userid);
          if ($is_member !== 0) {
            $this->updateMembership($is_member, 'member', $user[0]->userid);
          }
        }
        return $data;
      } else {
        return $none[0]->none = 'NO ONE NEED HELP. SORRY.';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function getHelpMessages($data) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Help WHERE userid = :userid AND guest_key = :guest_key";
      $all = $this->con->prepare($sql);
      $all->bindValue(':userid', $data['email'], PDO::PARAM_STR);
      $all->bindValue(':guest_key', $data['key'], PDO::PARAM_STR);
      $all->execute();
      if ($all->rowCount() > 0) {
        return $all->fetchAll(PDO::FETCH_OBJ);
      } else {
        return $none[0]->none = 'NO ONE NEEDS HELP.';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function sendHelpSolution($data) {
    try {
      $name = $data['name'];
      $email = $data['email'];
      $query = $data['solution'];
      $key = $data['key'];
      $type = 'solution';
      $sql = "INSERT INTO Q_FeedDB.Q2Help (message, userid, type, guest_key, name) VALUES (:message, :userid, :type, :guest_key, :name)";
      $help = $this->con->prepare($sql);
      $help->bindParam(':message', $query, PDO::PARAM_STR);
      $help->bindParam(':userid', $email, PDO::PARAM_STR);
      $help->bindParam(':type', $type, PDO::PARAM_STR);
      $help->bindParam(':guest_key', $key, PDO::PARAM_STR);
      $help->bindParam(':name', $name, PDO::PARAM_STR);
      $help->execute();
      if ($help->rowCount() > 0) {
        $date = new DateTime();
        $success['success'] = "<tr class='white_bg'>"
                . "<td class='_w25 _pd5 blu'>Administrator</td>"
                . "<td class='_w25'>" . $date->format("Y-m-d H:i:s") . "</td>"
                . "<td class='_w50'>$query</td>"
                . "</tr>";
      } else {
        $success['failure'] = 'failed';
      }
      echo json_encode($success);
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  public function getFeedback() {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Feedback";
      $all = $this->con->prepare($sql);
      $all->execute();
      if ($all->rowCount() > 0) {
        $this->setFeedbackStatus();
        return $all->fetchAll(PDO::FETCH_OBJ);
      } else {
        return $none[0]->none = 'NO ONE CARES.';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function setFeedbackStatus() {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Feedback SET status = :read";
      $update = $this->con->prepare($sql);
      $update->bindValue(':read', 'read', PDO::PARAM_STR);
      $update->execute();
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

}
