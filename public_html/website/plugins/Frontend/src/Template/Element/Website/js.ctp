<?php use Cake\Core\Configure; ?>
<script>

    var __translations = {
    	'childage': '<?= __d('fe', 'childage') ?>',
    };
    
    var __system = {
        'locale': '<?= $this->request->params['language']; ?>',
        'jump': <?php echo !array_key_exists('jump', $this->request->params['node']) || $this->request->params['node']['jump'] ? 'true' : 'false'; ?>
    };
    
    // tagmanager stuff
	var gaTrackingId = '<?= Configure::read('config.tracking.ga-tracking-id'); ?>';
    var docLang = '<?= $this->request->params['language']; ?>';
    var docRoute = '<?php echo array_key_exists('route', $this->request->params) ? $this->request->params['route'] : 'false'; ?>';
    var homeRoute = '<?= Configure::read('config.default.home.0.details.node.route') ?>';
    var docSeason = '<?= Configure::read('config.default.season'); ?>';
    var docStatusCode = <?= http_response_code(); ?>;
    <?php if(isset($tracking) && $tracking){ ?>var formSent = true;<?php } ?>

    
</script>
<?= $this->fetch('script') ?>
<?= $this->Html->script('Frontend.modernizr-custom.js') ?>
<?= $this->Html->script('Frontend.jquery-1.12.3.min.js') ?>
<?= $this->Html->script('Frontend.jquery.smooth-scroll.min.js') ?>
<?= $this->Html->script('Frontend.video.min.js') ?>
<?= $this->Html->script('Frontend.jquery.bxslider.min.js') ?>

<?= $this->Html->script('Frontend.pickadate/picker.js') ?>
<?= $this->Html->script('Frontend.pickadate/picker.date.js') ?>
<?= $this->Html->script('Frontend.pickadate/translations/' . $this->request->params['language'] . '.js') ?>

<?= $this->Html->script('Frontend.functions.js') ?>