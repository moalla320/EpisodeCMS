<div id="admin-language">
    <?php $current = Configure::read('Config.language') ?>
	<?php foreach(array('rus', 'eng') as $lang):?>
		<?php echo $html->link($lang, array('controller'=>'users', 'action'=>'language', $lang), array('class' => $current==$lang?'active':''))?>
	<?php endforeach;?>
</div>