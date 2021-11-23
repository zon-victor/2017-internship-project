<form id="register" method="POST" name="register" action="access/register">
  <div class="wfrmtitle">new users</div>
  <input type="text" name="name" class="input_common" placeholder="Full Name" />
  <select name="payroll" class="input_common input_select">
    <option id="2017014" selected="selected">Select Employer</option>
    <option id="2017000" value="2017000">SA Government</option>
    <option id="2017001" value="2017001">Q Link</option>
    <option id="2017002" value="2017002">Toyota</option>
    <option id="2017003" value="2017003">City Parks</option>
    <option id="2017004" value="2017004">City Power</option>
    <option id="2017005" value="2017005">TN Anglo</option>
    <option id="2017006" value="2017006">Hillside Aluminium</option>
    <option id="2017007" value="2017007">ESKOM</option>
    <option id="2017008" value="2017008">Transnet</option>
    <option id="2017009" value="2017009">Gold Fields</option>
    <option id="2017010" value="2017010">WhiskeyCreek</option>
    <option id="2017011" value="2017011">SAPPI SA</option>
    <option id="2017012" value="2017012">Testrun Gmail</option>
    <option id="2017013" value="2017013">Testrun Outlook</option>
  </select>
  <input type="text" name="employee_no" class="input_common" placeholder="Employee Number" />
  <!--<input type="number" name="net_salary" class="input_aright input_common" placeholder="Net Salary"/>-->
  <input type="email" name="email" class="input_common" placeholder="Work Email" />
  <input type="password" name="password" id="password" class="input_common" placeholder="New Password" />
  <input type="password" name="password2" class="input_common" placeholder="New Password Again" />
  <input type="submit" name="new" class="input_aleft sub_common submit" value="REGISTER"/>
</form>
<div id="verify"></div>
<div id="rules">
  <div class="prules_errors">PASSWORD RULES</div>
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
</div>
<div id="reg_errors">
  <div class='prules_errors'>REGISTRATION ERRORS</div>
  <ul id="access_errors" class="rules_list_errors"></ul>
</div>