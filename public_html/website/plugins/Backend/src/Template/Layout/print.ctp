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
    </head>
    <body class="print <?= strtolower($this->request->params['controller'] . '-' . $this->request->params['action']); ?>" onload="window.print()">
        <?= $this->fetch('content') ?>
    </body>
</html>
