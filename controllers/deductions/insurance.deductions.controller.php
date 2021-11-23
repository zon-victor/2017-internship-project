<?php

require_once 'all.deductions.controller.php';
require_once '../../models/deductions/insurance.deductions.model.php';
require_once '../../views/deductions/insurance.deductions.view.php';

class insuranceDeductionsController extends allDeductionsController {

    public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
        $this->model = new insuranceDeductionsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
        $this->view = new insuranceDeductionsView();
    }
    
    public function monthly($year, $month) {
        $category = 'insurance';
        $sm = $this->getSalaryMonth($year, $month);
        $deductions = $this->model->insuranceDeductionsPerMonth($year, $sm, $category);
        $yearly_deductions = $this->model->insuranceDeductionsPerYear();
        $this->view->renderInsuranceView($deductions, $year, $yearly_deductions, $category);
    }
    
    public function yearly($category, $year) {
      $this->per_year($year, $category);
    }
}

$home = new insuranceDeductionsController($DB_DSN, $DB_USER, $DB_PASSWORD);
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
    case '3':
        is_callable(array($home, $method)) ? $home->$method($_GET['P1'], $_GET['P2'],  $_GET['P3']) : $home->no_response();
        break;
    default:
        break;
}