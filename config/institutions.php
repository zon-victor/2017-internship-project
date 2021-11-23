<?php

//THIS SCRIPT MUST BE EXECUTED FROM THE TERMINAL
//THE PURPOSE OF THIS SCRIPT IS TO INSERT TESTING DATA IN THE INSTITUTIONS TABLE
require_once 'database.php';
$institution = array();

//Avbob
$institution[0] = array(
    'inst_id' => '2017100',
    'institution' => 'Avbob',
    'institution_abbr' => 'none',
    'inst_type' => 'insurance',
    'username' => 'avbob' 
);

//Hollard
$institution[1] = array(
    'inst_id' => '2017101',
    'institution' => 'Hollard',
    'institution_abbr' => 'none',
    'inst_type' => 'insurance',
    'username' => 'hollard' 
);

//Old Mutual
$institution[2] = array(
    'inst_id' => '2017102',
    'institution' => 'Old Mutual',
    'institution_abbr' => 'none',
    'inst_type' => 'insurance',
    'username' => 'old-mutual' 
);

//Discovery Insure
$institution[3] = array(
    'inst_id' => '2017103',
    'institution' => 'Discovery Insure',
    'institution_abbr' => 'none',
    'inst_type' => 'insurance',
    'username' => 'discovery-insure' 
);

//Budget insurance
$institution[4] = array(
    'inst_id' => '2017104',
    'institution' => 'Budget Insurance',
    'institution_abbr' => 'none',
    'inst_type' => 'insurance',
    'username' => 'budget' 
);

//Bonitas
$institution[5] = array(
    'inst_id' => '2017105',
    'institution' => 'Bonitas',
    'institution_abbr' => 'none',
    'inst_type' => 'Medical Aid',
    'username' => 'bonitas' 
);

//Discovery Health
$institution[6] = array(
    'inst_id' => '2017106',
    'institution' => 'Discovery Health',
    'institution_abbr' => 'none',
    'inst_type' => 'Medical Aid',
    'username' => 'discovery-health' 
);

//Fedhealth
$institution[7] = array(
    'inst_id' => '2017107',
    'institution' => 'Fedhealth',
    'institution_abbr' => 'none',
    'inst_type' => 'Medical Aid',
    'username' => 'fedhealth' 
);

//Genesis Private
$institution[8] = array(
    'inst_id' => '2017108',
    'institution' => 'Genesis Private',
    'institution_abbr' => 'none',
    'inst_type' => 'Medical Aid',
    'username' => 'genesis'
);

//Momentum
$institution[9] = array(
    'inst_id' => '2017109',
    'institution' => 'Momentum',
    'institution_abbr' => 'none',
    'inst_type' => 'Medical Aid',
    'username' => 'momentum'
);

//Denosa
$institution[10] = array(
    'inst_id' => '2017110',
    'institution' => 'Democratic Nursing Organisation of SA',
    'institution_abbr' => 'Denosa',
    'inst_type' => 'Mas',
    'username' => 'denosa'
);

//Nupsaw
$institution[11] = array(
    'inst_id' => '2017111',
    'institution' => 'National Union of Public Service and Allied Workers',
    'institution_abbr' => 'Nupsaw',
    'inst_type' => 'Mas',
    'username' => 'nupsaw'
);

//South African Policing Union
$institution[12] = array(
    'inst_id' => '2017112',
    'institution' => 'South African Policing Union',
    'institution_abbr' => 'Sapu',
    'inst_type' => 'Mas',
    'username' => 'sapu'
);

//National Teachers Union
$institution[13] = array(
    'inst_id' => '2017113',
    'institution' => 'National Teachers Union',
    'institution_abbr' => 'Natu',
    'inst_type' => 'Mas',
    'username' => 'natu'
);

function scramblePassword($username, $pass) {
    $pass1 = hash('sha512', 'QFeed Institution password'.$username.$pass.'Q Link Internship Project 2017');
    $pass2 = hash('sha256', 'decrypt me'.$pass1.$username.$pass.'123456789101112223233444555');
    $pass3 = hash('sha512', 'blah blah'.$pass2.'xyz'.$pass1.'The password is >>>'.$pass.$username);
    return ($pass3);
}

try 
{
    $conn = new PDO("mysql:host=localhost", $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    foreach ($institution as $creditor) {
        $password = scramblePassword($creditor['username'], '123456');
        $sql = "INSERT INTO Q_FeedDB.Q2Institution (inst_id, institution, institution_abbr, inst_type, username, password)
                    VALUES (:inst_id, :institution, :institution_abbr, :inst_type, :username, :password)";
        $testdata = $conn->prepare($sql);
        $testdata->bindParam(':inst_id', $creditor['inst_id'], PDO::PARAM_INT);
        $testdata->bindParam(':institution', $creditor['institution'], PDO::PARAM_STR);
        $testdata->bindParam(':institution_abbr', $creditor['institution_abbr'], PDO::PARAM_STR);            
        $testdata->bindParam(':inst_type', $creditor['inst_type'], PDO::PARAM_STR);
        $testdata->bindParam(':username', $creditor['username'], PDO::PARAM_STR);
        $testdata->bindParam(':password', $password, PDO::PARAM_STR);
        $testdata->execute();
        if ($testdata->rowCount() > 0) :
            echo $creditor['institution'].' successfully added  to Q2Institution table in Q_FeedDB database'."\n";
        else:
            echo 'Failed to add '.$creditor['institution'].' to Q2Institution table in Q_FeedDB database'."\n";
        endif;
    }
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}
