<?php
require_once 'all.deductions.controller.php';
require_once '../../models/deductions/mas.deductions.model.php';
require_once '../../views/deductions/mas.deductions.view.php';

class masDeductionsController extends allDeductionsController {

    public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
        $this->model = new masDeductionsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
        $this->view = new masDeductionsView();
    }
    
    public function monthly($year, $month) {
        $category = 'Mas';
        $sm = $this->getSalaryMonth($year, $month);
        $deductions = $this->model->masDeductionsPerMonth($year, $sm, $category);
        $yearly_deductions = $this->model->masDeductionsPerYear();
        $this->view->renderMasView($deductions, $year, $yearly_deductions, $category);
    }
    
    public function yearly($category, $year) {
      $category === 'mas' ? $category = 'Mas' : $category = 'none';
      if ($category !== 'none'){
        $this->per_year($year, $category);
      }
    }
}

$home = new masDeductionsController($DB_DSN, $DB_USER, $DB_PASSWORD);
$method = $_GET['METHOD'];
$params = $_GET['PARAMS'];

switch ($params) {
    case '0':
        is_callable(array($home, $method)) ? $home->$method() : $home->no_response();
        break;
    case '1':
        is_callable(array($home, $method)) ? $home->$method($_GET['P1']) : $home->no_response();
        break;
    case '2':
        is_callable(array($home, $method)) ? $home->$method($_GET['P1'], $_GET['P2']) : $home->no_response();
        break;
    default:
        break;
}