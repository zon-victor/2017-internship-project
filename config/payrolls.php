<?php

//THIS SCRIPT MUST BE EXECUTED FROM THE TERMINAL
//THE PURPOSE OF THIS SCRIPT IS TO INSERT TESTING DATA IN THE PAYROLL TABLE
require_once 'database.php';
$payroll = array();

//South African Government
$payroll[0] = array(
    'pid' => '2017000',
    'payroll' => 'persal',
    'payday' => '25',
    'domain' => 'publicservice.gov.za',
    'sector' => 'government' 
);

//Q Link
$payroll[1] = array(
    'pid' => '2017001',
    'payroll' => 'Q Link Holdings',
    'payday' => '25',
    'domain' => 'qlink.co.za',
    'sector' => 'non-government'
);

//Toyota
$payroll[2] = array(
    'pid' => '2017002',
    'payroll' => 'Toyota (Pty) Ltd',
    'payday' => '15',
    'domain' => 'toyota.co.za',
    'sector' => 'non-government'
);

//City Parks
$payroll[3] = array(
    'pid' => '2017003',
    'payroll' => 'City Parks',
    'payday' => '22',
    'domain' => 'jhbcityparks.com',
    'sector' => 'government'
);

//City Power
$payroll[4] = array(
    'pid' => '2017004',
    'payroll' => 'City Power',
    'payday' => '25',
    'domain' => 'citypower.co.za',
    'sector' => 'non-government'
);

//TN Anglo
$payroll[5] = array(
    'pid' => '2017005',
    'payroll' => 'TN Anglo',
    'payday' => '25',
    'domain' => 'angloamerican.com',
    'sector' => 'non-government'
);

//Hillside Aluminium
$payroll[6] = array(
    'pid' => '2017006',
    'payroll' => 'Hillside Aluminium (Pty) Ltd',
    'payday' => 'ME',
    'domain' => 'hillside.co.za',
    'sector' => 'non-government'
);

//ESKOM
$payroll[7] = array(
    'pid' => '2017007',
    'payroll' => 'Eskom',
    'payday' => '25',
    'domain' => 'eskom.co.za',
    'sector' => 'non-government'
);

//Transnet
$payroll[8] = array(
    'pid' => '2017008',
    'payroll' => 'Transnet',
    'payday' => 'ME',
    'domain' => 'transnet.net',
    'sector' => 'non-government'
);

//Gold Fields
$payroll[9] = array(
    'pid' => '2017009',
    'payroll' => 'Gold Fields',
    'payday' => '15',
    'domain' => 'goldfields.com',
    'sector' => 'non-government'
);

//Whiskey Creek
$payroll[10] = array(
    'pid' => '2017010',
    'payroll' => 'Whiskey Creek',
    'payday' => 'ME',
    'domain' => 'whiskeycreek.co.za',
    'sector' => 'non-government'
);

//Sappi SA
$payroll[11] = array(
    'pid' => '2017011',
    'payroll' => 'SAPPI SA',
    'payday' => '25',
    'domain' => 'sappi.com',
    'sector' => 'non-government'
);

//Testrun Gmail (Pty) Ltd
$payroll[12] = array(
    'pid' => '2017012',
    'payroll' => 'Testrun Gmail (Pty) Ltd',
    'payday' => 'ME',
    'domain' => 'gmail.com',
    'sector' => 'non-government'
);

//Testrun Outlook (Pty) Ltd
$payroll[13] = array(
    'pid' => '2017013',
    'payroll' => 'Testrun Outlook (Pty) Ltd',
    'payday' => 'ME',
    'domain' => 'outlook.com',
    'sector' => 'non-government'
);

try 
{
    $conn = new PDO("mysql:host=localhost", $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    foreach ($payroll as $employer) {
        $sql = "INSERT INTO Q_FeedDB.Q2Payroll (pid, payroll, payday, domain, sector)
                    VALUES (:pid, :payroll, :payday, :domain, :sector)";
        $testdata = $conn->prepare($sql);
        $testdata->bindParam(':pid', $employer['pid'], PDO::PARAM_INT);
        $testdata->bindParam(':payroll', $employer['payroll'], PDO::PARAM_STR);
        $testdata->bindParam(':payday', $employer['payday'], PDO::PARAM_STR);            
        $testdata->bindParam(':domain', $employer['domain'], PDO::PARAM_STR);
        $testdata->bindParam(':sector', $employer['sector'], PDO::PARAM_STR);
        $testdata->execute();
        if ($testdata->rowCount() > 0) :
            echo $employer['payroll'].' successfully added  to Q2Payroll table in Q_FeedDB database'."\n";
        else:
            echo 'Failed to add '.$employer['payroll'].' to Q2Payroll table in Q_FeedDB database'."\n";
        endif;
    }
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}
