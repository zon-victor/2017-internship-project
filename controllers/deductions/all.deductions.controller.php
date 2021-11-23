<?php
session_start();
if (!array_key_exists('userid', $_SESSION))
{
    header('Location: /');
}
require_once '../../common.php';
require_once '../../models/deductions/all.deductions.model.php';
require_once '../../views/deductions/all.deductions.view.php';

class allDeductionsController {

    public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD) {
        $this->model = new allDeductionsModel($DB_DSN, $DB_USER, $DB_PASSWORD);
        $this->view = new allDeductionsView();
    }
    
    public function all($year, $month) {
        $sm = $this->getSalaryMonth($year, $month);
        $deductions = $this->model->getDeductionsPerYear($year, $sm);
        $this->view->allPerYear($deductions, $year);
    }
    
    public function per_year($year, $category) {
        $sm = $this->getSalaryMonth($year, 'none');
        if ($sm == $year) {
            $deductions = $this->model->getYearlyDeductionsPerType($year, $category);
            $this->view->allPerYear($deductions, $year);
        }
    }
    
    public function getSalaryMonth($year, $month) {
        switch ($month) {
            case 'none':
                $sm = $year;
                break;
            case 'jan':
                $sm = $year.'01';
                break;
            case 'feb':
                $sm = $year.'02';
                break;
            case 'mar':
                $sm = $year.'03';
                break;
            case 'apr':
                $sm = $year.'04';
                break;
            case 'may':
                $sm = $year.'05';
                break;
            case 'jun':
                $sm = $year.'06';
                break;
            case 'jul':
                $sm = $year.'07';
                break;
            case 'aug':
                $sm = $year.'08';
                break;
            case 'sep':
                $sm = $year.'09';
                break;
            case 'oct':
                $sm = $year.'10';
                break;
            case 'nov':
                $sm = $year.'11';
                break;
            case 'dec':
                $sm = $year.'12';
                break;
            default:
                $sm = '0';
                break;
        }
        return $sm;
    }
    
    public function no_response() {
        return;
    }
}

$home = new allDeductionsController($DB_DSN, $DB_USER, $DB_PASSWORD);
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