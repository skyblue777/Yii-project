<?php
//include_once(dirname(__FILE__).'/../../utilities/Utility.php');
//check permit
$assetsPermit = false;
$uploadPermit = false;
$runtimePermit = false;
$cachedPermit = false;
$configPermit = false;

$basePath = dirname(__FILE__).'/../../';

//clearstatcache();
if (is_writable($basePath.'/../assets') === true) {
    $assetsPermit = true;
}
if (is_writable($basePath.'/../uploads') === true) {
    $uploadPermit = true;
}
if (is_writable($basePath.'/config') === true) {
    $configPermit = true;
}

if (is_writable($basePath.'/runtime') === true) {
    $runtimePermit = true;
}

if (is_writable($basePath.'/runtime/cache') === true) {
    $cachedPermit = true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Classifieds installation</title>
<link href="themes/install/css/reset.css" media="screen" rel="stylesheet" type="text/css" />
<link href="themes/install/css/install.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="install">
<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Welcome to Titan Classified Installation Wizard</h2>
    <p>You need the following details before proceeding.</p>
    <ol>
       <li>Database host</li>
       <li>Database name</li>
       <li>Database username and password</li>
    </ol>
    <div class="note">
        <h4>Note: Following folders must be writable:</h4>
        <ul>
            <li>/assets - <?php echo !$assetsPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/uploads - <?php echo !$uploadPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/protected/runtime - <?php echo !$runtimePermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/protected/runtime/cache - <?php echo !$cachedPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/protected/config - <?php echo !$configPermit ? 'NOT OK. This is not required but you will have to create file environment.php manually in the next step.' : '<span style="color:green">OK</span>';?></li>
        </ul>
    </div>
<?php

    if ($assetsPermit && $uploadPermit && $runtimePermit && $cachedPermit)
        echo '<div class="output">',
             '<a href="index.php?r=install/default/environment" class="btn">Next</a>',
             '</div>';
    else
        echo '<div class="note">You have to make sure all folders listed above existed and writable before start.</div>';
?>
</fieldset>
</div>
</body>
</html>
