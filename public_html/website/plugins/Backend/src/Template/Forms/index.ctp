<div class="<?= strtolower($this->name); ?> list">
    <?php if(count($forms) < 1){ ?>
    <div class="message info"><?= __d('be', 'No data available') ?></div>
    <?php }else{ ?>
    <table class="list" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= __d('be', 'Sender') ?></th>
                <th width="180"><?= __d('be', 'Type') ?></th>
                <th width="100"><?= __d('be', 'Structure') ?></th>
                <th width="100"><?= __d('be', 'Language') ?></th>
                <th width="120"><?= __d('be', 'Date/Time') ?></th>
                <th width="<?= action_width(3, false); ?>" class="actions">&nbsp;</th>
            </tr>
        </thead>
        <?= $this->element('Backend.paginator', ['colspan' => 6]); ?>
        <tbody>
            <?php foreach($forms as $nr => $form){ ?>
            <tr data-id="<?= $form['id']; ?>" class="element<?= $nr%2 ? ' alternate' : ''; ?>">
                <td><?= $form['infos']['sender']; ?></td>
                <td class="centered"><?= $form['infos']['type']; ?></td>
                <td class="centered"><?= $form['infos']['structure']; ?></td>
                <td class="flags"><span class="hide-text flag <?= $form['infos']['locale']; ?>"><div class="hide-text"><?= $form['infos']['locale']; ?></div></span></td>
                <td class="centered"><?= date("d.m.Y H:i:s", $form['infos']['sent']); ?></td>
                <td class="actions">
                    <?= $this->element('Backend.icon', ['icon' => 'eye', 'cls' => 'details', 'text' => __d('be', 'Details'), 'url' => ['action' => 'details', $type, $form['infos']['id']]]) ?>
                    <?= $this->element('Backend.icon', ['icon' => 'print', 'cls' => 'print', 'text' => __d('be', 'Print'), 'url' => ['action' => 'details', $type, $form['infos']['id'], 'print'], 'target' => '_blank']) ?>
                    <?= $this->element('Backend.icon', ['icon' => 'trash', 'text' => __d('be', 'Delete'), 'url' => ['action' => 'delete', $type, $form['infos']['id']], 'confirm' => __d('be', 'Do you really want to delete this entry?')]) ?>
                    <div class="clear"></div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>
<script>

    var selected = 0;

    function formdetails(e){
        $('<div class="' + __system['popup']['class'] + '">').bPopup({
            opacity: __system['popup']['opacity'],
            modalClose: __system['popup']['modalClose'],
            content: 'ajax',
            closeClass: 'actions .cancel',
            loadUrl: $(e).attr('href'),
            onClose: function(){
                destroyPopup();
            }
        });
    }

    $(document).ready(function(){
        
        // categories
        $('select#types').change(function(){
            window.location.href = '<?= $this->Url->build(['action' => 'index']) . DS; ?>' + $(this).val();
        });
        
        $('a.icon.details').click(function(event){
            event.preventDefault();
            formdetails($(this));
        });
        
        $('a.icon.print').click(function(event){
            event.preventDefault();
            openInHiddenIFrame($(this).attr('href'));
        });

    });
    
</script>