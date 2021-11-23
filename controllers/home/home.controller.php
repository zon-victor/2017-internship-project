<?php

session_start();

include_once '/QFeed/common.php';
include_once '/QFeed/errors_and_exceptions.php';

include_once '/QFeed/views/home/home.view.php';
include_once '/QFeed/models/home/home.model.php';

class homeController {

  public $restrict;
  public $access;
  public $users;
  public $errors;
  public $exceptions;
  public $feedbacks;
  public $queries;
  public $stats = 'on';

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    $this->model = new homeModel($DB_DSN, $DB_USER, $DB_PASSWORD);
    $this->view = new homeView();
  }

  public function home() {
    $this->noAccess();
    $this->view->home();
  }
  
  private function noAccess() {
    if (!$this->access) {
      redirect('/');
    }
  }
  
  private function offLimit() {
    if ($this->restrict) {
      redirect('/');
    }
  }

  public function admin() {
    $this->offLimit();
    $this->stats = 'off';
    $this->stats();
    $this->view->adminPanel($this->users, $this->errors, $this->exceptions, $this->feedbacks, $this->queries);
  }

  public function stats() {
    $this->offLimit();
    $this->users['verified'] = $this->model->countRegisteredAccounts('1');
    $this->users['unverified'] = $this->model->countRegisteredAccounts('0');
    $this->users['enabled'] = $this->model->countActiveAccounts('yes');
    $this->users['disabled'] = $this->model->countActiveAccounts('no');
    $this->errors['registration'] = $this->model->countErrors('registration');
    $this->errors['login'] = $this->model->countErrors('login');
    $this->errors['verification'] = $this->model->countErrors('verification');
    $this->errors['url'] = $this->model->countErrors('url');
    $this->exceptions['solved'] = $this->model->countExceptions('solved');
    $this->exceptions['unsolved'] = $this->model->countExceptions('unsolved');
    $this->feedbacks['new'] = $this->model->countFeedbacks('unread');
    $this->feedbacks['viewed'] = $this->model->countFeedbacks('read');
    $this->queries['new'] = $this->model->countQueriesByStatus('unread');
    $this->queries['viewed'] = $this->model->countQueriesByStatus('read');
    $this->queries['unanswered'] = $this->model->countQueriesByState('unanswered');
    $this->queries['answered'] = $this->model->countQueriesByState('answered');
    $this->queries['visitors'] = $this->model->countQueriesByMembership('visitor');
    $this->queries['members'] = $this->model->countQueriesByMembership('member');
    if ($this->stats == 'on') {
      $this->view->renderStats($this->users, $this->errors, $this->exceptions, $this->feedbacks, $this->queries);
    }
  }

  public function accounts($data) {
    $this->offLimit();
    if ($data['P1'] == 'users') {
      $users = $this->model->getUsers();
      $this->view->renderUserAccounts($users);
    } elseif ($data['P1'] == 'institutions') {
      $institutions = $this->model->getInstitutions();
      $this->view->renderInstitutionAccounts($institutions);
    } else {
      $users = $this->model->getUsers();
      $this->view->renderAccounts($users);
    }
  }

  public function access($data) {
    $this->offLimit();
    if ($data['P1'] == 'user') {
      $this->model->toggleUserAccess($data['access'], $data['email']);
    } elseif ($data['P1'] == 'institution') {
      $this->model->toggleInstitutionAccess($data['access'], $data['username']);
    }
  }

  public function help($data) {
    $this->offLimit();
    if (array_key_exists('P1', $data) && $data['P1'] == 'user' && array_key_exists('email', $data) && array_key_exists('key', $data)) {
      $messages = $this->model->getHelpMessages($data);
      $this->view->renderHelpMessages($messages);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'solution' && array_key_exists('email', $data) && array_key_exists('key', $data)) {
      $messages = $this->model->sendHelpSolution($data);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'render') {
      $users = $this->model->getHelpData();
      $this->view->renderHelp($users);
    }
  }

  public function feedback($data) {
    if (array_key_exists('P1', $data) && $data['P1'] == 'render') {
      $messages = $this->model->getFeedback();
      $this->view->renderFeedback($messages);
    }
  }

  public function errors($data) {
    $this->offLimit();
    if (array_key_exists('P1', $data) && $data['P1'] == 'delete' && array_key_exists('multi', $data) && $data['multi'] == 'no') {
      $this->model->deleteError($data['error_id']);
      $errors = $this->model->getErrors();
      $this->view->renderErrors($errors);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'delete' && array_key_exists('multi', $data) && $data['multi'] == 'yes') {
      $this->model->deleteErrors();
      $errors = $this->model->getErrors();
      $this->view->renderErrors($errors);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'render') {
      $errors = $this->model->getErrors();
      $this->view->renderErrors($errors);
    }
  }

  public function exceptions($data) {
    $this->offLimit();
    if (array_key_exists('P1', $data) && $data['P1'] == 'update') {
      $this->model->setExceptionStatus($data['line'], $data['filename'], $data['status']);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'delete' && array_key_exists('multi', $data) && $data['multi'] == 'no') {
      $this->model->deleteExceptionsByLineAndFilename($data['line'], $data['filename']);
      $exceptions = $this->model->getExceptions();
      $this->view->renderExceptions($exceptions);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'delete' && array_key_exists('multi', $data) && $data['multi'] == 'yes') {
      $this->model->deleteAllExceptions();
      $exceptions = $this->model->getExceptions();
      $this->view->renderExceptions($exceptions);
    } elseif (array_key_exists('P1', $data) && $data['P1'] == 'render') {
      $exceptions = $this->model->getExceptions();
      $this->view->renderExceptions($exceptions);
    }
  }

  public function years() {
    $this->view->reload_years();
  }

  public function deductions() {
    $this->view->deductions();
  }

  public function no_response() {
    echo 'Missing page!';
  }

}

$home = new homeController($DB_DSN, $DB_USER, $DB_PASSWORD);
$method = filter_input(INPUT_GET, 'METHOD');
$params = filter_input(INPUT_GET, 'PARAMS');

if (array_key_exists('account', $_SESSION) && $_SESSION['account'] === 'user'
        && array_key_exists('access', $_SESSION) && $_SESSION['access'] === 'granted') { 
  $home->restrict = true;
  $home->access = true;
} elseif (array_key_exists('account', $_SESSION) && $_SESSION['account'] === 'administrator'
        && array_key_exists('admin_id', $_SESSION)) {
  $home->restrict = false;
} else {
  $home->restrict = true;
  $home->access = false;
  
}

null !== $_POST && count($_POST) != 0 ? $postdata = $_POST : $postdata = NULL;
$postdata !== null ? $postdata = array_merge($_GET, $postdata) : $postdata = $_GET;

is_callable(array($home, $method)) ? $home->$method($postdata) : $home->no_response();
