<?php
$pageTitle = __('Forgot Password');
echo head(array('title' => $pageTitle, 'bodyclass' => 'login'), $header);
?>
<h1><?php echo $pageTitle; ?></h1>
<p id="login-links">
<span id="backtologin"><?php echo link_to('users', 'login', __('Back to Log In')); ?></span>
</p>

<p>You will need to contact your system administrator to reset your password.</p>

<?php echo foot(array(), $footer); ?>
