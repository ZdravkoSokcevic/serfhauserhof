<?php use Cake\Core\Configure; ?>
<script>
    var __translations = {
        'ajax-error': '<?= __d('be', 'The AJAX request returned an unexpected error!') ?>',
        'upload': {
            'select': '<?= __d('be', 'No file selected!'); ?>',
        },
        'import': {
            'success': '<?= __d('be', 'Import completed!'); ?>',
            'failed': '<?= __d('be', 'Some translation fallbacks could not be found in the database'); ?>',
        },
        'selector': {
            'single': '<?= __d('be', 'Select item') ?>',
            'multiple': '<?= __d('be', 'Add item') ?>',
            'wait': '<?= __d('be', 'Wait till the preview is loaded!') ?>',
            'remove': '<?= __d('be', 'Remove item') ?>',
            'confirm': '<?= __d('be', 'Do you really want to remove this item?') ?>',
            'preview': '<?= __d('be', 'Show') ?>',
            'error': '<?= __d('be', 'Could not initialize selector') ?>',
            'custom': {
                'www': '<?= __d('be', 'External links have to look like this: %s', 'https://www.medienjaeger.at') ?>',
                'mail': '<?= __d('be', 'Email links have to look like this: %s', 'mailto:office@medienjaeger.at') ?>',
                'tel': '<?= __d('be', 'Telephone links have to look like this: %s', 'tel:004351220225') ?>'
            }
        },
        'times': {
            'button': '<?= __d('be', 'Add period') ?>',
            'headline': '<?= __d('be', 'Add period') ?>',
            'placeholder': {
                'from': '<?= __d('be', 'From') ?>',
                'to': '<?= __d('be', 'To') ?>'
            },
            'buttons': {
                'add': '<?= __d('be', 'Add') ?>',
                'cancel' : '<?= __d('be', 'Cancel') ?>'
            },
            'remove': '<?= __d('be', 'Remove period') ?>',
            'confirm': '<?= __d('be', 'Do you really want to remove this period?') ?>',
            'empty': '<?= __d('be', 'Please provide a start and end time!') ?>'
        },
        'editor': {
            'link': '<?= __d('be', 'Insert link') ?>',
            'image': '<?= __d('be', 'Insert image') ?>',
            'auto': '<?= __d('be', 'Detect internal links') ?>'
        }
    };

    var __system = {
        'locale': '<?= $this->request->params['language']; ?>',
        'translation': {
            'title': '<?= Configure::read('translations.' . Configure::read('translation') . '.title'); ?>',
            'short': '<?= Configure::read('translation'); ?>',
        },
        'editor': {
            'styles': <?= json_encode(Configure::read('styles')); ?>
        },
        'popup': {
            'class': 'popup-container',
            'opacity': 0.32,
            'modalClose': false,
        },
        'infos': <?= json_encode($system); ?>,
        'sortable': <?php echo isset($load_tablesorter) && $load_tablesorter == true ? 'true' : 'false'; ?>,
        'request': {
            'controller': '<?= $this->request->params['controller']; ?>',
            'action': '<?= $this->request->params['action']; ?>',
            'pass': {
                <?php foreach($this->request->params['pass'] as $k => $v){ echo $k . ': "' . $v . '",'."\n"; }; ?>
            },
        }

    };

</script>
<?= $this->Html->css('Backend.jquery-ui.min.css'); ?>
<?= $this->Html->css('Backend.jquery-ui.theme.min.css'); ?>
<?= $this->Html->script('Backend.jquery-2.2.0.min.js') ?>
<?= $this->Html->script('Backend.jquery-ui.min.js') ?>
<?= $this->Html->script('Backend.jquery.bpopup.min.js') ?>
<?php if(in_array($this->request->params['action'],['update']) || (isset($load_editor) && $load_editor == true)){ ?>
    <?= $this->Html->script('Backend.tinymce/tinymce.min.js') ?>
    <?= $this->Html->script('Backend.tinymce/jquery.tinymce.min.js') ?>
<?php } ?>
<?php if(in_array($this->request->params['action'],['update']) || (isset($load_datepicker) && $load_datepicker == true)){ ?>
    <?= $this->Html->css('Backend.pickadate/default.css') ?>
    <?= $this->Html->css('Backend.pickadate/default.date.css') ?>
    <?= $this->Html->script('Backend.pickadate/picker.js') ?>
    <?= $this->Html->script('Backend.pickadate/picker.date.js') ?>
    <?= $this->Html->script('Backend.pickadate/translations/' . $this->request->params['language'] . '.js') ?>
<?php } ?>
<?php if(isset($load_tablesorter) && $load_tablesorter == true){ ?>
    <?= $this->Html->script('Backend.jquery.rowsorter.js') ?>
<?php } ?>
<?= $this->fetch('script') ?>
<?= $this->Html->script('Backend.backend.js') ?>
