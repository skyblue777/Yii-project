<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Congratulation, you made it!</h2>
    <p>Titan Classified has been installed. <strong>It is important that you rename or remove /protected/modules/install folder to avoid other to run the installation again and delete your database.</strong>.</p>
    <div class="input">
        <label>E-mail</label><strong><?php if (Yii::app()->user->hasFlash('email')) echo Yii::app()->user->getFlash('email');?></strong>
    </div>
    <div class="input">
        <label>Password</label><strong><?php if (Yii::app()->user->hasFlash('password')) echo Yii::app()->user->getFlash('password');?></strong>
        <span class="note">Note that password carefully! It is a random password that was generated just for you.</span>
    </div>
    <div class="input">
		<ul>
        	<li><a target="_blank" href="<?php echo Yii::app()->Request->getBaseUrl(true).'/?r=Admin'; ?>">Go to Admin</a></li>
        	<li><a target="_blank" href="<?php echo Yii::app()->Request->getBaseUrl(true); ?>">Go to Site</a></li>
        </ul>
	</div>
</fieldset>