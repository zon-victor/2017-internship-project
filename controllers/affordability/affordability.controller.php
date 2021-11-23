<?php

session_start();
include_once '/QFeed/common.php';
include_once '/QFeed/models/affordability/affordability.model.php';
include_once '/QFeed/views/affordability/affordability.view.php';
class affordabilityController {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    $this->model = new affordabilityModel($DB_DSN, $DB_USER, $DB_PASSWORD);
    $this->view = new affordabilityView();
  }
  
  public function render() {
    $this->view->renderAffordabilityData();
  }
  
  public function saved() {
    $tests = $this->model->getSavedTests();
    $this->view->renderSaved($tests);
  }
  
  public function test($data) {
    $feedback = $this->model->calculateAffordability($data);
    if (array_key_exists('outcome', $feedback)) {
      $this->view->displayResult($feedback);
    } else {
      echo $feedback;
    }
  }
  
  public function save($data) {
    $this->model->saveResults($data);
  }
  
  public function update($data) {
    $data['service_name'] = $data['name'];
    $data['amount'] = $data['value'];
    $data['net_salary'] = $data['cnet'];
    $data['id_affordability'] = $data['P1'];
    if ($data['cat'] !== 'insurance') {
      $data['category'] = 'other';
    } else {
      $data['category'] = 'insurance';
    }
    $feedback = $this->model->calculateAffordability($data);
    if (array_key_exists('outcome', $feedback) && ($feedback['outcome'] === 'pass' || $feedback['outcome'] === 'failed')) {
      $data['outcome'] = $feedback['outcome'];
      $data['category'] = $data['cat'];
      $this->model->updateResults($data);
    }
  }
  
  public function view($data) {
    $id = $data['P1'];
    $test = $this->model->getSavedTest($id);
    $this->view->displayTest($test);
  }
  
  public function delete($data) {
   $this->model->deleteExistingTest($data);
  }
  
  public function no_response() {
    echo 'What?';
  }

}

$affordability = new affordabilityController($DB_DSN, $DB_USER, $DB_PASSWORD);
$method = (string)filter_input(INPUT_GET, 'METHOD');

null !== $_POST && count($_POST) != 0 ? $postdata = $_POST : $postdata = null;
$postdata !== null ? $data = array_merge($_GET, $postdata) : $data = $_GET;
is_callable(array($affordability, $method)) ? $affordability->$method($data) : $affordability->no_response();
