<?php

class allDeductionsModel {
    
    public function __construct($DB_DSN, $DB_USER, $DB_PASSWORD)
    {
        try {
            $con = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con = $con;
        }catch(PDOException $e){
            $e->getMessage();
        }
    }
    
    public function getDeductionsPerYear($year, $sm)
    {
        try {
            $employee_no = $_SESSION['employee_no'];
            $pid = $_SESSION['pid'];
            $sql = "SELECT salary_month, Q_FeedDB.Q2Deductions.*"
                    . "FROM Q_FeedDB.Q2Deductions "
                    . "WHERE employee_no=:employee_no "
                    . "AND pid=:pid "
                    . "AND salary_month "
                    . "LIKE :salary_month";
            $get = $this->con->prepare($sql);
            $get->bindValue(":employee_no", $employee_no, PDO::PARAM_INT);
            $get->bindValue(":pid", $pid, PDO::PARAM_INT);
            $get->bindValue(":salary_month", $sm."%", PDO::PARAM_INT);
            $get->execute();
            if ($get->rowCount() > 0) {
                $deductions = $get->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_OBJ);
                foreach ($deductions as $deduction) {
                    foreach ($deduction as $deducted) {
                        $deducted->institution = $this->getInstitution($deducted->inst_id);
                        $deducted->month = $this->salaryMonth($year, $deducted->salary_month);
                    }
                }
                return $deductions;
            }  else {
              $ded['none'] = 'No deductions';
              return $ded;
            }
         }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    public function getYearlyDeductionsPerType($year, $category)
    {
        try {
            $employee_no = $_SESSION['employee_no'];
            $pid = $_SESSION['pid'];        
            $sql = "SELECT salary_month, Q_FeedDB.Q2Deductions.*"
                    . "FROM Q_FeedDB.Q2Deductions "
                    . "WHERE employee_no=:employee_no "
                    . "AND pid=:pid "
                    . "AND category=:category "
                    . "AND salary_month "
                    . "LIKE :salary_month";
            $get = $this->con->prepare($sql);
            $get->bindValue(":employee_no", $employee_no, PDO::PARAM_INT);
            $get->bindValue(":pid", $pid, PDO::PARAM_INT);
            $get->bindValue(":category", $category, PDO::PARAM_STR);
            $get->bindValue(":salary_month", $year."%", PDO::PARAM_INT);
            $get->execute();
            if ($get->rowCount() > 0) {
                $deductions = $get->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_OBJ);
                foreach ($deductions as $deduction) {
                    foreach ($deduction as $deducted) {
                        $deducted->institution = $this->getInstitution($deducted->inst_id);
                        $deducted->month = $this->salaryMonth($year, $deducted->salary_month);
                    }
                }
                return $deductions;
            } else {
              $ded['none'] = "No $category deductions";
              return $ded;
            }
         }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    public function sumYearlyDeductionsPerCategory($year, $category)
    {
        try {
            $employee_no = $_SESSION['employee_no'];
            $pid = $_SESSION['pid'];        
            $sql = "SELECT sum(amount) AS total "
                    . "FROM Q_FeedDB.Q2Deductions "
                    . "WHERE employee_no=:employee_no "
                    . "AND pid=:pid "
                    . "AND category=:category "
                    . "AND salary_month LIKE :salary_month ";
            $get = $this->con->prepare($sql);
            $get->bindValue(":employee_no", $employee_no, PDO::PARAM_INT);
            $get->bindValue(":pid", $pid, PDO::PARAM_INT);
            $get->bindValue(":salary_month", $year."%", PDO::PARAM_INT);
            $get->bindValue(":category", $category, PDO::PARAM_STR);
            $get->execute();
            if ($get->rowCount() > 0) {
                $deductions = $get->fetchAll(PDO::FETCH_OBJ);
                return $deductions;
            } else {
                echo 'failed';
            }
         }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    public function salaryMonth($year, $salary_month) {
         switch ($salary_month) {
            case $year.'01':
                $month = 'JANUARY';
                break;
            case $year.'02':
                $month = 'FEBRUARY';
                break;
            case $year.'03':
                $month = 'MARCH';
                break;
            case $year.'04':
                $month = 'APRIL';
                break;
            case $year.'05':
                $month = 'MAY';
                break;
            case $year.'06':
                $month = 'JUNE';
                break;
            case $year.'07':
                $month = 'JULY';
                break;
            case $year.'08':
                $month = 'AUGUST';
                break;
            case $year.'09':
                $month = 'SEPTEMBER';
                break;
            case $year.'10':
                $month = 'OCTOBER';
                break;
            case $year.'11':
                $month = 'NOVEMBER';
                break;
            case $year.'12':
                $month = 'DECEMBER';
                break;
            default:
                break;
        }
        return $month;
    }
    
    public function getInstitution($inst_id)
    {
     try {
            $sql = "SELECT * FROM Q_FeedDB.Q2Institution WHERE inst_id=:inst_id";
            $get = $this->con->prepare($sql);
            $get->bindValue(":inst_id", $inst_id, PDO::PARAM_INT);
            $get->execute();
            if ($get->rowCount() > 0) {
                $inst = $get->fetch(PDO::FETCH_OBJ);
                if ($inst->institution_abbr == 'none'):
                    return $inst->institution;
                else:
                    return $inst->institution_abbr;
                endif;
            } else {
                echo 'failed';
            }
         }catch(PDOException $e){
            echo $e->getMessage();
        } 
    }
        
    public function getDeductionsPerMonth($year, $sm, $category)
    {
        try {
            $employee_no = $_SESSION['employee_no'];
            $pid = $_SESSION['pid'];
            $sql = "SELECT salary_month, Q_FeedDB.Q2Deductions.* FROM Q_FeedDB.Q2Deductions "
                    . "WHERE employee_no=:employee_no "
                    . "AND pid=:pid "
                    . "AND category=:category "
                    . "AND salary_month=:salary_month ";
            $get = $this->con->prepare($sql);
            $get->bindValue(":employee_no", $employee_no, PDO::PARAM_INT);
            $get->bindValue(":pid", $pid, PDO::PARAM_INT);
            $get->bindValue(":category", $category, PDO::PARAM_STR);
            $get->bindValue(":salary_month", $sm, PDO::PARAM_INT);
            $get->execute();
            if ($get->rowCount() > 0) {
                $deductions = $get->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_OBJ);
                foreach ($deductions as $deduction) {
                    foreach ($deduction as $deducted) {
                        $deducted->institution = $this->getInstitution($deducted->inst_id);
                        $deducted->month = $this->salaryMonth($year, $sm);
                    }
                }
                return $deductions;
            } else {
                echo 'NO MONTHLY '.strtoupper($category).' DEDUCTIONS';
            }
         }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    public function categoryDeductionsPerYear($category)
    {
        $years = ['2013', '2014', '2015', '2016', '2017'];
        $total = [];
        foreach ($years as $year) {
            $total[$year] = $this->sumYearlyDeductionsPerCategory($year, $category);
        }
        return $total;
    }
}
