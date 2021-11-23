<?php

session_start();
include_once '/QFeed/common.php';
if (!array_key_exists('inst_id', $_SESSION)) {
  redirect('/');
}
include_once '/QFeed/errors_and_exceptions.php';
include_once '/QFeed/models/institutions/institutions.model.php';
include_once '/QFeed/views/institutions/institutions.view.php';

class institutionsController {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    $this->model = new institutionsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
    $this->view = new institutionsView();
  }

  public function institution() {
    if (!array_key_exists('year', $_SESSION)) {
      $_SESSION['year'] = 2017;
    }
    $data = $this->model->getDeductionsPerYear();
    $this->view->loadDefaultView($data);
  }

  public function deductions($data) {
    if (array_key_exists('P1', $data) && $data['P1'] === 'history' && array_key_exists('P2', $data) && $data['P2'] === 'render') {
      $data = $this->model->getDeductionsPerYear();
      $this->view->renderDeductions($data);
    } elseif (array_key_exists('P1', $data) && $data['P1'] === 'history' && array_key_exists('P2', $data)) {
      $_SESSION['year'] = $data['P2'];
      $data = $this->model->getDeductionsPerYear();
      $this->view->renderDeductionsPerYear($data);
    } elseif (array_key_exists('P1', $data) && $data['P1'] === 'year') {
      if (!array_key_exists('year', $_SESSION)) {
        $_SESSION['year'] = 2017;
      }
      echo $_SESSION['year'];
    }
  }
  
  public function manage($data) {
    if (array_key_exists('P1', $data) && array_key_exists('P2', $data)
            && $data['P1'] === 'render' && $data['P2'] === 'file') {
      $this->view->renderDeductionsFileManagementInterface();
    } else if (array_key_exists('P1', $data) && array_key_exists('P2', $data)
            && $data['P1'] === 'render' && $data['P2'] === 'online') {
      //$this->view->renderDeductionsOnlineManagementInterface('online');
    } else if (array_key_exists('P1', $data) && $data['P1'] === 'render') {
      $this->view->renderDeductionsManagementInterface();
    }
  }

  public function no_response() {
    echo 'Missing page!';
  }

}

$institutions = new institutionsController($DB_DSN, $DB_USER, $DB_PASSWORD);
$method = filter_input(INPUT_GET, 'METHOD');

null !== $_POST && count($_POST) != 0 ? $postdata = $_POST : $postdata = NULL;
$postdata !== null ? $postdata = array_merge($_GET, $postdata) : $postdata = $_GET;

is_callable(array($institutions, $method)) ? $institutions->$method($postdata) : $institutions->no_response();
