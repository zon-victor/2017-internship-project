<?php

class accessModel extends errorsAndExceptionsLogger {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    try {
      parent::__construct($DB_DSN, $DB_USER, $DB_PASSWORD);
    } catch (PDOException $e) {
      $e->getMessage();
    }
  }

  private function sanitize($var) {
    $var1 = strip_tags($var);
    $var3 = trim($var1);
    return ($var3);
  }

  private function validateDomain($email, $payroll) {
    try {
      $domains = array(
          'publicservice.gov.za' => '2017000', //FAKE DOMAIN FOR PERSAL
          'qlink.co.za' => '2017001', //Q LINK
          'angloamerican.com' => '2017005', // TN ANGLO
          'toyota.co.za' => '2017002', //TOYOTA
          'eskom.co.za' => '2017007', //ESKOM
          'jhbcityparks.com' => '2017003', //CITY PARKS
          'citypower.co.za' => '2017004', //CITY POWER
          'goldfields.com' => '2017009', //GOLDFIELDS
          'hillside.co.za' => '2017006', //HILLSIDE ALUMINIUM
          'transnet.net' => '2017008', //TRANSNET
          'sappi.com' => '2017011', //SAPPI SA
          'whiskeycreek.co.za' => '2017010', //WHISKEYCREEK
          'gmail.com' => '2017012', //Associated with Testrun Gmail (Pty) Ltd
          'outlook.com' => '2017013'//Associated with Testrun Outlook (Pty) Ltd
      );
      $useremail = explode('@', $email);
      $domain = $useremail['1'];
      if (array_key_exists($domain, $domains)) {
        if ($domains[$domain] == $payroll) {
          return (1);
        } else {
          return (2);
        }
      } else {
        return (0);
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function rerouteMail($email) {
    $domains = array(
        'gmail.com' => 'sendto', //Associated with Testrun Gmail (Pty) Ltd
        'outlook.com' => 'sendto', //Associated with Testrun Outlook (Pty) Ltd
        'qlink.co.za' => 'sendto'//Associated with Q Link
    );

    $useremail = explode('@', $email);
    $domain = $useremail['1'];
    if (array_key_exists($domain, $domains)) {
      return (1);
    } else {
      return (0);
    }
  }

  private function validateEmployeeNumber($employee_no, $payroll) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Employee WHERE employee_no = :employee_no AND pid = :pid";
      $validate = $this->con->prepare($sql);
      $validate->bindValue(":employee_no", $employee_no, PDO::PARAM_INT);
      $validate->bindValue(":pid", $payroll, PDO::PARAM_INT);
      $validate->execute();
      return $validate->rowCount();
    } catch (PDOException $ex) {
      $this->logException($ex);
    }
  }

  private function passwordStrength($password) {
    $passed = 0;
    $strength['lowercase'] = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    $strength['uppercase'] = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $strength['numeric'] = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
    $strength['special'] = ['{', '}', '[', ']', '~', '`', '_', '^', '|', '!', '#', '$', '*', '-', '=', '+', '>', '<', ',', '.', '?', '/', ';', ':', '\\'];

    foreach ($strength as $required) {
      foreach ($required as $character) {
        if (strpos($password, $character)) {
          $passed++;
          break 1;
        }
      }
    }
    return $passed;
  }

  private function checkIfAccountExist($email) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE email=:email LIMIT 1";
      $login = $this->con->prepare($sql);
      $login->bindValue(":email", $email, PDO::PARAM_STR);
      $login->execute();
      if ($login->rowCount() > 0) {
        $employee = $login->fetch(PDO::FETCH_OBJ);
        if ($employee->altmail !== 'none') {
          $_SESSION['send_to'] = $employee->altmail;
        } else {
          $_SESSION['send_to'] = $email;
        }
        return $employee->fullname;
      } else {
        return 0;
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function validateInput($user) {
    try {
//THE REGISTRATION FORM
      $name = array_key_exists('payroll', $user) ? $this->sanitize($user['name']) : ""; // NAME OF THE EMPLOYEE
      $employer = array_key_exists('payroll', $user) ? $this->sanitize($user['payroll']) : ""; //EMPLOYER
      $employee_no = array_key_exists('employee_no', $user) ? $this->sanitize($user['employee_no']) : ""; //EMPLOYEE NUMBER
      $email = array_key_exists('email', $user) ? $this->sanitize($user['email']) : ""; //EMAIL
      $plain_password = array_key_exists('password', $user) ? $this->sanitize($user['password']) : ""; //PASSWORD
      $plain_password2 = array_key_exists('password2', $user) ? $this->sanitize($user['password2']) : ""; //PASSWORD AGAIN
      $error = []; //STORES ERRORS
      $allow = ['-', '_', '`', '~', "'", ' ']; //ALLOWED CHARACTERS IN ADDITION TO ALPHABETS

      if (null === $name || !ctype_alpha(str_replace($allow, '', $name))) {
        $error['name'] = 'Your name contains invalid characters';
      }

      if (null === $employer || !ctype_digit($employer)) {
        $error['employer'] = 'Please select an employer';
      }

      if (null === $employee_no || !ctype_alnum($employee_no)) {
        $error['employee_no'] = 'Employee number contains invalid characters';
      } elseif ($this->validateEmployeeNumber($employee_no, $employer) != 1) {
        $error['employee_no'] = 'Use your payslip and request information from your employeer';
      }

      if (null === email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Please enter a valid email address';
      }

      if (null === $plain_password || strlen($plain_password) > 15 || strlen($plain_password) < 8) {
        $error['password'] = 'Apply password rules for password length.';
      }

      if (null === $plain_password2 || strlen($plain_password2) > 15 || strlen($plain_password2) < 8) {
        $error['password2'] = 'Apply password rules for repeated password length';
      }

      if (strcmp($plain_password, $plain_password2) !== 0) {
        $error['password_match'] = 'Your passwords do no match';
      }

      if ($this->passwordStrength($plain_password) !== 4) {
        $error['password_strength'] = "Your password does not follow given rules.";
      }

      if ($this->checkIfAccountExist($email) !== 0) {
        $error['exist'] = 'This email is already in use';
      }

      if ($this->validateDomain($email, $employer) == 0) {
        $error['domain'] = "Please contact your employer for information you don't have.";
      } elseif ($this->validateDomain($email, $employer) == 2) {
        $error['domain'] = "Please contact your employer for information you don't have.";
      }

      if (empty($error)) {
        $userdata = ['name' => $name, 'payroll' => $employer, 'employee_no' => $employee_no, 'email' => $email, 'plain_password' => $plain_password];
        return ($userdata);
      } else {
        $error['errors'] = 'errors';
        return ($error);
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function generateUserid($email, $password) {
    $str1 = hash('sha512', $email . '2017 Internship' . $password . 'Victor Makhubele');
    $str2 = hash('sha512', 'QFeed project' . $password . $email . 'WeThickCode_' . $str1);
    $str3 = hash('sha512', $str1 . $str2 . 'Q LINK' . $password . $password);
    return ($str3);
  }

  private function encryptPassword($email, $password) {
    $pass1 = hash('sha512', 'QFeed password hash' . $email . $password . 'Q LINK Internship Project');
    $pass2 = hash('sha256', 'decrypt me' . $pass1 . $email . $password . '12345678910');
    $pass3 = hash('sha512', 'abc' . $pass2 . 'xyz' . $pass1 . 'The password is >>>' . $password . 'Email:' . $email);
    return ($pass3);
  }

  private function scramblePassword($username, $pass) {
    $pass1 = hash('sha512', 'QFeed Institution password' . $username . $pass . 'Q Link Internship Project 2017');
    $pass2 = hash('sha256', 'decrypt me' . $pass1 . $username . $pass . '123456789101112223233444555');
    $pass3 = hash('sha512', 'blah blah' . $pass2 . 'xyz' . $pass1 . 'The password is >>>' . $pass . $username);
    return ($pass3);
  }
  
  private function verificationCode($email, $password) {
    $str1 = mt_rand(100001, 960000);
    $str2 = mt_rand(0, 1000000);
    $str3 = hash('sha512', $str1 . $str2 . $email . $password);
    $code = substr($str3, 0, 6);
    return ($code);
  }

  private function sendVerificationEmail($name, $email, $vericode) {
    try {
      $link = '<a style="border:1px solid #333;text-decoration: none;color:#5d8aa8;font-size:13px;cursor:pointer;width:92px;height:24px;border-radius:8px;background-color:#ccc" href="http://qfeed.datatestserver.com/access/verify/' . $vericode . '/' . $email . '">Click Here</a>';
      $headers = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= 'From: access <no-reply@qfeed.datatestserver.com>' . "\r\n";
      $subject = 'Thank you for joining QFeed';
      $message = '<html><body style="color:#708090";font-size:12px;>';
      $message .= '<p>Welcome to QFeed, ' . $name . '</p>';
      $message .= '<p>Please ' . $link . ' to activate your account.</p>';
      $message .= '<div>Enjoy Your Stay</div>';
      $message .= '<div>The QFeed Team</div></body></html>';
      $mail = mail($email, $subject, $message, $headers);
      return ($mail);
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function registerUser($user) {
    try {
      $userdata = $this->validateInput($user);
      if (array_key_exists('errors', $userdata)) {
        $errors = '';
        foreach ($userdata as $key => $value) {
          if (strcmp($value, 'errors') !== 0) {
            $errors .= '<li>' . $value . '</li>';
          }
        }
        if (!empty($user['email'])) {
          $username = $this->sanitize($user['email']);
        } elseif (!empty($user['employee_no'])) {
          $username = $this->sanitize($user['employee_no']);
        } else {
          $username = 'Guest Email Not Available';
        }
        if (!empty($user['name'])) {
          $name = $this->sanitize($user['name']);
        } else {
          $name = 'Guest\'s Name Not Available';
        }
        $this->logError('registration', $errors, $username, $name, 'guest');
        return ($userdata);
      }
      $sector = $userdata['payroll'] == '2017000' ? 'government' : 'non-government';
      $userid = $this->generateUserid($userdata['email'], $userdata['plain_password']); //User ID
      $password = $this->encryptPassword($userdata['email'], $userdata['plain_password']); //Encrypted/Hashed Password
      $vericode = $this->verificationCode($userdata['email'], $userdata['plain_password']); //Account Verification Key
      $datetime = new DateTime('now');
      $regdate = $datetime->format("Y-m-d H:i:s"); // Date
      $verified = '0';
      $online = '0';
      if ($this->rerouteMail($userdata['email']) == 1) {
        $alternative = '0';
      } else {
        $alternative = '1';
      }
      $sql = "INSERT INTO Q_FeedDB.Q2Registry (userid, employee_no, vericode, email, password, verified, sector, payroll, fullname, online, regdate, alternative)
                    VALUES (:userid, :employee_no, :vericode, :email, :password, :verified, :sector, :payroll, :fullname, :online, :regdate, :alternative)";
      $register = $this->con->prepare($sql);
      $register->bindParam(':userid', $userid, PDO::PARAM_STR);
      $register->bindParam(':employee_no', $userdata['employee_no'], PDO::PARAM_INT);
      $register->bindParam(':vericode', $vericode, PDO::PARAM_STR);
      $register->bindParam(':email', $userdata['email'], PDO::PARAM_STR);
      $register->bindParam(':password', $password, PDO::PARAM_STR);
      $register->bindParam(':verified', $verified, PDO::PARAM_STR);
      $register->bindParam(':sector', $sector, PDO::PARAM_STR);
      $register->bindParam(':payroll', $userdata['payroll'], PDO::PARAM_STR);
      $register->bindParam(':fullname', $userdata['name'], PDO::PARAM_STR);
      $register->bindParam(':online', $online, PDO::PARAM_STR);
      $register->bindParam(':regdate', $regdate, PDO::PARAM_STR);
      $register->bindParam(':alternative', $alternative, PDO::PARAM_STR);
      $register->execute();
      if ($register->rowCount() > 0) {
        if ($alternative == '0') {
          if ($this->sendVerificationEmail($userdata['name'], $userdata['email'], $vericode) == FALSE) {
            $response['invalid'] = "failed to send an email to" . $userdata['email'];
          } else {
            $response['registered'] = 'registered';
            $response['name'] = $userdata['name'];
            $response['email'] = $userdata['email'];
            $_SESSION['access'] = 'verify';
          }
        } else {
          $response['reroute'] = 'reroute';
          $_SESSION['userid'] = $userid;
          $_SESSION['vericode'] = $vericode;
          $_SESSION['access'] = 'reroute';
        }
        $_SESSION['email'] = $userdata['email'];
      } else {
        $response['failure'] = 'failure';
      }
      return ($response);
    } catch (PDOException $ex) {
      $this->logException($ex);
    }
  }

  public function useAlternativeEmail($email0) {
    try {
      $email = $this->sanitize($email0);
      $sql = "UPDATE Q_FeedDB.Q2Registry SET altmail = :altmail WHERE userid = :userid";
      $update = $this->con->prepare($sql);
      $update->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $update->bindValue(':altmail', $email, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        $this->sendVerificationEmail($_SESSION['name'], $email, $_SESSION['vericode']);
        $_SESSION['access'] = 'unverified';
        $_SESSION['altmail'] = $email;
      } else {
        $_SESSION['access'] = 'verification error';
      }
      $url['redirect'] = '/';
      echo json_encode($url);
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function verifyUserAccount($vericode, $email0) {
    try {
      $email = $this->sanitize($email0);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['access'] = 'invalid email';
        redirect('/');
      }
      $verified = '1';
      $sql = "UPDATE Q_FeedDB.Q2Registry SET verified = :verified WHERE (email = :email OR altmail = :altmail) AND vericode = :vericode";
      $update = $this->con->prepare($sql);
      $update->bindValue(':verified', $verified, PDO::PARAM_INT);
      $update->bindValue(':email', $email, PDO::PARAM_STR);
      $update->bindValue(':altmail', $email, PDO::PARAM_STR);
      $update->bindValue(':vericode', $vericode, PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        $this->autoLogin($email);
      } else {
        $_SESSION['access'] = 'verification error';
        redirect('/');
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function autoLogin($email) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE (email=:email OR altmail = :altmail) LIMIT 1";
      $login = $this->con->prepare($sql);
      $login->bindValue(":email", $email, PDO::PARAM_STR);
      $login->bindValue(":altmail", $email, PDO::PARAM_STR);
      $login->execute();
      if ($login->rowCount() > 0) {
        $employee = $login->fetch(PDO::FETCH_OBJ);
        $_SESSION['userid'] = $employee->userid;
        $_SESSION['employee_no'] = $employee->employee_no;
        $_SESSION['pid'] = $employee->payroll;
        $_SESSION['account'] = 'user';
        $_SESSION['access'] = 'granted';
        $_SESSION['email'] = $employee->email;
        $this->checkVerification($employee->verified, $employee->altmail, $employee->email, $employee->alternative);
      } else {
        $_SESSION['access'] = 'denied';
        redirect('/');
      }
    } catch (Exception $exc) {
      echo $exc->getMessage();
    }
  }

  public function loginUser($email0, $plain_password) {
    try {
      $email = $this->sanitize($email0);
      $password = $this->encryptPassword($email, $plain_password);
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (strlen($password) > 7) {
          $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE email = :email AND password = :password AND access = :access LIMIT 1";
          $login = $this->con->prepare($sql);
          $login->bindParam(":email", $email, PDO::PARAM_STR);
          $login->bindParam(":password", $password, PDO::PARAM_STR);
          $login->bindValue(":access", 'yes', PDO::PARAM_STR);
          $login->execute();
          if ($login->rowCount() > 0) {
            $employee = $login->fetch(PDO::FETCH_OBJ);
            $_SESSION['userid'] = $employee->userid;
            $_SESSION['employee_no'] = $employee->employee_no;
            $_SESSION['pid'] = $employee->payroll;
            $_SESSION['sector'] = $employee->sector;
            $_SESSION['name'] = $employee->fullname;
            $_SESSION['email'] = $employee->email;
            $_SESSION['vericode'] = $employee->vericode;
            $_SESSION['account'] = 'user';
            $this->checkVerification($employee->verified, $employee->altmail, $email, $employee->alternative);
          } else {
            $_SESSION['access'] = 'denied';
            $this->logError('login', '<li>Access denied</li>', $email, 'Name not available', 'user');
            redirect('/');
          }
        } else {
          $_SESSION['access'] = 'denied';
          $this->logError('login', '<li>' . $error['password'] . '</li>', $email, 'Name not available', 'user');
           redirect('/');
        }
      } else {
        $_SESSION['access'] = 'denied';
        $this->logError('login', '<li>' . $error['email'] . '</li>', $email0, 'Name not available', 'user');
         redirect('/');
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function checkVerification($verified, $altmail, $email, $alternative) {
    if ($verified == '0' && $altmail == 'none' && $alternative == '0') {
      $_SESSION['access'] = 'unverified';
      $_SESSION['email'] = $email;
      $this->logError('verification', '<li>Unverified account</li>', $_SESSION['email'], $_SESSION['name'], 'user');
      redirect('/');
    } elseif ($verified == '0' && $altmail == 'none' && $alternative == '1') {
      $_SESSION['access'] = 'incomplete';
      $_SESSION['altmail'] = $altmail;
      redirect('/');
    } elseif ($verified == '0' && $altmail != 'none' && $alternative == '1') {
      $_SESSION['access'] = 'unverified';
      $_SESSION['altmail'] = $altmail;
      $this->logError('verification', '<li>Unverified account</li>', $_SESSION['email'], $_SESSION['name'], 'user');
      redirect('/');
    } elseif ($verified == '1') {
      $_SESSION['access'] = 'granted';
      redirect('/home');
    }
  }

  public function loginInstitution($username0, $plain_password) {
    try {
      $username = $this->sanitize($username0);
      $password = $this->scramblePassword($username, $plain_password);
      if (strlen($password) > 5) {
        $sql = "SELECT * FROM Q_FeedDB.Q2Institution WHERE username=:username AND password=:password AND access=:access LIMIT 1";
        $login = $this->con->prepare($sql);
        $login->bindParam(":username", $username, PDO::PARAM_STR);
        $login->bindParam(":password", $password, PDO::PARAM_STR);
        $login->bindValue(":access", "yes", PDO::PARAM_STR);
        $login->execute();
        if ($login->rowCount() > 0) {
          $institution = $login->fetch(PDO::FETCH_OBJ);
          $_SESSION['inst_id'] = $institution->inst_id;
          $_SESSION['institution'] = $institution->institution;
          $_SESSION['institution_abbr'] = $institution->institution_abbr;
          $_SESSION['inst_type'] = $institution->inst_type;
          $_SESSION['account'] = 'institution';
          redirect('/institution');
        } else {
          // $err['inst'] = 'Failed to login. Check username and/or password.';
          $this->logError('login', '<li>' . $err . '</li>', $username, $username, 'institution');
          // echo json_encode($err);]
          redirect('/');
        }
      } else {
        // $error['password'] = "Password length: 6 or characters please."; //Password is too short
        $this->logError('login', '<li>' . $error['password'] . '</li>', $username, $username, 'institution');
        // echo json_encode($error);
        redirect('/');
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  public function loginAdmin($username0, $plain_password) {
    try {
      $username = $this->sanitize($username0);
      $password = $this->scramblePassword($username, $plain_password);
      if (strlen($password) > 5) {
        $sql = "SELECT * FROM Q_FeedDB.Q2Admin WHERE username=:username AND password=:password LIMIT 1";
        $login = $this->con->prepare($sql);
        $login->bindValue(":username", $username, PDO::PARAM_STR);
        $login->bindValue(":password", $password, PDO::PARAM_STR);
        $login->execute();
        if ($login->rowCount() > 0) {
          $admin = $login->fetch(PDO::FETCH_OBJ);
          $_SESSION['admin_id'] = $admin->id_admin;
          $_SESSION['account'] = 'administrator';
          redirect('/home/admin');
        } else {
          $err['admin'] = 'Failed to login. Check username and/or password.';
          $this->logError('login', '<li>' . $err . '</li>', $username, $username, 'administrator');
          echo json_encode($err);
        }
      } else {
        $error['password'] = "Password length: 6 or characters please."; //Password is too short
        $this->logError('login', '<li>' . $error['password'] . '</li>', $username, $username, 'administrator');
        echo json_encode($error);
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function createOneTimeCode($email) {
    $str1 = mt_rand(100001, 520000);
    $str2 = mt_rand(0, 111000);
    $str3 = hash('sha256', $str1 . $email . $str2 . $email);
    $code = substr($str3, 0, 6);
    return ($code);
  }

  public function storeOneTimeCode($otc, $email) {
    try {
      $sql = "UPDATE Q_FeedDB.Q2Registry SET otc=:otc WHERE email=:email";
      $update = $this->con->prepare($sql);
      $update->bindValue(":email", $email, PDO::PARAM_STR);
      $update->bindValue(":otc", $otc, PDO::PARAM_STR);
      $update->execute();
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  public function forgotPassword($email0) {
    try {
      $email = $this->sanitize($email0);
      $name = $this->checkIfAccountExist($email);
      if ($name === 'failure') {
        $res['failure'] = '<p id="reset_next">ACCOUNT NOT FOUND. VERIFY YOU HAVE THE CORRECT EMAIL.</p>';
        echo json_encode($res);
        return;
      }
      $otc = $this->createOneTimeCode($email);
      $link = '<a style="border:1px solid #333;text-decoration: none;color:#5d8aa8;font-size:13px;cursor:pointer;width:92px;height:24px;border-radius:8px;background-color:#ccc" href="http://qfeed.datatestserver.com/access/reset/' . $otc . '/' . $email . '">Click Here</a>';
      $headers = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= 'From: access <no-reply@qfeed.datatestserver.com>' . "\r\n";
      $subject = 'Forgotten Password';
      $message = '<html><body style="color:#708090";font-size:12px;>';
      $message .= '<p>Seems like you need a new password, ' . $name . '</p>';
      $message .= '<p>Please ' . $link . ' to create a new password for your account.</p>';
      $message .= '<div>Enjoy Your Stay</div>';
      $message .= '<div>The QFeed Team</div></body></html>';
      $mail = mail($_SESSION['send_to'], $subject, $message, $headers);
      if ($mail == TRUE) {
        $this->storeOneTimeCode($otc, $email);
        $res['success'] = '<p id="reset_next">TO CHANGE YOUR PASSWORD, FOLLOW INSTRUCTIONS SENT TO'. $_SESSION['send_to'].'</p>';
        echo json_encode($res);
      } else {
        $res['failure'] = "<p id='reset_next'>FAILED TO SEND EMAIL TO $email.</p>";
        $this->logError('verification', "<li>Password reset: Failed to send email to $email</li>", $email, $name, 'user');
        echo json_encode($res);
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function validateResetLink($data) {
    if (empty($data['otc']) || empty($data['email'])) {
      $_SESSION['access'] = 'reset error';
      return (1);
    }

    if (null === $data['otc'] || !ctype_alnum($data['otc'])) {
      $_SESSION['access'] = 'reset error';
      return (1);
    }

    if (null === $data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $_SESSION['access'] = 'reset error';
      return (1);
    }

    $user['otc'] = $this->sanitize($data['otc']);
    $user['email'] = $this->sanitize($data['email']);
    return ($user);
  }

  public function verifyResetLink($data) {
    try {
      $user = $this->validateResetLink($data);
      if ($user !== 1) {
        $sql = "SELECT * FROM Q_FeedDB.Q2Registry WHERE email=:email AND otc=:otc LIMIT 1";
        $login = $this->con->prepare($sql);
        $login->bindValue(":email", $user['email'], PDO::PARAM_STR);
        $login->bindValue(":otc", $user['otc'], PDO::PARAM_STR);
        $login->execute();
        if ($login->rowCount() > 0) {
          $employee = $login->fetch(PDO::FETCH_OBJ);
          $_SESSION['otc'] = $user['otc'];
          $_SESSION['email'] = $user['email'];
          $_SESSION['name'] = $employee->fullname;
          $_SESSION['access'] = 'reset';
        } else {
          $this->logError('verification', '<li>Reset link is modified or already used</li>', $data['email'], $data['email'], 'user');
        }
      } else {
        $this->logError('verification', '<li>Reset link is modified and invalid</li><li>One Time Code: ' . $this->sanitize($data['otc']) . '</li>', $this->sanitize($data['email']), $data['email'], 'user');
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function validateResetFormInput($user) {
//VALIDATE RESET FORM INPUT
    if (array_key_exists('reset', $user) && array_key_exists('password', $user) && array_key_exists('password2', $user)) {
      $plain_password = array_key_exists('password', $user) ? $this->sanitize($user['password']) : ""; //PASSWORD
      $plain_password2 = array_key_exists('password2', $user) ? $this->sanitize($user['password2']) : ""; //PASSWORD AGAIN
    } else {
      $error['reset'] = 'An unexplainable error happened';
      goto end;
    }

    if (null === $plain_password || strlen($plain_password) > 15 || strlen($plain_password) < 8) {
      $error['password'] = 'Password length must be 8 to 15 characters long';
    }

    if (null === $plain_password2 || strlen($plain_password2) > 15 || strlen($plain_password2) < 8) {
      $error['password2'] = 'Repeated password length must be 8 to 15 characters long';
    }

    if (strcmp($plain_password, $plain_password2) !== 0) {
      $error['password_match'] = 'Passwords do no match';
    }

    if ($this->passwordStrength($plain_password) !== 4) {
      $error['password_strength'] = "Please apply password rules.";
    }

    end:
    if (empty($error)) {
      $password = ['plain_password' => $plain_password];
      return ($password);
    } else {
      $error['errors'] = 'errors';
      return ($error);
    }
  }

  public function resetPassword($user) {
    try {
      $password = $this->validateResetFormInput($user);
      if (!array_key_exists('plain_password', $password)) {
        $errors = '';
        $_SESSION['reset_errors'] = '';
        foreach ($password as $error) {
          if (strcmp($error, 'errors') !== 0) {
            $errors .= '<li>' . $error . '</li>';
            $_SESSION['reset_errors'] .= '<li>' . $error . '</li>';
          }
        }
        $this->logError('reset', $errors, $_SESSION['email'], 'Query the Q2Registry table', 'user');
        redirect('/');
        return;
      }
      $encrypted_password = $this->encryptPassword($_SESSION['email'], $password['plain_password']);
      $sql = "UPDATE Q_FeedDB.Q2Registry "
              . "SET password=:password, otc=:otc "
              . "WHERE email=:email";
      $update = $this->con->prepare($sql);
      $update->bindValue(":email", $_SESSION['email'], PDO::PARAM_STR);
      $update->bindValue(":password", $encrypted_password, PDO::PARAM_STR);
      $update->bindValue(":otc", "none", PDO::PARAM_STR);
      $update->execute();
      if ($update->rowCount() > 0) {
        $this->autoLogin($_SESSION['email']);
      } else {
        $error['errors'] = '<li>Failed to change your password</li>';
        $this->logError('reset', '<li>Failed to reset password</li>', $_SESSION['email'], 'Query the Q2Registry table', 'user');
        // session_destroy();
        redirect('/');
        return;
      }
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  private function checkIfGuestEmailExist($email) {
    try {
      $sql = "SELECT * FROM Q_FeedDB.Q2Help WHERE userid = :userid LIMIT 1";
      $check = $this->con->prepare($sql);
      $check->bindValue(":userid", $email, PDO::PARAM_STR);
      $check->execute();
      if ($check->rowCount() > 0) {
        $user = $check->fetch(PDO::FETCH_OBJ);
        return $user->guest_key;
      } else {
        return 'query';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function helpLogin($data) {
    try {
      $key = $this->sanitize($data['key']);
      $email = $this->sanitize($data['email']);
      $sql = "SELECT * FROM Q_FeedDB.Q2Help WHERE userid = :userid AND guest_key = :guest_key LIMIT 1";
      $check = $this->con->prepare($sql);
      $check->bindValue(":userid", $email, PDO::PARAM_STR);
      $check->bindValue(":guest_key", $key, PDO::PARAM_STR);
      $check->execute();
      if ($check->rowCount() > 0) {
        $user = $check->fetch(PDO::FETCH_OBJ);
        $_SESSION['help'] = 'active';
        $_SESSION['name'] = $user->name;
        $_SESSION['email'] = $email;
        $_SESSION['userid'] = $user->userid;
        $_SESSION['guest_key'] = $key;
        $res['success'] = '/access/help';
      } else {
        $res['failure'] = 'no_redirect';
      }
      echo json_encode($res);
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  private function sendHelpKey($name, $email, $key) {
    try {
      $headers = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= 'From: access <no-reply@qfeed.datatestserver.com>' . "\r\n";
      $subject = 'We received your query';
      $message = '<html><body style="color:#708090";font-size:12px;>';
      $message .= '<p>Please login to the help page using the credentials given below.';
      $message .= 'This will allow you to chat live with the admin and keep track of progress.</p>';
      $message .= '<div>Username: '.$email.'</div>';
      $message .= '<div>Help Key: '.$key.'</div>';
      $message .= '<br>';
      $message .= '<div>Enjoy your day</div>';
      $message .= '<div>The QFeed Team</div></body></html>';
      $mail = mail($email, $subject, $message, $headers);
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function helpQuery($data) {
    try {
      $name = $this->sanitize($data['name']);
      $email = $this->sanitize($data['email']);
      $query = $this->sanitize($data['query']);
      $type = $this->checkIfGuestEmailExist($email);
      if (strcmp($type, 'query') == 0) {
        $key = substr($this->createOneTimeCode($email), 0, 5);
        $this->sendHelpKey($name, $email, $key);
      } else {
        $key = $type;
        $type = 'followup';
      }
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
        $success['success'] = "<li class='chat_txt'>$name [" . $date->format("Y-m-d H:i:s") . "] >>>>  $query</li>";
      } else {
        $success['failure'] = 'failed';
      }
      echo json_encode($success);
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

  public function loadHelpMessages() {
    try {
      if (!isset($_SESSION['guest_key'])) {
        return $message['none'] = 'new_guest';
      }
      $sql = "SELECT * FROM Q_FeedDB.Q2Help WHERE userid = :userid AND guest_key = :guest_key";
      $check = $this->con->prepare($sql);
      $check->bindValue(":userid", $_SESSION['email'], PDO::PARAM_STR);
      $check->bindValue(":guest_key", $_SESSION['guest_key'], PDO::PARAM_STR);
      $check->execute();
      if ($check->rowCount() > 0) {
        return $check->fetchAll(PDO::FETCH_OBJ);
      } else {
        return $messages[0]->none = 'no help messages!';
      }
    } catch (Exception $exc) {
      $this->logException($exc);
    }
  }

  public function storeFeedback($data) {
    try {
      $sql = "INSERT INTO Q_FeedDB.Q2Feedback (feedback, userid, name) VALUES (:feedback, :userid, :name)";
      $help = $this->con->prepare($sql);
      $help->bindParam(':feedback', $data['feedback'], PDO::PARAM_STR);
      $help->bindParam(':userid', $_SESSION['userid'], PDO::PARAM_STR);
      $help->bindParam(':name', $_SESSION['name'], PDO::PARAM_STR);
      $help->execute();
      if ($help->rowCount() > 0) {
        $success['success'] = "<div class='chat_txt'>Thank you very much. Your feedback will help us offer better services.</div>";
      } else {
        $success['failure'] = 'failed';
      }
      echo json_encode($success);
    } catch (Exception $ex) {
      $this->logException($ex);
    }
  }

}
