Hi <?php echo $username;?>,<br/>
<br/>
You recently asked to reset your <?php echo Settings::SITE_NAME; ?> password. To complete your request, please follow this link:<br/>
<br/>
<?php echo $link;?><br/>
<br/>
Alternately, you may go to <?php echo $url;?> and enter the following confirmation code:<br/>
<br/>
<?php echo $code;?><br/>
<br/>
Please note: for your protection, a copy of this email has been sent to all the email addresses associated with your <?php echo Settings::SITE_NAME; ?> account.<br/>
<br/>
If you did not request a new password, you may disregard this message.<br/>
<br/>
Thanks,<br/>
<?php echo Settings::SITE_NAME; ?><br/>
<br/>