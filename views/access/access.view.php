<?php

class accessView {

  public function welcome() {
    include_once 'welcome.php';
  }

  public function verifyAccount($name, $email) {
    $message['registered'] = "HI " . strtoupper($name) . ", YOU HAVE SUCCESSFULLY REGISTERED, "
            . "HOWEVER, YOU MUST FIRST VERIFY YOUR ACCOUNT BY CLICKING ON THE LINK WE SENT TO YOUR "
            . "EMAIL (" . strtoupper($email) . ")";
    echo json_encode($message);
  }

  public function alternativeEmail($name) {
    $response['reroute'] = "Hi " . strtoupper($name) . ", welcome to Qfeed. "
            . "We need an alternative email (GMAIL OR OUTLOOK OR QLINK) from you because your email domain"
            . " is not yet supported by this QFeed Beta version.";
    $response['email'] = '<div class="altmail" name="alt_email" id="alt_email">'
            .'<form id="testMail" name="testMail" method="POST" action="/access/alternative">'
            .'<input type="email" name="altmail" id="altmail" placeholder="Alternative Email">'
            .'<input type="submit" name="othermail" id="othermail" onclick="sendToAlternativeEmail(event)" value="Other Email">'
            .'</form>'
             .'</div>';
    echo json_encode($response);
  }

  public function registrationFailed() {
    $response['failure'] = 'Failed to register your account, the administrator has be notified about this error. '
            . 'You can attempt again.';
    echo json_encode($response);
  }

  public function returnRegistrationErrors($errors) {
    foreach ($errors as $error) {
      if ($error !== 'errors'):
        $err['errors'] .= "<li>" . $error . "</li>";
      endif;
    }
    echo json_encode($err);
  }

