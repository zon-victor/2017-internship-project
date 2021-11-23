<?php

require_once 'all.deductions.model.php';

class insuranceDeductionsModel extends allDeductionsModel {
    
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
    
    public function insuranceDeductionsPerMonth($year, $sm, $category)
    {
        return $this->getDeductionsPerMonth($year, $sm, $category);
    }
    
    public function insuranceDeductionsPerYear()
    {
        $category = 'insurance';
        return $this->categoryDeductionsPerYear($category);
    }
    
    public function deductionPerYear($year, $category) {
      $this->getYearlyDeductionsPerType($year, $category);
    }
        
}
