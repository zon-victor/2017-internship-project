<?php

session_start();
include_once '/QFeed/common.php';
include_once '/QFeed/models/savings/savings.model.php';
include_once '/QFeed/views/savings/savings.view.php';

class savingsController {

  public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
    $this->model = new savingsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
    $this->view = new savingsView();
  }

  public function render() {
    $this->view->renderTargetBasedCalculator();
  }

  public function target() {
    $this->view->renderTargetBasedCalculator();
  }
  
  public function term() {
    $this->view->renderTermBasedCalculator();
  }
  
  public function calculate($data) {
    $this->model->calculateSavings($data);
  }

  public function initialized() {
    $initialized = $this->model->getSpecificGoals('initialized');
    if (array_key_exists('none', $initialized)) {
      echo json_encode($initialized);
    } else {
      $this->view->renderGoals($initialized, 'initialized');
    }
  }

  public function uninitialized() {
    $uninitialized = $this->model->getSpecificGoals('uninitialized');
    if (array_key_exists('none', $uninitialized)) {
      echo json_encode($uninitialized);
    } else {
      $this->view->renderGoals($uninitialized, 'uninitialized');
    }
  }

  public function reached() {
    $reached = $this->model->getSpecificGoals('reached');
    if (array_key_exists('none', $reached)) {
      echo json_encode($reached);
    } else {
      $this->view->renderGoals($reached, 'reached');
    }
  }
  
  public function status() {
    $this->view->renderGroupedGoals();
  }
  
  public function delete($data) {
    if (array_key_exists('P1', $data) && ($data['P1'] == 'uninitialized' || $data['P1'] == 'initialized' || $data['P1'] == 'reached')) {
      $this->model->deleteAllGoals($data['P1']);
    }
  }
  
  public function remove($data) {
      $this->model->deleteGoal($data['P1'], $data['P2']);
  }
  
  public function goal($data) {
    if (array_key_exists('P1', $data)) {
      $goal = $this->model->getGoal($data['P1']);
      if ($data['P2'] == 'uninitialized') {
        $this->view->renderUninitializedGoalDetails($goal);
      } else if ($data['P2'] == 'initialized') {
        $this->view->renderInitializedGoalDetails($goal);
      } else if ($data['P2'] == 'reached') {
        $this->view->renderReachedGoalDetails($goal);
      }
    }
  }
  
  public function start_all($data) {
    $uninitialized = $this->model->getSpecificGoals('uninitialized');
    if (array_key_exists('none', $uninitialized)) {
      echo json_encode($uninitialized);
    } else {
      foreach ($uninitialized as $goal) {
        $goal['id'] = $goal['id_budget'];
        $period = explode('.', $goal['period']);
        if ($period[0] > 1) {
          $years = 'years';
        } else {
          $years = 'year';
        }

        if ($period[1] > 1) {
          $months = 'months';
        } else {
          $months = 'month';
        }
        $goal['period'] = "$period[0] $years and $period[1] $months";
        $this->model->initializeGoal($goal);
      }
    }
  }
  
  public function initialize($data) {
    $this->model->initializeGoal($data);
  }

  public function no_response() {
    echo 'What?';
  }

}

$savings = new savingsController($DB_DSN, $DB_USER, $DB_PASSWORD);
$method = (string) filter_input(INPUT_GET, 'METHOD');

null !== $_POST && count($_POST) != 0 ? $postdata = $_POST : $postdata = null;
$postdata !== null ? $data = array_merge($_GET, $postdata) : $data = $_GET;
is_callable(array($savings, $method)) ? $savings->$method($data) : $savings->no_response();
