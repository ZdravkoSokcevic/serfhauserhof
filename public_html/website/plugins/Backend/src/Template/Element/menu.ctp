<nav class="<?php echo count($left) == 0 && count($right) == 0 ? 'no-action' : ''; ?>">
    <div>
        <h1><?= isset($title) ? $title : '' ?></h1>
        <div>
            <?php foreach(['left' => $left, 'right' => $right] as $position => $elements){ ?>
                <?php if(count($elements) > 0){ ?>
                <div class="<?= $position ?>">
                    <?php foreach($elements as $element){
                        if(array_key_exists('type', $element) && (!array_key_exists('show', $element) || $element['show'] === true)){
                            switch ($element['type']) {
                                case 'select':
                                    if(array_key_exists('name', $element) && array_key_exists('attr', $element)){
                                        $attr = array_merge([
                                            'label' => false,
                                            'templates' => ['inputContainer' => '{{content}}'],
                                            'empty' => false,
                                            'options' => []
                                        ], $element['attr']);
                                        echo $this->Form->input($element['name'], $attr);
                                    }
                                    break;
                                case 'link':
                                    if(array_key_exists('text', $element) && array_key_exists('url', $element) && array_key_exists('attr', $element)){
                                        $attr = array_merge([
                                            'target' => '_self'
                                        ], $element['attr']);
                                        echo $this->Html->link($element['text'], $element['url'], $element['attr']);
                                    }
                                    break;
                                case 'icon':
                                    if(array_key_exists('icon', $element) && array_key_exists('text', $element) && array_key_exists('url', $element)){
                                        if(array_key_exists('action', $element)){
                                            $cls = array_key_exists('class', $element) ? $element['class'] : '';
                                            echo '<div data-url="' . $this->Url->build($element['url']) . '" class="button action-button ' . $cls . '" title="' . $element['text'] . '"><i class="fa fa-' . $element['icon'] . '"></i><div class="action">';
                                            $actions = array_filter(explode(":", $element['action']));
                                            if(in_array('select', $actions) && array_key_exists('select', $element)){
                                                $attr = array_merge([
                                                    'label' => false,
                                                    'templates' => ['inputContainer' => '{{content}}'],
                                                    'empty' => false,
                                                    'options' => []
                                                ], $element['select']['attr']);
                                                echo $this->Form->input($element['select']['name'], $attr);
                                            }
                                            if(in_array('input', $actions) && array_key_exists('input', $element)){
                                                $attr = array_merge([
                                                    'label' => false,
                                                    'templates' => ['inputContainer' => '{{content}}']
                                                ], $element['input']['attr']);
                                                echo $this->Form->input($element['input']['name'], $attr);
                                            }
                                            if(in_array('file', $actions) && array_key_exists('file', $element)){
                                                $attr = array_merge([
                                                    'label' => false,
                                                    'templates' => ['inputContainer' => '{{content}}'],
                                                    'type' => 'file',
                                                    'class' => 'test',
                                                ], $element['file']['attr']);
                                                // $attr['class'] = array_key_exists('class', $attr) ? $attr['class'] . ' hidden' : 'hidden';
                                                echo $this->Form->input($element['file']['name'], $attr);
                                                // echo '<a href="javascript:triggerClick(\'' . str_replace(['_'],['-'],$element['file']['name']) . '\')" class="button space">' . __d('be', 'Select file') . '</a>';
                                            }
                                            echo '</div><a href="#" class="send button submit"><i class="fa fa-arrow-right"></i></a><a href="#" class="cancel button submit"><i class="fa fa-times"></i></a><div class="clear"></div></div>';
                                        }else{
                                            echo $this->element('Backend.icon', ['icon' => $element['icon'], 'cls' => array_key_exists('class', $element) ? $element['class'] : false, 'text' => $element['text'], 'url' => $element['url'], 'confirm' => array_key_exists('confirm', $element) ? $element['confirm'] : false]);
                                        }
                                    }
                                    break;
                                case 'translations':
                                    if(array_key_exists('active', $element)){
                                        echo $this->element('Backend.translations', ['active' => $element['active']]);
                                    }
                                    break;
                                case 'upload':
                                    if(array_key_exists('id', $element) && array_key_exists('url', $element) && array_key_exists('text', $element)){
                                        echo '<form id="' . $element['id'] . '" action="' . $this->Url->build($element['url']). '" class="dropzone button">';
                                        echo '<div class="dz-message">' . $element['text'] . '</div>';
                                        echo '<div class="fallback">';
                                        echo '<input name="Filedata" type="file" multiple="multiple" />';
                                        echo '</div>';
                                        echo '</form>';
                                    }
                                    break;
                            }
                        }
                    } ?>
                </div>
                <?php } ?>
            <?php } ?>
            <div class="clear"></div>
        </div>
    </div>
</nav>