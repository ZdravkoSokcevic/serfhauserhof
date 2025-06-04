<?php

// init
$_options = isset($infos) && is_array($infos) ? $infos : [];

$_id = array_key_exists('id', $_options) ? $_options['id'] : '%id';
$_type = array_key_exists('type', $_options) ? $_options['type'] : '%type';
$_filename = array_key_exists('filename', $_options) ? $_options['filename'] : '%filename';
$_title = array_key_exists('title', $_options) ? $_options['title'] : '%title';
$_original = array_key_exists('original', $_options) ? $_options['original'] : '%original';
$_icon = array_key_exists('icon', $_options) ? $_options['icon'] : '%icon';
$_desc = array_key_exists('desc', $_options) ? $_options['desc'] : '%desc';

?>
<?php if(isset($type) && $type == 'image'){ ?><div class="item <?= $_type; ?>" data-id="<?= $_id; ?>" data-type="<?= $_type; ?>"><div class="img" style="background-image: url('/img/thumbs/<?= $_filename; ?>')"></div><div class="info"><span class="title"><?= $_title; ?><a class="fa fa-trash remove" href="javascript:void(0);" title="<?= __d('be', 'Remove item'); ?>"></a><div class="clear"></div></span><?= $_original; ?></div><div class="clear"></div></div><?php }else if(isset($type) && $type == 'node'){ ?><div class="item <?= $_type; ?>" data-id="<?= $_id; ?>" data-type="<?= $_type; ?>"><i class="fa fa-sitemap"></i><div class="info"><span class="title"><?= $_title; ?><a class="fa fa-trash remove" href="javascript:void(0);" title="<?= __d('be', 'Remove item'); ?>"></a><div class="clear"></div></span><?= $_desc; ?></div><div class="clear"></div></div><?php }else if(isset($type) && $type == 'category'){ ?><div class="item <?= $_type; ?>" data-id="<?= $_id; ?>" data-type="<?= $_type; ?>"><i class="fa fa-folder-o"></i><div class="info"><span class="title"><?= $_title; ?><a class="fa fa-trash remove" href="javascript:void(0);" title="<?= __d('be', 'Remove item'); ?>"></a><div class="clear"></div></span><?= $_desc; ?></div><div class="clear"></div></div><?php }else if(isset($type) && $type == 'element'){ ?><div class="item <?= $_type; ?>" data-id="<?= $_id; ?>" data-type="<?= $_type; ?>"><i class="fa fa-<?= $_icon; ?>"></i><div class="info"><span class="title"><?= $_title; ?><a class="fa fa-trash remove" href="javascript:void(0);" title="<?= __d('be', 'Remove item'); ?>"></a><div class="clear"></div></span><?= $_desc; ?></div><div class="clear"></div></div><?php } ?>