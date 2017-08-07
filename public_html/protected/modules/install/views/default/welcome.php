<fieldset>
    <h1><a href="#">Titan Classified</a></h1>
    <h2>Welcome to Titan Classified Installation Wizard</h2>
    <p>You will need to know the following items before proceeding. In all likelihood, these items were supplied to you by your Web Host. If you do not have this information, then you will need to contact them before you can continue.</p>
    <ol>
       <li>Database host</li>
       <li>Database name</li>
       <li>Database username and password</li>
    </ol>
    <div class="note">
        <h4>Notes: Fowllowing folder must be writable (chmod 777):</h4>
        <ul>
            <li>/assets - <?php echo !$assetsPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/uploads - <?php echo !$uploadPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/protected/runtime - <?php echo !$runtimePermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
            <li>/protected/runtime/cache - <?php echo !$cachedPermit ? 'NOT OK' : '<span style="color:green">OK</span>';?></li>
        </ul>
    </div>
    <?php if ($assetsPermit && $uploadPermit && $runtimePermit) : ?>
    <div class="output">        
        <?php echo CHtml::link('Next', array('default/environment'), array('class'=>'btn'));?>
    </div>
    <?php else: ?>
    <div class="note">You have to make sure all folders listed above existed and writable before start.</div>
    <?php endif; ?>
</fieldset>