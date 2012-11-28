<?php head(array('bodyclass'=>'login'), 'login-header'); ?>
<h1>Forgot Password</h1>
<p id="login-links">
<span id="backtologin"><?php echo link_to('users', 'login', 'Back to Log In'); ?></span>
</p>

<p class="clear">If you are having difficulties loggin in, contact your system administrator. Password retreival unavailable.</p>

<?php foot(array(), 'login-footer'); ?>