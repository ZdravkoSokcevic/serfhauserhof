<?php use Cake\Core\Configure; ?>
<?php

    // link
    $url = '#';
    $target = '_self';
    $text = !empty($element_content['details']['linktext']) ? $element_content['details']['linktext'] : __d('fe', 'more');
    if($element_content['details']['link'][0]['type'] == 'node'){
        $url = $this->Url->build(['node' => $element_content['details']['link'][0]['org'], 'language' => $this->request->params['language']]);
    }else if($element_content['details']['link'][0]['type'] == 'element' && $element_content['details']['link'][0]['details']['code'] == 'link'){
        $url = $element_content['details']['link'][0]['details']['link'];
        $target = $element_content['details']['link'][0]['details']['target'];
    }
    
?>
<a href="<?= $url; ?>" target="<?= $target; ?>" class="tiny-teaser nr-<?= $count; ?>">
    <div class="img" style="background-position: <?= $element_content['details']['image'][0]['details']['focus'][3]['css']; ?>; background-image: url('<?= $element_content['details']['image'][0]['details']['seo'][3]; ?>');"></div>
    <h2><?= $element_content['details']['headline']; ?></h2>
</a>