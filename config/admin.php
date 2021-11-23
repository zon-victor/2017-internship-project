<?php

require_once 'database.php';

function scramblePassword($username, $pass) {
  $pass1 = hash('sha512', 'QFeed Institution password' . $username . $pass . 'Q Link Internship Project 2017');
  $pass2 = hash('sha256', 'decrypt me' . $pass1 . $username . $pass . '123456789101112223233444555');
  $pass3 = hash('sha512', 'blah blah' . $pass2 . 'xyz' . $pass1 . 'The password is >>>' . $pass . $username);
  return ($pass3);
}

try {
  $conn = new PDO("mysql:host=localhost", $DB_USER, $DB_PASSWORD);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $username = 'admin';
  $password = scramblePassword($username, '123456');
  $sql = "INSERT INTO Q_FeedDB.Q2Admin (username, password) VALUES (:username, :password)";
  $testdata = $conn->prepare($sql);
  $testdata->bindParam(':username', $username, PDO::PARAM_STR);
  $testdata->bindParam(':password', $password, PDO::PARAM_STR);
  $testdata->execute();
} catch (PDOException $e) {
  die("DB ERROR: " . $e->getMessage());
}


