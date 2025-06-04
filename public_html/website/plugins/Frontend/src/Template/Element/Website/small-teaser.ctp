<?php use Cake\Core\Configure; ?>
<?php

    // link 1
    $url1 = false;
    $target1 = '_self';
    $text1 = array_key_exists('linktext', $element_content['details']) && !empty($element_content['details']['linktext']) ? $element_content['details']['linktext'] : __d('fe', 'more');
    if(array_key_exists('link', $element_content['details']) && is_array($element_content['details']['link'])){
        if($element_content['details']['link'][0]['type'] == 'node'){
            $url1 = $this->Url->build(['node' => $element_content['details']['link'][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link'][0]['type'] == 'element' && $element_content['details']['link'][0]['details']['code'] == 'link'){
            $url1 = $element_content['details']['link'][0]['details']['link'];
            $target1 = $element_content['details']['link'][0]['details']['target'];
        }else if($element_content['details']['link'][0]['type'] == 'element' && $element_content['details']['link'][0]['details']['code'] == 'download'){
            $url1 = '/provide/download/' . $this->request->params['language'] . '/' . $element_content['details']['link'][0]['details']['id'] . '/';
            $target1 = '_blank';
        }
    }
    
    // link 2
    $url2 = false;
    $target2 = '_self';
    $text2 = array_key_exists('linktext2', $element_content['details']) && !empty($element_content['details']['linktext2']) ? $element_content['details']['linktext2'] : __d('fe', 'more');
    if(array_key_exists('link2', $element_content['details']) && is_array($element_content['details']['link2'])){
        if($element_content['details']['link2'][0]['type'] == 'node'){
            $url2 = $this->Url->build(['node' => $element_content['details']['link2'][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link2'][0]['type'] == 'element' && $element_content['details']['link2'][0]['details']['code'] == 'link'){
            $url2 = $element_content['details']['link2'][0]['details']['link'];
            $target2 = $element_content['details']['link2'][0]['details']['target'];
        }else if($element_content['details']['link2'][0]['type'] == 'element' && $element_content['details']['link2'][0]['details']['code'] == 'download'){
            $url2 = '/provide/download/' . $this->request->params['language'] . '/' . $element_content['details']['link2'][0]['details']['id'] . '/';
            $target2 = '_blank';
        }
    }
    
?>
<article class="small-teaser nr-<?= $count; ?><?php echo $url1 && $url2 ? ' two-buttons' : ''; ?>">
    <?php if($url1 && $url2){ ?>
    <div class="img" style="background-position: <?= $element_content['details']['image'][0]['details']['focus'][3]['css']; ?>; background-image: url('<?= $element_content['details']['image'][0]['details']['seo'][3]; ?>');"></div>
    <?php }else{ ?>
    <a href="<?= $url1; ?>" target="<?= $target1; ?>" class="img" style="background-position: <?= $element_content['details']['image'][0]['details']['focus'][3]['css']; ?>; background-image: url('<?= $element_content['details']['image'][0]['details']['seo'][3]; ?>');"></a>
    <?php } ?>
    <a href="<?= $url1; ?>" target="<?= $target1; ?>">
        <div class="text">
            <h2><?= $element_content['details']['headline']; ?></h2>
            <p><?= $element_content['details']['content']; ?></p>
        </div>
    </a>
    <div class="buttons">
        <a href="<?= $url1; ?>" class="button" target="<?= $target1; ?>"><?= $text1; ?></a>
        <?php if($url2){ ?>
        <a href="<?= $url2; ?>" class="button dark" target="<?= $target2; ?>"><?= $text2; ?></a>
        <?php } ?>
    </div>
</article>