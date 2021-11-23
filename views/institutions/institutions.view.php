<?php

class institutionsView {

  public function loadDefaultView($data) {
    $inst = $_SESSION['institution_abbr'] == 'none' ? $_SESSION['institution'] : $_SESSION['institution_abbr'];
    $_SESSION['institution_name'] = $inst;
    ?>
    <!doctype html>
    <html>
      <head>
        <title>QFEED | <?php echo strtoupper($inst); ?></title>
        <link type="text/css" rel="stylesheet" href="/css/fonts.css">
        <link type="text/css" rel="stylesheet" href="/css/qfeed_home.css">
        <script src="/js/jquery.js" crossorigin="anonymous"></script>
      </head>
      <body>
        <div id="wrapper">
          <div id="qhome_header">
            <div class="logo">QFeed</div>
            <div id="qprofile"><?= strtoupper($inst) ?></div>
            <ul id="admin_menu" class="blu_bg">
              <li class="institution_menu ">
                <button class="white activeInstMenu_" data-target="institution" data-path="/institution/deductions/history/render" onclick="loadContent(this, event)">Deductions History</button>
              </li>
              <li class="institution_menu">
                <button class="white" data-target="institution" data-path="/institution/manage/render" onclick="loadContent(this, event)">Manage Deductions</button>
              </li>
              <li class="search"><input type="search" name="search" id="search" class="txt_ac" placeholder="SEARCH DEDUCTIONS HISTORY"></li>
            </ul>
            <div id="qlogout"><a href="/access/logout">Logout</a></div>
          </div>
          <div id="admin_content">
            <div id="institution" class="admin_content">
                <?= $this->renderDeductions($data) ?>
            </div>
          </div>
          <script src="/js/institutions.js" type="text/javascript"></script>
      </body>
    </html>
    <?php
  }

  public function renderDeductions($data) {
    ?>
    <div class="adminData">
      <div class="adminSubMenu">
        <button data-display="adminData_" id="2017" data-path="/institution/deductions/history/2017" class="activeAccBtn_" onclick="getDeductions(this, event)">2017</button>
        <button data-display="adminData_" id="2016" data-path="/institution/deductions/history/2016" onclick="getDeductions(this, event)">2016</button>
        <button data-display="adminData_" id="2015" data-path="/institution/deductions/history/2015" onclick="getDeductions(this, event)">2015</button>
        <button data-display="adminData_" id="2014" data-path="/institution/deductions/history/2014" onclick="getDeductions(this, event)">2014</button>
        <button data-display="adminData_" id="2013" data-path="/institution/deductions/history/2013" onclick="getDeductions(this, event)">2013</button>
      </div>
      <div class="adminData_">
        <div id="data" class="_instData _pd5">
            <?= $this->renderDeductionsPerYear($data) ?>
        </div>
        <div id="qhome_footer">
          &copy; 2017 QFeed. All rights reserved
        </div>
      </div>
    </div>
    <?php
  }

  public function renderDeductionsPerYear($employees) {
    ?>
    <h3><?= $_SESSION['year'] ?> DEDUCTIONS BY <?= strtoupper($_SESSION['institution_name']) ?></h3>
    <table class="_instDataTable">
      <tr>
        <th>NAME</th>
        <th>EMPLOYEE NUMBER</th>
        <th>EMPLOYER</th>
        <th>REASON</th>
        <th>AMOUNT</th>
        <th>SALARY MONTH</th>
      </tr>
      <?php
      foreach ($employees as $employee) {
        ?>
        <tr>
          <td><?= $employee->name ?></td>
          <td><?= $employee->employee_no ?></td>
          <td><?= $employee->payroll ?></td>
          <td><?= $employee->reason ?></td>
          <td class="amonth amount"><?= $employee->amount ?></td>
          <td><?= $employee->salary_month ?></td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  }

  public function renderDeductionsManagementInterface() {
    ?>
    <div class="adminData">
      <div class="adminSubMenu">
        <button data-display="adminData_" id="file" data-path="/institution/manage/render/file" class="activeAccBtn_" onclick="manageDeductions(this, event)">FILE</button>
        <button data-display="adminData_" id="online" data-path="/institution/manage/render/online" onclick="manageDeductions(this, event)">ONLINE</button>
      </div>
      <div class="adminData_">
        <div id="data" class="_instData _pd5">

        </div>
        <div id="qhome_footer">
          &copy; 2017 QFeed. All rights reserved
        </div>
      </div>
    </div>
    <?php
  }

  public function renderDeductionsFileManagementInterface() {
    ?>
    <div id="upload_wrapper">
      <h1>UPLOAD A DEDUCTIONS FILE WITH UPDATED OR NEW DATA</h1>
      <div>
        SELECT A FILE: 
        <input type="file" id="deductionsInput" onchange="uploadDeductionsFile(this, event)">
      </div>
      <h3>WHAT TO KNOW BEFORE YOU UPLOAD</h3>
      <p>
        The file must be in csv format. The headings <b>action,salary_month,employee_number,employer,reason and amount</b> must be on top of the file. 
        Headings can be in any order, just make sure associated data fields are under the correct heading. Please note that you can use excel to generate a csv file, 
        all you have to do is save your spreadsheet as csv and the upload it.
      </p>
      About the headings
      <ul class="txt_aleft">
        <li>action - delete (Remove upcoming deduction) or update (If it already exist in the system) or add (if it is a new deduction)</li>
        <li>salary_month - If deduction is for may 2017 then the value is 201705. Format is YYYYMM (Year and month of deduction)</li>
        <li>employee_number - Employee number of the client</li>
        <li>employer - The company which the client works for. Select one from this list.</li>
        <ul>
          <li>Q LINK Holdings</li>
          <li>Government</li>
          <li>Toyota (Pty) Ltd</li>
          <li>City Parks</li>
          <li>City Power</li>
          <li>TN Anglo</li>
          <li>Hillside Aluminium (Pty) Ltd</li>
          <li>Eskom</li>
          <li>Transnet</li>
          <li>Gold Fields</li>
          <li>Whiskey Creek</li>
          <li>SAPPI SA</li>
          <li>Testrun Gmail (Pty) Ltd</li>
          <li>Testrun Outlook (Pty) Ltd</li>
        </ul>
      </ul>
    </div>
    <div id="uploaded"></div>
    <?php
  }

}
