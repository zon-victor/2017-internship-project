<?php

include_once '/QFeed/config/database.php';

function redirect($url) {
  echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
}
