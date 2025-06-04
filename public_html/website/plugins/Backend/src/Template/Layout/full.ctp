<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($title) ? strip_tags($title) : $this->fetch('title') ?></title>
        <?php if(isset($refresh) && $refresh === true){ ?><meta http-equiv="refresh" content="1" /><?php } ?>
        <?= $this->element('Backend.meta') ?>
        <?= $this->fetch('meta') ?>
        <?= $this->Html->css('Backend.reset.css') ?>
        <?= $this->Html->css('Backend.font-awesome.min.css') ?>
        <?= $this->Html->css('Backend.backend.css') ?>
        <?= $this->fetch('css') ?>
        <?= $this->element('Backend.js') ?>
    </head>
    <body class="<?= strtolower($this->request->params['controller'] . '-' . $this->request->params['action']); ?>">
        <div class="loading"><?= __d('be', 'Active connection') ?> <i class="fa fa-cog fa-spin"></i></div>
        <?= $this->element('Backend.sidebar') ?>
        <div class="main<?php echo isset($menu) && ((array_key_exists('left', $menu) && count($menu['left']) > 0) || (array_key_exists('right', $menu) && count($menu['right']) > 0)) ? '' : ' no-menu-actions'; ?>">
            <?= $this->element('Backend.menu', ['left' => isset($menu) && array_key_exists('left', $menu) ? $menu['left'] : [], 'right' => isset($menu) && array_key_exists('right', $menu) ? $menu['right'] : []]); ?>
            <section class="main-content">
                <?= $this->element('Backend.caution', ['caution' => $caution]) ?>
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </section>
            <?= $this->element('Backend.footer'); ?>
            <div class="clear"></div>
        </div>
        <div id="datepicker-container"></div>
    </body>
</html>
