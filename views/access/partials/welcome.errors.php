<?php

$mail = array_key_exists('altmail', $_SESSION) ? $_SESSION['altmail'] : $_SESSION['email'];
$error_title = 'FIX THE FOLLOWING ERROR';
if (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'verification error') {
  echo "<div class='error_title'>$error_title</div>";
  echo "<div class='errors'>Verification errror: Your account is already activated or the activation link is invalid</div>";
  session_destroy();
} elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'denied') {
  echo "<div class='error_title'>$error_title</div>";
  echo "<div class='errors'>Invalid login credentials: Check email, password and account type</div>";
  session_destroy();
} elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'unverified') {
  echo "<div class='error_title'>$error_title</div>";
  echo "<div class='errors'>Unverified account: Please verify your account by clicking on the link we sent to " .$mail. "</div>";
  session_destroy();
} elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'invalid email') {
  echo "<div class='error_title'>$error_title}</div>";
  echo "<div class='errors'> Invalid email format: Stop modifying your email address.</div>";
  session_destroy();
} elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'reset error') {
  echo "<div class='error_title'>$error_title</div>";
  echo "<div class='errors'> Invalid reset link: Stop modifying the link.</div>";
  session_destroy();
}
