<?php

    // init
    $links = [];

    // link tl
    $key = 'tl';
    $links[$key] = ['url' => false, 'target' => '_self', 'tag' => 'div', 'attr' => '', 'text' => false];
    if(array_key_exists('link-' . $key, $element_content['details']) && is_array($element_content['details']['link-' . $key]) && array_key_exists(0, $element_content['details']['link-' . $key]) && count($element_content['details']['link-' . $key][0]) > 0){
        if($element_content['details']['link-' . $key][0]['type'] == 'node'){
            $links[$key]['url'] = $this->Url->build(['node' => $element_content['details']['link-' . $key][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link-' . $key][0]['type'] == 'element' && $element_content['details']['link-' . $key][0]['details']['code'] == 'link'){
            $links[$key]['url'] = $element_content['details']['link-' . $key][0]['details']['link'];
            $links[$key]['target'] = $element_content['details']['link-' . $key][0]['details']['target'];
        }
        if($links[$key]['url']){
            $links[$key]['tag'] = 'a';
            $links[$key]['attr'] = ' href="' . $links[$key]['url'] . '" target="' . $links[$key]['target'] . '"';
        }
    }
    if(array_key_exists('text_' . $key, $element_content['details']) && !empty($element_content['details']['text_' . $key])){
        $links[$key]['text'] = $element_content['details']['text_' . $key];
    }
    
    // link bl
    $key = 'bl';
    $links[$key] = ['url' => false, 'target' => '_self', 'tag' => 'div', 'attr' => '', 'text' => false];
    if(array_key_exists('link-' . $key, $element_content['details']) && is_array($element_content['details']['link-' . $key]) && array_key_exists(0, $element_content['details']['link-' . $key]) && count($element_content['details']['link-' . $key][0]) > 0){
        if($element_content['details']['link-' . $key][0]['type'] == 'node'){
            $links[$key]['url'] = $this->Url->build(['node' => $element_content['details']['link-' . $key][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link-' . $key][0]['type'] == 'element' && $element_content['details']['link-' . $key][0]['details']['code'] == 'link'){
            $links[$key]['url'] = $element_content['details']['link-' . $key][0]['details']['link'];
            $links[$key]['target'] = $element_content['details']['link-' . $key][0]['details']['target'];
        }
        if($links[$key]['url']){
            $links[$key]['tag'] = 'a';
            $links[$key]['attr'] = ' href="' . $links[$key]['url'] . '" target="' . $links[$key]['target'] . '"';
        }
    }
    if(array_key_exists('text_' . $key, $element_content['details']) && !empty($element_content['details']['text_' . $key])){
        $links[$key]['text'] = $element_content['details']['text_' . $key];
    }
    
    // link c
    $key = 'c';
    $links[$key] = ['url' => false, 'target' => '_self', 'tag' => 'div', 'attr' => '', 'text' => false];
    if(array_key_exists('link-' . $key, $element_content['details']) && is_array($element_content['details']['link-' . $key]) && array_key_exists(0, $element_content['details']['link-' . $key]) && count($element_content['details']['link-' . $key][0]) > 0){
        if($element_content['details']['link-' . $key][0]['type'] == 'node'){
            $links[$key]['url'] = $this->Url->build(['node' => $element_content['details']['link-' . $key][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link-' . $key][0]['type'] == 'element' && $element_content['details']['link-' . $key][0]['details']['code'] == 'link'){
            $links[$key]['url'] = $element_content['details']['link-' . $key][0]['details']['link'];
            $links[$key]['target'] = $element_content['details']['link-' . $key][0]['details']['target'];
        }
        if($links[$key]['url']){
            $links[$key]['tag'] = 'a';
            $links[$key]['attr'] = ' href="' . $links[$key]['url'] . '" target="' . $links[$key]['target'] . '"';
        }
    }
    if(array_key_exists('text_' . $key, $element_content['details']) && !empty($element_content['details']['text_' . $key])){
        $links[$key]['text'] = $element_content['details']['text_' . $key];
    }
    
    // link br
    $key = 'br';
    $links[$key] = ['url' => false, 'target' => '_self', 'tag' => 'div', 'attr' => '', 'text' => false];
    if(array_key_exists('link-' . $key, $element_content['details']) && is_array($element_content['details']['link-' . $key]) && array_key_exists(0, $element_content['details']['link-' . $key]) && count($element_content['details']['link-' . $key][0]) > 0){
        if($element_content['details']['link-' . $key][0]['type'] == 'node'){
            $links[$key]['url'] = $this->Url->build(['node' => $element_content['details']['link-' . $key][0]['org'], 'language' => $this->request->params['language']]);
        }else if($element_content['details']['link-' . $key][0]['type'] == 'element' && $element_content['details']['link-' . $key][0]['details']['code'] == 'link'){
            $links[$key]['url'] = $element_content['details']['link-' . $key][0]['details']['link'];
            $links[$key]['target'] = $element_content['details']['link-' . $key][0]['details']['target'];
        }
        if($links[$key]['url']){
            $links[$key]['tag'] = 'a';
            $links[$key]['attr'] = ' href="' . $links[$key]['url'] . '" target="' . $links[$key]['target'] . '"';
        }
    }
    if(array_key_exists('text_' . $key, $element_content['details']) && !empty($element_content['details']['text_' . $key])){
        $links[$key]['text'] = $element_content['details']['text_' . $key];
    }
    
?>
<div class="clear"></div>
<article class="impressions-teaser">
    <div class="left">
        <<?= $links['tl']['tag'] . $links['tl']['attr']; ?> class="img img-tl" style="background-position: <?= $element_content['details']['image-tl'][0]['details']['focus'][3]['css']; ?>; background-image: url('<?= $element_content['details']['image-tl'][0]['details']['seo'][3]; ?>');">
            <?php if($links['tl']['text']){ ?><div class="frame"><div><h3><?= $links['tl']['text']; ?></h3></div></div><?php } ?>
        </<?= $links['tl']['tag']; ?>>
        <div class="bottom">
            <<?= $links['bl']['tag'] . $links['bl']['attr']; ?> class="img img-bl" style="background-position: <?= $element_content['details']['image-bl'][0]['details']['focus'][4]['css']; ?>; background-image: url('<?= $element_content['details']['image-bl'][0]['details']['seo'][4]; ?>');">
                <?php if($links['bl']['text']){ ?><div class="frame"><div><h3><?= $links['bl']['text']; ?></h3></div></div><?php } ?>
            </<?= $links['bl']['tag']; ?>>
            <div class="text text-bl">
                <table>
                    <tr>
                        <td><h2><?= $element_content['details']['box1_headline']; ?></h2><p><?= $element_content['details']['box1_content']; ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <<?= $links['c']['tag'] . $links['c']['attr']; ?> class="img img-c" style="background-position: <?= $element_content['details']['image-c'][0]['details']['focus'][4]['css']; ?>; background-image: url('<?= $element_content['details']['image-c'][0]['details']['seo'][4]; ?>');">
        <?php if($links['c']['text']){ ?><div class="frame"><div><h3><?= $links['c']['text']; ?></h3></div></div><?php } ?>
    </<?= $links['c']['tag']; ?>>
    <div class="right">
        <div class="top">
            <div class="text text-tr">
                <table>
                    <tr>
                        <td><h2><?= $element_content['details']['box2_headline']; ?></h2><p><?= $element_content['details']['box2_content']; ?></p></td>
                    </tr>
                </table>
            </div>
            <<?= $links['br']['tag'] . $links['br']['attr']; ?> class="img img-br" style="background-position: <?= $element_content['details']['image-br'][0]['details']['focus'][4]['css']; ?>; background-image: url('<?= $element_content['details']['image-br'][0]['details']['seo'][4]; ?>');">
                <?php if($links['br']['text']){ ?><div class="frame"><div><h3><?= $links['br']['text']; ?></h3></div></div><?php } ?>
            </<?= $links['br']['tag']; ?>>
        </div>
        <<?= $links['br']['tag'] . $links['br']['attr']; ?> class="img img-c" style="background-position: <?= $element_content['details']['image-br2'][0]['details']['focus'][3]['css']; ?>; background-image: url('<?= $element_content['details']['image-br2'][0]['details']['seo'][3]; ?>');">
            <?php if($links['br']['text']){ ?><div class="frame"><div><h3><?= $links['br']['text']; ?></h3></div></div><?php } ?>
        </<?= $links['br']['tag']; ?>>
    </div>
    <div class="clear"></div>
</article>
<div class="clear"></div>