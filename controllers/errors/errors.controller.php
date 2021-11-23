<?php

include_once '/QFeed/views/errors/errors.view.php';

class errorController {

  public function __construct() {
    $this->view = new errorsView($_GET);
  }

}

$error = new errorController();
