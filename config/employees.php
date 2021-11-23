<?php

require_once 'database.php';
$employees = [];
$count = 0;

require_once 'employees/employees.anglo.php';
require_once 'employees/employees.cityparks.php';
require_once 'employees/employees.citypower.php';
require_once 'employees/employees.eskom.php';
require_once 'employees/employees.goldfields.php';
require_once 'employees/employees.gov.php';
require_once 'employees/employees.hillside.php';
require_once 'employees/employees.qlink.php';
require_once 'employees/employees.sappi.php';
require_once 'employees/employees.toyota.php';
require_once 'employees/employees.transnet.php';
require_once 'employees/employees.trgmail.php';
require_once 'employees/employees.troutlook.php';
require_once 'employees/employees.whiskeycreek.php';

try 
{
    $conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    foreach ($employees as $employee) {
        $sql = "INSERT INTO Q_FeedDB.Q2Employee (employee_no, pid, salary, net_salary)
                    VALUES (:employee_no, :pid, :salary, :net_salary)";
        $testdata = $conn->prepare($sql);
        $testdata->bindParam(':employee_no', $employee['employee_no'], PDO::PARAM_INT);
        $testdata->bindParam(':pid', $employee['pid'], PDO::PARAM_INT);            
        $testdata->bindParam(':salary', $employee['salary'], PDO::PARAM_STR);
        $testdata->bindParam(':net_salary', $employee['net_salary'], PDO::PARAM_STR);
        $testdata->execute();
        if ($testdata->rowCount() > 0) :
            $count++;
        endif;
    }
    if ($count == 140) {
        echo '140 Employees inserted into the Q2Employee table.';
    }
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}