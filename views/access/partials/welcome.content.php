<div id="qfeed">QFeed</div>
<div id="main">
  <div id="wleft">
    <div id="winfo">
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
<?php
if (array_key_exists('access', $_SESSION) && $_SESSION['access'] != 'incomplete') 
{
  ?>
    <div id="wrform">
        <?php include_once 'welcome.register.php'; ?>
    </div>
  <?php
} 
elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'incomplete')
{
  ?>
    <p id="incomplete">
      Hi <?= $_SESSION['name'] ?>, welcome to Qfeed. We need an alternative email (GMAIL OR OUTLOOK OR QLINK)
      from you because your email domain is not yet supported by this QFeed beta version. This is just for sending
      a verification code. You will use your work email to login after verification.
    </p>
  <?php
} 
  ?>
  </div>
<?php
if (array_key_exists('access', $_SESSION) && $_SESSION['access'] !== 'incomplete')
{ 
  ?>
    <div id="wmid"><div id="wor"></div></div>
    <div id="wright">
      <div id="wlform">
          <?php
          include_once 'welcome.login.php';
          include_once 'welcome.reset.php';
          ?>
      </div>
    </div>
  <?php
}
elseif (array_key_exists('access', $_SESSION) && $_SESSION['access'] == 'incomplete')
{ 
  ?>
    <div id="wright">
      <div class="altmail">
        <form id="testMail" name="testMail" method="POST" action="/access/alternative">
        <input type="email" name="altmail" id="altmail" placeholder="Alternative Email">
        <input type="submit" name="othermail" id="othermail" onclick="sendToAlternativeEmail(event)" value="Other Email">
        </form>
      </div>
    </div>
    <div id="qlogout"><a href="access/logout">Later</a></div>
  <?php
}
?>
</div>
<div id="errors">
  <?php include_once 'welcome.errors.php'; ?>
</div>