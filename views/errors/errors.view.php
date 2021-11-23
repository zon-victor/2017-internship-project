<?php

class errorsView {

  public function __construct($error) {
    $this->errorPage($error);
  }

  private function errorPage($error) {
    ?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="UTF-8">
        <title>Lost</title>
        <style>
          body {
              margin: 0 auto;
              color: #5d8aa8;
          }
          #error {
              width: 100%;
              height: 160px;
              position: relative;
              float: left;
              text-align: center;
              font-size: 38px;
              font-weight: bold;
              line-height: 160px;
              /*border-bottom: 3px solid #333;*/
          }
          #time {
              margin: 30px auto;
              position: relative;
              clear: both;
              width: auto;
              /*border-top: 3px solid #333;*/
              text-align: center;
          }
          #notice {
              margin: 30px auto;
              position: relative;
              clear: both;
              width: auto;
              height: 64px;
              text-align: center;
              font-size: 72px; 
              font-weight: bold;
          }
          #redirect {
              margin: 30px auto;
              position: relative;
              clear: both;
              width: 320px;
              height: 320px;
              border-radius: 320px;
              border: 5px dotted #333;
              text-align: center;
              font-size: 72px;
              font-weight: bold;
              line-height: 320px;
          }
        </style>
      </head>
      <body>
        <div id="error">QFEED ERROR: RESOURCE (<?= $error['error'] ?>) UNAVAILABLE</div>
        <div id="notice">REDIRECTING IN</div>
        <div id="redirect"></div>
        <div id="time">RESOURCE REQUESTED ON <?= strtoupper($error['time']) ?></div>
        <script type="text/javascript">

          var secondsRemaining;
          var intervalHandle;
          var minutes = 0.1

          secondsRemaining = minutes * 60;
          intervalHandle = setInterval(tick, 1000);

          function tick()
          {
              var timeDisplay = document.getElementById("redirect");

              // turn the seconds into mm:ss
              var min = Math.floor(secondsRemaining / 60);
              var sec = secondsRemaining - (min * 60);

              //add a leading zero (as a string value) if seconds less than 10
              if (sec < 10) {
                  sec = "0" + sec;
              }

              var message = sec;

              timeDisplay.innerHTML = message;

              if (secondsRemaining === 0) {
                  clearInterval(intervalHandle);
                  window.location = '/';
              }

              secondsRemaining--;
          }
        </script>
      </body>
    </html>
    <?php
  }
}
