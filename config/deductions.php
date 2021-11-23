<?php

$deductions = [];
$count = 0;

require_once 'deductions/2013.deductions.php';
require_once 'deductions/2014.deductions.php';
require_once 'deductions/2015.deductions.php';
require_once 'deductions/2016.deductions.php';
require_once 'deductions/2017.deductions.php';

require_once 'database.php';

try 
{
    $conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    foreach ($deductions as $deduction) {
            foreach ($deduction as $deducted) {
                $sql = "INSERT INTO Q_FeedDB.Q2Deductions (employee_no, category, inst_id, pid, amount, reason, status, salary_month)
                            VALUES (:employee_no, :category, :inst_id, :pid, :amount, :reason, :status, :salary_month)";
                $testdata = $conn->prepare($sql);
                $testdata->bindParam(':employee_no', $deducted->employee_no, PDO::PARAM_INT);
                $testdata->bindParam(':category', $deducted->category, PDO::PARAM_STR);
                $testdata->bindParam(':inst_id', $deducted->inst_id, PDO::PARAM_INT);
                $testdata->bindParam(':pid', $deducted->pid, PDO::PARAM_INT);            
                $testdata->bindParam(':amount', $deducted->amount, PDO::PARAM_STR);            
                $testdata->bindParam(':reason', $deducted->reason, PDO::PARAM_STR);            
                $testdata->bindParam(':status', $deducted->status, PDO::PARAM_INT);            
                $testdata->bindParam(':salary_month', $deducted->salary_month, PDO::PARAM_INT);            
                $testdata->execute();
                if ($testdata->rowCount() > 0) :
                    $count++;
                endif;
            }
    }
    echo $count." deductions have been inserted in the Q2Deductions table of QFeed database."."\n";
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}