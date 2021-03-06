<div class="midheading">
<h2><?php echo $title ?></h2>
</div>
<div class="<?php echo $data['model'] ?>-view">
    <?php foreach ($data['entry'][$data['model']] as $field => $content): ?>
    <?php if (in_array($field, $visible) && !('title' == $field)): ?>
        <div class="midtxt"><span class="midboldtxt">
	    <div class="<?php echo $field ?>"><?php echo $content ?></div>
        </span></div>
    <?php endif ?>
    <?php endforeach ?>
</div>
<?php echo $this->Theme->wrapper($data['model'].'-after', $this) ?>
<?php echo $this->Theme->wrapper('view-after', $this) ?>