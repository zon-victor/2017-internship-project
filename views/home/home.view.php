<?php

class homeView {

  public function home() {
    ?>
    <!doctype html>
    <html>
      <head>
        <title>QFEED | HOME</title>
        <link type = "text/css" rel = "stylesheet" href = "/css/fonts.css">
        <link type = "text/css" rel = "stylesheet" href = "/css/qfeed_home.css">
        <script src = "/js/jquery.js" crossorigin = "anonymous"></script>
      </head>
      <body>
        <div id="wrapper">
          <div id="qhome_header">
            <div id="qlogo"><a href="/home">QFeed</a></div>
            <div id="qprofile"><a href="/home/profile">Profile</a></div>
            <div id="qlogout"><a href="/access/logout">Logout</a></div>
          </div>
          <div id="qhome_content">
            <div id="qhome_content_left">
                <?php require_once 'partials/home.left.php'; ?>
            </div>
            <div id="qhome_content_center">
              <div id="savingsMenuBar">
                <button id="planOne" class="savingsMenuOption" onclick="planCalculatorOne(this, event)">METHOD ONE</button>
                <button id="planTwo" class="savingsMenuOption" onclick="planCalculatorTwo(this, event)">METHOD TWO</button>
                <button id="uninitializedPlan" class="savingsMenuOption" data-status="uninitialized" onclick="loadUnitializedGoals(this, event)">UNINITIALIZED</button>
                <button id="initializedPlan" class="savingsMenuOption" data-status="initialized" onclick="loadItializedGoals(this, event)">INITIALIZED</button>
                <button id="reachedPlan" class="savingsMenuOption" data-status="reached" onclick="loadReachedGoals(this, event)">REACHED</button>
              </div>
              <div id="affordabilityMenuBar">
                <button id="newTest" class="affordabilityMenuOption" onclick="renderAffordability(this, event)">NEW AFFORDABILITY TEST</button>
                <button id="adjustTest" class="affordabilityMenuOption" onclick="adjustAffordability(this, event)">ADJUST EXISTING AFFORDABILTY TEST</button>
              </div>
              <?= $this->deductions() ?>
            </div>
            <div id="qhome_content_right">
                <?php require_once 'partials/home.right.php'; ?>
            </div>
          </div>
          <div id="qhome_footer">
            <span class="plink"><a href="#">About</a></span>
            <span class="plink"><a href="/access/help">Help</a></span>
            <span class="plink">&copy; 2017 QFeed. All rigths reserved.</span>
            <span class="plink"><a href="#">Terms</a></span>
            <span class="plink"><a href="/access/feedback">Feedback</a></span>
          </div>
        </div>
        <script src="/js/qfeed.js" type="text/javascript"></script>
      </body>
    </html>
    <?php
  }

  public function deductions() {
    $this->monthsJanToJun();
    echo '<div id="data">';
    echo $this->bigMonths();
    echo '</div>';
    $this->monthsJulToDec();
  }

  public function monthsJanToJun() {
    $buttons = '';
    echo '<div id="months_left">';
    $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];
    foreach ($months as $month) {
      $buttons .= '<button class="month" id="' . $month . '">' . strtoupper($month) . '</button>';
    }
    echo $buttons;
    echo '</div>';
  }

  public function monthsJulToDec() {
    $buttons = '';
    echo '<div id="months_right">';
    $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    foreach ($months as $month) {
      $buttons .= '<button class="month" id="' . $month . '">' . strtoupper($month) . '</button>';
    }
    echo $buttons;
    echo '</div>';
  }

  public function reload_years() {
    ?>
    <div id="years">
      <button id="2017" class="year" onclick="onYearClick(this, event)">2017</button>
      <button id="2016" class="year" onclick="onYearClick(this, event)">2016</button>
      <button id="2015" class="year" onclick="onYearClick(this, event)">2015</button>
      <button id="2014" class="year" onclick="onYearClick(this, event)">2014</button>
      <button id="2013" class="year" onclick="onYearClick(this, event)">2013</button>
    </div>
    <?php
  }

  public function bigMonths() {
    $buttons = '';
    $months = ['jan' => 'JANUARY', 'feb' => 'FEBRUARY', 'mar' => 'MARCH', 'apr' => 'APRIL', 'may' => 'MAY', 'jun' => 'JUNE', 'jul' => 'JULY', 'aug' => 'AUGUST', 'sep' => 'SEPTEMBER', 'oct' => 'OCTOBER', 'nov' => 'NOVEMBER', 'dec' => 'DECEMBER'];
    foreach ($months as $id => $month) {
      $buttons .= '<button class="bmonth" id="' . $id . '">' . $month . '</button>';
    }
    return $buttons;
  }

