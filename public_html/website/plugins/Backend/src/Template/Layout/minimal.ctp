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
        <?= $this->element('Backend.meta') ?>
        <?= $this->fetch('meta') ?>
        <?= $this->Html->css('Backend.reset.css') ?>
        <?= $this->Html->css('Backend.font-awesome.min.css') ?>
        <?= $this->Html->css('Backend.backend.css') ?>
        <?= $this->fetch('css') ?>
        <?= $this->element('Backend.js') ?>
    </head>
    <body class="minimal">
        <div class="main">
            <section class="main-content">
                <?= $this->fetch('content') ?>
            </section>
            <div class="clear"></div>
        </div>
        <div id="datepicker-container"></div>
    </body>
</html>
