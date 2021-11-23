<?php

require_once 'all.deductions.model.php';

class masDeductionsModel extends allDeductionsModel {
    
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
    
    public function masDeductionsPerMonth($year, $sm, $category)
    {
        return $this->getDeductionsPerMonth($year, $sm, $category);
    }
    
    public function masDeductionsPerYear()
    {
        $category = 'Mas';
        return $this->categoryDeductionsPerYear($category);
    }
        
}
