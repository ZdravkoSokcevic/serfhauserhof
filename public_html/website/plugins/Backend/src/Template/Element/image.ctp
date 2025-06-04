<div class="item image<?= $info[$image['id']]['used'] ? ' used' : ''; ?><?= $info[$image['id']]['recrop'] ? ' recrop' : ''; ?>" id="<?= $image['id']; ?>"<?php echo count($info[$image['id']]['purposes']) > 0 ? ' data-cropped-' . join('="true" data-cropped-', $info[$image['id']]['purposes']) . '="true"' : ""; ?>>
    <div class="title">
        <?php if($premissions['group']){ ?><input type="checkbox" name="image[]" id="image-<?= $image['id']; ?>" value="<?= $image['id']; ?>" /><?php } ?>
        <label title="<?= $image['title']; ?>" for="image-<?= $image['id']; ?>"><?= $this->Text->truncate($image['title'],25); ?></label>
        <div class="actions">
            <?php if($premissions['exchange']){ ?><a class="exchange" href="<?= $this->Url->build(['action' => 'exchange', $image['id']]); ?>" title="<?= __d('be','Replace image'); ?>"><i class="fa fa-exchange"></i></a>&nbsp;&nbsp;<?php } ?><?php if($premissions['crop']){ ?><a href="<?= $this->Url->build(['action' => 'crop', $image['id']]); ?>" title="<?= __d('be','Crop image'); ?>"><i class="fa fa-crop"></i></a>&nbsp;&nbsp;<?php } ?><?php if($premissions['revolve']){ ?><a href="#" class="rotate-icon" title="<?= __d('be','Rotate image'); ?>"><i class="fa fa-repeat"></i></a>&nbsp;&nbsp;<?php } ?><?php if($premissions['auto']){ ?><a class="auto" href="<?= $this->Url->build(['action' => 'auto', $image['id']]); ?>" title="<?= __d('be','Auto. crop image'); ?>"><i class="fa fa-magic"></i></a>&nbsp;&nbsp;<?php } ?><?php if($premissions['delete']){ ?><a href="<?= $this->Url->build(['action' => 'delete', $category['id'], $image['id']]); ?>" onclick="return confirm('<?= __d('be','Do you realy want to delete this image?'); ?>')" title="<?= __d('be','Delete image'); ?>"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;<?php } ?><a class="info" href="javascript:void(0);"><i class="fa fa-info-circle"></i></a>
        </div>
    </div>
    <span class="original" title="<?= $image['original']; ?>"><?= $this->Text->truncate($image['original'], 35); ?></span>
    <div class="preview" style="background-image: url('/img/thumbs/<?= $image['id']; ?>.<?= $image['extension']; ?>');"></div>
    <?php if($premissions['translate']){ ?>
    <div class="languages">
        <?php echo $this->element('Backend.flags', ['translations' => $image['_translations'], 'url' => ['action' => 'translate', $image['id']], 'clear' => true]); ?>
    </div>
    <div class="translations">
        <span><?= __d('be','Translations'); ?></span>
        <form method="POST" action="<?php echo $this->Url->build(['action' => 'translate', $image['id']]); ?>">
            <?php
            foreach($translations as $k => $v){
                if($v['active'] === true){
                    echo $this->Form->input('translation[' . $k . ']', ['class' => 'flag ' . $k, 'label' => false, 'data-value' => array_key_exists($k,$image['_translations']) ? $image['_translations'][$k]['title'] : '', 'value' => array_key_exists($k,$image['_translations']) ? $image['_translations'][$k]['title'] : '', 'placeholder' => __d('be', 'Translation')]);
                }
            }
            ?>
            <a class="submit button save" href="javascript:void(0);"><?= __d('be', 'Save'); ?></a>
            <a class="submit button cancel" href="javascript:void(0);"><?= __d('be', 'Cancel'); ?></a>
            <div class="clear"></div>
        </form>
    </div>
    <?php } ?>
    <?php if($premissions['revolve']){ ?>
    <div class="rotate">
        <span><?= __d('be','Rotate'); ?></span>
        <form method="POST" action="<?php echo $this->Url->build(['action' => 'revolve', $image['id']]); ?>">
            <div class="message"><?= strtoupper(__d('be', 'Note')); ?>: <?= __d('be', 'Images have to be cropped again after rotating!'); ?></div>
            <?php
                echo $this->Form->input('degrees', ['label' => false, 'options' => ['90' => '90° ' . __d('be', 'clockwise'), '180' => '180° ' . __d('be', 'clockwise'), '270' => '270° ' . __d('be', 'clockwise')]]);
            ?>
            <input type="submit" name="submit" class="submit button save" value="<?= __d('be', 'Rotate'); ?>" />
            <a class="submit button cancel" href="javascript:void(0);"><?= __d('be', 'Cancel'); ?></a>
            <div class="clear"></div>
        </form>
    </div>
    <?php } ?>
    <div class="information">
        <span><?= __d('be','Information'); ?></span>
        <ul>
        <?php
        foreach($purposes as $k => $v){
            
            // init
            $cls = '';
            $recrop = $cropped = false;
            if(array_key_exists($k, $info[$image['id']]['blanks'])){
                $cropped = $info[$image['id']]['blanks'][$k]['modified'];
                $cls = 'cropped';
                if($info[$image['id']]['blanks'][$k]['recrop']){
                    $cls = 'recrop';
                    $recrop = true;
                }
            }
            
            echo '<li rel="' . $k . '" class="' . $cls . '">';
            if($recrop){
                echo '<i class="fa fa-exclamation-triangle"></i>';
            }else if($cropped){
                echo '<i class="fa fa-check-circle"></i>';
            }else{
                echo '<i class="fa fa-times-circle"></i>';
            }
            echo '&nbsp;&nbsp;<span class="purpose">' . $v['name'] . '</span>';
            if($cropped){
                echo '<span class="time">' . date("Y-m-d H:i:s", $cropped) . '</span>';
            }
            echo '<div class="clear"></div>';
            echo '</li>';
        }
        ?>
        </ul>
        <div class="category">
            <?php if($info[$image['id']]['category']){ ?>
            <div>
                <span><?= __d('be', 'Category'); ?>:</span>
                <span class="value long"><?= $info[$image['id']]['category']; ?></span>
                <div class="clear"></div>
            </div>
            <?php } ?>
            <div>
                <span><?= __d('be', 'Upload'); ?>:</span>
                <span class="value"><?= date("Y-m-d H:i:s", $info[$image['id']]['upload']); ?></span>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>