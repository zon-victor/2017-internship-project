<?php

session_start();
include_once '/QFeed/common.php';
include_once '/QFeed/errors_and_exceptions.php';

$method = filter_input(INPUT_GET, 'METHOD');

if (array_key_exists('userid', $_SESSION) && array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'granted'
        && ((array_key_exists('METHOD', $_GET) && ($method == 'feedback' || $method == 'help')))) {
          ;
} elseif (array_key_exists('account', $_SESSION) && $_SESSION['account'] === 'administrator') {
  redirect("/home/admin");
} elseif (array_key_exists('userid', $_SESSION) && array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'granted') {
  redirect("/home");
} 

include_once '/QFeed/models/access/access.model.php';
include_once '/QFeed/views/access/access.view.php';

class AccessControl {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    $this->model = new accessModel($DB_DSN, $DB_USER, $DB_PASSWORD);
    $this->view = new accessView();
  }

  public function welcome() {
    if (array_key_exists('otc', $_SESSION) && array_key_exists('email', $_SESSION) && array_key_exists('name', $_SESSION)) {
      $this->view->passwordResetPage($_SESSION['name']);
    } else {
      $this->view->welcome();
    }
  }

  public function register($data) {
    $res = $this->model->registerUser($data);
    if (array_key_exists('errors', $res)) {
      $this->view->returnRegistrationErrors($res);
    } elseif (array_key_exists('reroute', $res)) {
      $this->view->alternativeEmail($res['name']);
    } elseif (array_key_exists('registered', $res)) {
      $this->view->verifyAccount($res['name'], $res['email']);
    } elseif (array_key_exists('invalid', $res)) {
      echo json_encode($res);
    } else {
      $this->view->registrationFailed();
    }
  }

  public function alternative($data) {
    $email = $data['altmail'];
    if ($email !== NULL)
    { 
      $this->model->useAlternativeEmail($email);
    }
  }

  public function verify($data) {
    $vericode = $data['P1'];
    $email = $data['P2'];
    $this->model->verifyUserAccount($vericode, $email);
  }

  public function login($data) {
    if ($data['account'] == 'user') {
      $this->model->loginUser($data['handle'], $data['password']);
    } elseif ($data['account'] == 'institution') {
      $this->model->loginInstitution($data['handle'], $data['password']);
    } elseif ($data['account'] == 'admin') {
      $this->model->loginAdmin($data['handle'], $data['password']);
    }
  }

  public function logout() {
    session_destroy();
    redirect('/');
  }

  public function reset($data) {
    if (array_key_exists('init', $data)) {
      $this->model->forgotPassword($data['init']);
    } elseif (array_key_exists('reset', $data) && array_key_exists('password', $data) && array_key_exists('password2', $data)) {
      $this->model->resetPassword($data);
    } elseif (array_key_exists('P1', $data) && null !== $data['P1'] && array_key_exists('P2', $data) && null !== $data['P2']) {
      $user['otc'] = $data['P1'];
      $user['email'] = $data['P2'];
      $this->model->verifyResetLink($user);
      redirect('/');
    } else {
      $this->logout();
    }
  }
  
  public function help($data) {
    if (!empty($data) && array_key_exists('P1', $data) && $data['P1'] === 'login') {
      $this->model->helpLogin($data);
    } elseif (!empty($data) && array_key_exists('P1', $data) && $data['P1'] === 'query') {
      $this->model->helpQuery($data);
    } else {
      $messages = $this->model->loadHelpMessages();
      $this->view->helpPage($messages);
    }
  }
  
  public function feedback($data) {
    if (array_key_exists('P1', $data) && $data['P1'] === 'user') {
      $this->model->storeFeedback($data);
    } else {
      $this->view->feedBack();
    }
  }

  public function no_response() {
    echo 'No response! <(-_-)>';
  }

}

$access = new AccessControl($DB_DSN, $DB_USER, $DB_PASSWORD);

null !== $_POST && count($_POST) != 0 ? $postdata = $_POST : $postdata = NULL;
$postdata !== null ? $data = array_merge($_GET, $postdata) : $data = $_GET;
is_callable(array($access, $method)) ? $access->$method($data) : $access->no_response();