  public function passwordResetPage($name) {
    if (array_key_exists('access', $_SESSION) && $_SESSION['access'] === 'reset') {
      ?>
      <!DOCTYPE html>
      <html>
        <head>
          <meta charset="UTF-8">
          <title><?= $name; ?> | New Password</title>
          <link type="text/css" rel="stylesheet" href="/css/fonts.css">
          <link type="text/css" rel="stylesheet" href="/css/reset.css">
        </head>
        <body>
          <div id="reset_wrapper">
            <div id="header">
              <div id="qfeed">QFeed</div>
            </div>
            <div id="site_info">
              <p>
                QFeed allows you to do a personal afford-ability test. 
                For example, to check if you may qualify for a new insurance policy.
              </p>
              <p>
                QFeed has an archive of salary deductions for up to five years.
                We might have yours. Register to find out.
              </p>
              <p>
                QFeed also offers budgeting features to help you reach your goals
                and make calculated financial decisions.
              </p>
            </div>
            <div id="reset_form">
              <div id="reset_form_title">HI <?= strtoupper($name); ?>, SET YOUR NEW PASSWORD</div>
              <form action="/access/reset" method="POST" name="reset_pass" id="reset_pass">
                <input type="password" name="password" id="password" placeholder="New Password" class="input_common">
                <input type="password" name="password2" id="password2" placeholder="Repeat New Password" class="input_common">
                <input type="submit" name="reset" id="reset" value="Submit Changes" onclick="resetPassword(event)" class="input_common">
              </form>
              <div id="password_rules">
                <div class="prules_errors">PLEASE APPLY THESE PASSWORD RULES</div>
                <ul class="rules_list_errors">
                  <li>Password length: between 8 and 15 characters long.</li>
                  <li>The password must contain one or more characters from each of the following groups:
                    <ul>
                      <li>Uppercase alpha characters group - [ABCDEFGHIJKLMNOPQRSTUVWXYZ]</li>
                      <li>Lowercase alpha characters group - [abcdefghijklmnopqrstuvwxyz]</li>
                      <li>Numeric characters group - [1234567890]</li>
                      <li>Special characters group - [{}[]~`_^|!#$*-=+><,.?/;:\]</li>
                    </ul>
                  </li>
                </ul>
              <p class='wide'>
            <?php
              if (array_key_exists('reset_errors', $_SESSION)) {
                ?>
                 <div class="prules_errors">PLEASE FIX THE FOLLOWING ERRORS</div>
                <ul><?= $_SESSION['reset_errors'] ?></ul>
                <?php
              }
             ?>
            </p>
              </div>
            </div>
            <div id="footer">
                <?php include_once 'partials/welcome.footer.php'; ?>
            </div>
          </div>
        </body>
      </html>
      <?php
    } else {
      $_SESSION['access'] = 'welcome';
    }
  }

  public function helpPage($messages) {
//    if (array_key_exists('access', $_SESSION) && $_SESSION['access'] === 'reset') {
    ?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="UTF-8">
        <title>QFEED | HELP</title>
        <link type="text/css" rel="stylesheet" href="/css/fonts.css">
        <link type="text/css" rel="stylesheet" href="/css/qfeed_home.css">
        <script src="/js/jquery.js"></script>
      </head>
      <body>
        <div id="wrapper">
          <div id="qhome_header">
            <div id="qlogo"><a href="/">QFeed</a></div>
            <div id="qprofile">Help</div>
            <?php
              if (isset($_SESSION['access']) && $_SESSION['access'] === 'granted') {
                echo '<div id="qlogout"><a href="/access/logout">Logout</a></div>';
              }
            ?>
          </div>
          <div class="just_content">
            <div id="data" class="_pd5">
              <h3>Registration Help For Employees</h3>
              <ul>
                <li>Employee Number - Issued to you by the company you work for.</li>
                <li>Work Email - Issued to you by the company you work for.</li>
                <li>Employer - The company you work for.</li>
                <li>Password - This is up to you. However, you must take into consideration our password rules.</li>
              </ul>
              <h3>Login Help For Registered Users</h3>
              <ul>
                <li>Account Verification - Instructions were sent to your email. If you have a problem, you can send a help query using the help form.</li>
                <li>Account must be set to user. This is the default setting unless you switched it.</li>
              </ul>
              <h3>Password Reset Help For Registered Users</h3>
              <ul>
                <li>If you failed to reset your password and you believe something is wrong with our systems please contact the administrator using the help form.</li>
              </ul>
              <h3>Usage Help</h3>
              <ul>
                <li>To view all deductions in a particular year you must select a category (all/medical/insurance/mas) and then a year.</li>
                <li>To view all deductions of a certain month you must select a category (all/medical/insurance/mas), a year and then a month.</li>
                <li>To calculate afforadability, select affordability, and fill in the required fields in the form.</li>
                <li>To calculate savings, select the savings tab and choose the savings calculator which suits your needs.</li>
              </ul>
              <h3>Queries And Responses</h3>
              <ul id="help_chat">
                  <?php
                  if (array_key_exists('help', $_SESSION) && $_SESSION['help'] === 'active') {
                    ?>
                    <?php
                    if (!$messages[0]->none) {
                      foreach ($messages as $message) {
                        $name = $message->type == 'solution' ? '<span class="blu">Administrator</span>' : $message->name;
                        echo "<li class='chat_txt'>$name ($message->date) :  $message->message</li>";
                      }
                    }
                    ?>
                </ul>
                <ul>
                  <li><input type="hidden" name="name" id="name" class="just_input txt_ac fs11" value="<?= $_SESSION['name'] ?>" required></li>
                  <li><input type="hidden" name="email" id="email" class="just_input txt_ac fs11" value="<?= $_SESSION['email'] ?>" required></li
                  <li><textarea name="query" id="query" class="query fs11 txt_ac" placeholder="FOLLOW-UP MESSAGE"></textarea></li>
                  <li><input type="submit" name="help" class="send_info txt_ac" value="SUBMIT MESSAGE" onclick="sendHelpQuery(event)"></li>
                </ul>
                <?php
              } else {
                ?>
                <ul id="queries">
                  <p>If this is not the first time you ask for help, please <button id="show_hlogin" class="task_button" data-show="help_login" data-hide="queries" onclick="showThis(this, event)">click here to login</button> using the key sent to your email. 
                    You will be able to view solutions from the administrator, and chat live if he/she is online.</p>
                  <li><input type="text" name="name" id="name" class="just_input txt_ac fs11" placeholder="WHAT IS YOUR NAME?" required></li>
                  <li><input type="email" name="email" id="email" class="just_input txt_ac fs11" placeholder="WHAT IS YOUR EMAIL?" required></li>
                  <li>
                    <textarea name="query" id="query" class="query fs11 txt_ac" placeholder="WHAT DO YOU NEED HELP WITH?"></textarea>
                  </li>
                  <li>
                    <input type="submit" name="help" class="send_info txt_ac" value="SEND QUERY" onclick="sendHelpQuery(event)">
                  </li>
                </ul>
                <ul id="help_login">
                  <p class="red">Help access key was sent to your email. Login with the key and the email you used to ask for help.
                    You will be able to chat live with the administrator. Also, this is the only way for you to view the response to your query.</p>
                  <li><input type="email" name="email" id="guestmail" class="just_input txt_ac fs11" placeholder="YOUR EMAIL" required></li>
                  <li><input type="password" name="password" id="key" class="just_input txt_ac fs11" placeholder="HELP ACCESS KEY" required></li>
                  <li>
                    <input type="submit" name="login_help" class="send_info txt_ac" value="LOGIN" onclick="loginForHelp(event)">
                  </li>
                  <p><button class="task_button" data-hide="help_login" data-show="queries" onclick="showThis(this, event)">click here</button> to go back to help form</p>
                </ul>
              <?php } ?>
            </div>
          </div>
          <div id="footer">
            <?php include_once 'partials/welcome.footer.php'; ?>
          </div>
        </div>
        <script src="/js/help.js"></script>
      </body>
    </html>
    <?php
//    } else {
//      $_SESSION['access'] = 'welcome';
//    }
  }

  public function feedBack() {
    ?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="UTF-8">
        <title>QFEED | FEEDBACK</title>
        <link type="text/css" rel="stylesheet" href="/css/qfeed_home.css">
        <script src="/js/jquery.js"></script>
      </head>
      <body>
        <div id="wrapper">
          <div id="qhome_header">
            <div id="qlogo"><a href="/">QFeed</a></div>
            <div id="qprofile">Feedback</div>
            <?php
            if (isset($_SESSION['access']) && $_SESSION['access'] === 'granted') {
              echo '<div id="qlogout"><a href="/access/logout">Logout</a></div>';
            }
            ?>
          </div>
          <div class="just_content">
              <?php
              if (isset($_SESSION['access']) && $_SESSION['access'] !== 'granted') {
                echo '<h3>ONLY REGISTERED USERS CAN OFFER FEEDBACK</h3>';
              } else {
                ?>
              <div id="data" class="_pd5">
                <h3>You are more than welcome to give us feedback about your QFeed usage experience</h3>
                <ul>
                  <li>
                    <textarea class="query fs11" id="myFeedback" placeholder="Anything in your mind about the services we offer?"></textarea>
                  </li>
                  <li><input type="submit" name="help" class="send_info txt_ac" value="SEND FEEDBACK" onclick="sendFeedback(event)"></li>
                </ul>
              </div>
    <?php } ?>
          </div>
          <div id="footer">
            <?php include_once 'partials/welcome.footer.php'; ?>
          </div>
          <script src="/js/qfeed.js"></script>
      </body>
    </html>
    <?php
  }

}
