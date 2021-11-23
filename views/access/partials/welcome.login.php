<form id="login" method="POST" name="login" action="access/login">
  <div class="wfrmtitle">members</div>
  <input type="text" name="handle" class="input_common" placeholder="Work Email or Username" value="<?= array_key_exists('email', $_SESSION) ? $_SESSION['email'] : '' ?>" required/>
  <input type="password" name="password" class="input_common" placeholder="Your Password" required/>
  <select name="account" id="account" class="input_common input_select">
    <option id="user" value="user" selected="selected">User</option>
    <option id="institution" value="institution">Institution</option>
    <option id="admin" value="admin">Administrator</option>
  </select>
  <input type="submit" name="member" id="member" class="submit input_aright" value="LOGIN"/>
  <a href="#" id="forgot_pass">Reset password</a>
</form>