//ADMINISTRATION PANEL
  public function adminPanel($users, $errors, $exceptions, $feedbacks, $queries) {
    ?>
    <!doctype html>
    <html>
      <head>
        <title>QFEED | ADMINISTRATION</title>
        <link type="text/css" rel="stylesheet" href="/css/fonts.css">
        <link type="text/css" rel="stylesheet" href="/css/qfeed_home.css">
        <script src="/js/jquery.js"></script>
      </head>
      <body>
        <div id="wrapper">
          <div id="qhome_header">
            <div class="logo">QFeed</div>
            <div id="qprofile">Admin</div>
            <ul id="admin_menu">
              <li class="admin_menu"><button class="activeMenu_" data-target="stats" data-path="/home/stats" onclick="loadContent(this, event)">Statistics</button></li>
              <li class="admin_menu"><button data-target="accounts" data-path="/home/accounts" onclick="loadContent(this, event)">Accounts</button></li>
              <li class="admin_menu"><button data-target="help" data-path="/home/help/render" onclick="loadContent(this, event)">Help</button></li>
              <li class="admin_menu"><button data-target="feedback" data-path="/home/feedback/render" onclick="loadContent(this, event)">Feedback</button></li>
              <li class="admin_menu"><button data-target="errors" data-path="/home/errors/render" onclick="loadContent(this, event)">Errors</button></li>
              <li class="admin_menu"><button data-target="exceptions" data-path="/home/exceptions/render" onclick="loadContent(this, event)">Exceptions</button></li>
            </ul>
            <div id="qlogout"><a href="/access/logout">Logout</a></div>
          </div>
          <div id="admin_content">
            <div id="stats" class="admin_content">
                <?= $this->renderStats($users, $errors, $exceptions, $feedbacks, $queries) ?>
            </div>
            <div id="accounts" class="admin_content"></div>
            <div id="help" class="admin_content"></div>
            <div id="feedback" class="admin_content"></div>
            <div id="errors" class="admin_content"></div>
            <div id="exceptions" class="admin_content"></div>
          </div>
        </div>
        <script src="/js/admin.js" type="text/javascript"></script>
      </body>
    </html>
    <?php
  }

  public function renderStats($users, $errors, $exceptions, $feedbacks, $queries) {
    ?>
    <div class="stats stats48">
      <div class="statsHeader">REGISTERED USERS</div>
      <ul class="statsData">
        <li class="statsData4">          
          <div class="statsCount">
              <?= $users['verified'] ?>
          </div>
          <div class="statsLabel">VERIFIED</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $users['unverified'] ?>
          </div>
          <div class="statsLabel">UNVERIFIED</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $users['enabled'] ?>
          </div>
          <div class="statsLabel">ENABLED</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $users['disabled'] ?>
          </div>
          <div class="statsLabel">DISABLED</div>
        </li>
      </ul>
    </div>
    <div class="stats stats48">
      <div class="statsHeader">ERRORS</div>
      <ul class="statsData">
        <li class="statsData4">          
          <div class="statsCount">
              <?= $errors['registration'] ?>
          </div>
          <div class="statsLabel">REGISTRATION</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $errors['login'] ?>
          </div>
          <div class="statsLabel">LOGIN</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $errors['verification'] ?>
          </div>
          <div class="statsLabel">VERIFICATION</div>
        </li>
        <li class="statsData4">          
          <div class="statsCount">
              <?= $errors['url'] ?>
          </div>
          <div class="statsLabel">INVALID URL</div>
        </li>
      </ul>
    </div>
    <div class="stats stats72">
      <div class="statsHeader">HELP QUERIES</div>
      <ul class="statsData">
        <li class="statsData6">
          <div class="statsCount"><?= $queries['new'] ?></div>
          <div class="statsLabel">NEW</div>
        </li>
        <li class="statsData6">          
          <div class="statsCount"><?= $queries['viewed'] ?></div>
          <div class="statsLabel">VIEWED</div>
        </li>
        <li class="statsData6">          
          <div class="statsCount"><?= $queries['answered'] ?></div>
          <div class="statsLabel">ANSWERED</div>
        </li>
        <li class="statsData6">          
          <div class="statsCount"><?= $queries['unanswered'] ?></div>
          <div class="statsLabel">UNANSWERED</div>
        </li>
        <li class="statsData6">          
          <div class="statsCount"><?= $queries['members'] ?></div>
          <div class="statsLabel"><?= $queries['members'] !== 1 ? 'MEMBERS' : 'MEMBER' ?></div>
        </li>
        <li class="statsData6">          
          <div class="statsCount"><?= $queries['visitors'] ?></div>
          <div class="statsLabel"><?= $queries['visitors'] !== 1 ? 'VISITORS' : 'VISITOR' ?></div>
        </li>
      </ul>
    </div>
    <div class="stats stats24">
      <div class="statsHeader">EXCEPTIONS</div>
      <ul class="statsData">
        <li class="statsData2">          
          <div class="statsCount">
              <?= $exceptions['solved'] ?>
          </div>
          <div class="statsLabel">SOLVED</div>
        </li>
        <li class="statsData2">          
          <div class="statsCount">
              <?= $exceptions['unsolved'] ?>
          </div>
          <div class="statsLabel">UNSOLVED</div>
        </li>
      </ul>
    </div>
    <div class="stats stats24">
      <div class="statsHeader">FEEDBACK</div>
      <ul class="statsData">
        <li class="statsData2">          
          <div class="statsCount"><?= $feedbacks['new'] ?></div>
          <div class="statsLabel">NEW</div>
        </li>
        <li class="statsData2">          
          <div class="statsCount"><?= $feedbacks['viewed'] ?></div>
          <div class="statsLabel">VIEWED</div>
        </li>
      </ul>
    </div>
    <?php
  }

  public function renderAccounts($users) {
    ?>
    <div class="adminData">
      <div class="adminSubMenu">
        <button data-display="adminData_" data-path="/home/accounts/users" class="activeAccBtn_" onclick="getAccounts(this, event)">Users</button>
        <button data-display="adminData_" data-path="/home/accounts/institutions" onclick="getAccounts(this, event)">Institutions</button>
      </div>
      <div class="adminData_">
          <?= $this->renderUserAccounts($users) ?>
      </div>
    </div>
    <?php
  }

  public function renderUserAccounts($users) {
    ?>
    <table class="adminDataTable">
      <tr>
        <th>NAME</th>
        <th>EMAIL</th>
        <th>REGISTRATION DATE</th>
        <th>EMPLOYEE NUMBER</th>
        <th>EMPLOYER</th>
        <th>SECTOR</th>
        <th>ACCOUNT STATUS</th>
        <th>OPTIONS</th>
      </tr>
      <?php
      foreach ($users as $user) {
        if ($user->verified == '1') {
          $verified = 'Verified';
        } else {
          $verified = 'Unverified';
        }
        if ($user->access == 'yes') {
          $access = 'Enabled';
          $button_txt = 'Disable Account';
          $new_access = 'no';
        } else {
          $access = 'Disabled';
          $button_txt = 'Enable Account';
          $new_access = 'yes';
        }
        ?>
        <tr>
          <td class="adminDataTable8cols"><?= $user->fullname ?></td>
          <td class="adminDataTable8cols"><?= $user->email ?></td>
          <td class="adminDataTable8cols"><?= $user->regdate ?></td>
          <td class="adminDataTable8cols"><?= $user->employee_no ?></td>
          <td class="adminDataTable8cols"><?= $user->employer ?></td>
          <td class="adminDataTable8cols"><?= $user->sector ?></td>
          <td class="adminDataTable8cols" id="<?= $user->acc_status_id ?>"><?= $verified ?> & <?= $access ?></td>
          <td class="adminDataTable8cols _pd5">
            <button class="task_button" data-status="<?= $user->acc_status_id ?>" data-access="<?= $new_access ?>" data-email="<?= $user->email ?>" onclick="toggleUserAccess(this, event)">
                <?= $button_txt ?>
            </button>
          </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  }

  public function renderInstitutionAccounts($institutions) {
    ?>
    <table class="adminDataTable">
      <tr>
        <th>NAME</th>
        <th>USERNAME</th>
        <th>INSTITUTION TYPE</th>
        <th>ACCOUNT STATUS</th>
        <th>OPTIONS</th>
      </tr>
      <?php
      foreach ($institutions as $institution) {
        if ($institution->access == 'yes') {
          $access = 'Enabled';
          $button_txt = 'Disable Account';
          $new_access = 'no';
        } else {
          $access = 'Disabled';
          $button_txt = 'Enable Account';
          $new_access = 'yes';
        }
        ?>
        <tr>
          <td class="adminDataTable5cols"><?= $institution->name ?></td>
          <td class="adminDataTable5cols"><?= $institution->username ?></td>
          <td class="adminDataTable5cols"><?= $institution->inst_type ?></td>
          <td class="adminDataTable5cols" id="<?= $institution->acc_status_id ?>"><?= $access ?></td>
          <td class="adminDataTable5cols">
            <button class="task_button" data-status="<?= $institution->acc_status_id ?>" data-access="<?= $new_access ?>" data-username="<?= $institution->username ?>" onclick="toggleInstitutionAccess(this, event)">
                <?= $button_txt ?>
            </button>
          </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  }

  public function renderErrors($errors) {
    ?>
    <table class="adminDataTable">
      <tr>
        <th>ERROR TYPE</th>
        <th>ERROR MESSAGE</th>
        <th>USERNAME/EMAIL</th>
        <th>NAME OF USER</th>
        <th>ACCOUNT TYPE</th>
        <th>TIME</th>
        <th>OPTIONS</th>
      </tr>
      <?php
      if (!$errors[0]->none) {
        foreach ($errors as $error) {
          ?>
          <tr id="<?= $error->id_errors ?>" class="rem_errs">
            <td class="adminDataTable8cols"><?= $error->error_type ?></td>
            <td class="adminDataTable8cols"><ul><?= $error->error_message ?></ul></td>
            <td class="adminDataTable8cols"><?= $error->username ?></td>
            <td class="adminDataTable8cols"><?= $error->name ?></td>
            <td class="adminDataTable8cols"><?= $error->account ?></td>
            <td class="adminDataTable8cols"><?= $error->datetime ?></td>
            <td class="adminDataTable8cols txt_ac">
              <button data-id="<?= $error->id_errors ?>" onclick="deleteError(this, event)" class="task_button">Delete Error</button>
            </td>
          </tr>
          <?php
        }
        echo '<tr><td colspan="7" class="no_errs"><button onclick="deleteErrors(this, event)" class="task_button">Delete All Errors</button></td></tr>';
      } else {
        echo "<tr><td colspan='7' class='no_errs'>" . $errors[0]->none . "</td></tr>";
      }
      ?>
    </table>
    <?php
  }

  public function renderExceptions($exceptions) {
    ?>
    <table class="adminDataTable">
      <tr>
        <th>ERROR MESSAGE</th>
        <th>LINE NUMBER</th>
        <th>FILENAME</th>
        <th>ACCOUNT TYPE</th>
        <th>TIME</th>
        <th>EXCEPTION STATUS</th>
        <th>CHANGE STATUS</th>
        <th>DELETE</th>
      </tr>
      <?php
      if (!$exceptions[0]->none) {
        foreach ($exceptions as $exception) {
          if ($exception->status == 'solved') {
            $btn_txt = 'Set As Unsolved';
            $change_to = 'unsolved';
          } elseif ($exception->status == 'unsolved') {
            $btn_txt = 'Set As Solved';
            $change_to = 'solved';
          }
          ?>
          <tr class="rem_errs">
            <td class="adminDataTable8cols"><?= $exception->message ?></td>
            <td class="adminDataTable8cols"><ul><?= $exception->line ?></ul></td>
            <td class="adminDataTable8cols"><?= $exception->filename ?></td>
            <td class="adminDataTable8cols"><?= $exception->account ?></td>
            <td class="adminDataTable8cols"><?= $exception->datetime ?></td>
            <td class="adminDataTable8cols <?= $exception->exc_status ?>"><?= $exception->status ?></td>
            <td class="adminDataTable8cols txt_ac">
              <button data-change="<?= $change_to ?>" data-line="<?= $exception->line ?>" data-file="<?= $exception->filename ?>" data-target="<?= $exception->exc_status ?>"  data-button="<?= $exception->exc_status_btn ?>" onclick="changeExceptionStatus(this, event)" class="task_button <?= $exception->exc_status_btn ?>"><?= $btn_txt ?></button>
            </td>
            <td class="adminDataTable8cols txt_ac">
              <button data-line="<?= $exception->line ?>" data-file="<?= $exception->filename ?>"  onclick="deleteException(this, event)" class="task_button">Delete Exception</button>
            </td>
          </tr>
          <?php
        }
        echo '<tr><td colspan="8" class="no_errs"><button onclick="deleteAllExceptions(this, event)" class="task_button">Delete All Exceptions</button></td></tr>';
      } else {
        echo "<tr><td colspan='8' class='no_errs'>" . $exceptions[0]->none . "</td></tr>";
      }
      ?>
    </table>
    <?php
  }

  public function renderHelp($users) {
    ?>
    <div class="adminData">
      <div class="adminSubMenu">
          <?php
          foreach ($users as $key => $data) {
            ?>
          <button class="no_overflow" data-email="<?= $key ?>" data-key="<?= $data[0]->guest_key ?>" onclick="showHelpMessages(this, event)"><?= $data[0]->name ?></button>
          <?php
        }
        ?>
      </div>
      <div class="adminData_">
        <div id="data" class="_pd5">PLEASE SELECT A USER ON THE LEFT-HAND SIDE. YOU WILL BE ABLE TO READ VIEW HELP QUERIES AND RESPOND TO THEM.</div>
      </div>
    </div>
    <?php
  }

  public function renderHelpMessages($messages) {
    ?>
    <h3>Messages between <span class="blu caps"><?= $messages[0]->name ?></span> and the <span class="blu caps">Administrator</span></h3>
    <table class="adminDataTable" id="q_and_a">
      <tr>
        <th>Name</th>
        <th>Time</th>
        <th>Message</th>
      </tr>
      <?php
      foreach ($messages as $message) {
        $name = $message->type == 'solution' ? "<span class='blu'>Administrator</span>" : $message->name;
        ?>
        <tr class="white_bg">
          <td class="_w25 _pd5"><?= $name ?></td>
          <td class="_w25"><?= $message->date ?></td>
          <td class="_50"><?= $message->message ?> </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <ul class="_pd5">
      <li><textarea name="solution" id="solution" class="query fs11 txt_ac _w100" placeholder="RESPOND TO QUERIES"></textarea></li>
      <li><input type="submit" data-name="<?= $messages[0]->name ?>" data-email="<?= $messages[0]->userid ?>" data-key="<?= $messages[0]->guest_key ?>" name="send_solution" class="send_info txt_ac" value="SUBMIT RESPONSE" onclick="sendHelpSolution(this, event)"></li>
    </ul>
    <?php
  }

  public function renderFeedback($messages) {
    ?>
    <h3>Feedback from users</h3>
    <div class="adminData">
      <div class="adminData_">
        <div id="data" class="_pd5">
          <ul>
              <?php
              foreach ($messages as $message) {
                ?>
              <li><?= $message->name ?> (<?= $message->date ?>): <?= $message->feedback ?></li>
              <?php
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
    <?php
  }

}
