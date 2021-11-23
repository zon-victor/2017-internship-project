<?php
if (!array_key_exists('access', $_SESSION)) {
  $_SESSION['access'] = 'welcome';
}
?>
<!doctype html>
<html>
  <head>
    <title>QFEED | WELCOME</title>
    <link type="text/css" rel="stylesheet" href="css/fonts.css">
    <link type="text/css" rel="stylesheet" href="css/qfeed.css">
    <script src="js/jquery.js"></script>
  </head>
  <body>
    <div id="header"><?php require_once 'partials/welcome.header.php'; ?></div>
    <div id="content"><?php require_once 'partials/welcome.content.php'; ?></div>
    <div id="footer"><?php require_once 'partials/welcome.footer.php'; ?></div>
    <script src="js/access.js" type="text/javascript"></script>
  </body>
</html>