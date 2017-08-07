<style type="text/css">
.mesg { margin-top: 20px; }
</style>

<?php if ($deleteSuccess) : ?>
    <div class="mesg"><?php echo $msgs[0]; ?></div>
<?php else : ?>
    <div style="color: #f00;" class="mesg">
        <?php foreach ($msgs as $msg) : ?>
            <p><?php echo $msg; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>