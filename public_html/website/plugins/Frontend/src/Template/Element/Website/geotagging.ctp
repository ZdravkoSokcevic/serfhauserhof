<?php use Cake\Core\Configure; ?>
<meta name="DC.title" content="<?= Configure::read('config.default.hotel') ?>" />
<meta name="geo.region" content="<?= Configure::read('config.default.geo-region') ?>" />
<meta name="geo.placename" content="<?= Configure::read('config.default.city-'.$this->request->params['language']) ?>" />
<meta name="geo.position" content="<?= Configure::read('config.default.geo-latitude') ?>.<?= Configure::read('config.default.geo-longitude') ?>" />
<meta name="ICBM" content="<?= Configure::read('config.default.geo-latitude') ?>, <?= Configure::read('config.default.geo-longitude') ?>" />
