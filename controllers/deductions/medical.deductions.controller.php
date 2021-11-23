<?php
require_once 'all.deductions.controller.php';
require_once '../../models/deductions/medical.deductions.model.php';
require_once '../../views/deductions/medical.deductions.view.php';

class medicalDeductionsController extends allDeductionsController {

    public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
        $this->model = new medicalDeductionsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
        $this->view = new medicalDeductionsView();
    }
    
    public function monthly($year, $month) {
        $category = 'Medical Aid';
        $sm = $this->getSalaryMonth($year, $month);
        $deductions = $this->model->medicalDeductionsPerMonth($year, $sm, $category);
        $yearly_deductions = $this->model->medicalDeductionsPerYear();
        $this->view->renderMedicalView($deductions, $year, $yearly_deductions, 'medical');
    }
    
    public function yearly($category, $year) {
      $category = $category.' aid';
      $this->per_year($year, $category);
    }
}

$home = new medicalDeductionsController($DB_DSN, $DB_USER, $DB_PASSWORD);
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
        is_callable(array($home, $method)) ? $home->$method($_GET['P1'], $_GET['P2'], $_GET['P3']) : $home->no_response();
        break;
    default:
        break;
}