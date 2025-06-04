<?php if(array_key_exists('details', $element_content) && is_array($element_content['details']) && array_key_exists('_details', $element_content['details']) && is_array($element_content['details']['_details']) && count($element_content['details']['_details']['infos']) > 0){ ?>
<a class="anchor" id="<?= $element_content['id']; ?>" name="<?= $element_content['id']; ?>"></a>
<section class="pool" data-pool-id="<?= $element_content['id']; ?>">
    <div class="pool-text">
        <div class="line-1"><?=  $element_content['details']['line1']; ?></div>
        <div class="line-2"><?=  $element_content['details']['line2']; ?></div>
        <span class="line"></span>
    </div>
    <div class="images">
        <ul class="viewport bxslider">
        <?php foreach($element_content['details']['_details']['infos'] as $package){ ?>
            <li>
                <div class="bxslide" style="background-position: <?= $package['images'][0]['details']['focus'][1]['css']; ?>; background-image: url('<?= $package['images'][0]['details']['seo'][1]; ?>');"></div>
            </li>
        <?php } ?>
        </ul>
    </div>
    <div class="packages">
        <ul class="viewport bxslider">
        <?php foreach($element_content['details']['_details']['infos'] as $package){ ?>
            <?php $url = array_key_exists($package['id'], $element_content['details']['_details']['nodes']) ? $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$package['id']], 'language' => $this->request->params['language']]) : false; ?>
            <li>
                <article class="bxslide<?php echo $url ? '' : ' no-link'; ?>">
                    <div class="img" style="background-position: <?= $package['images'][0]['details']['focus'][1]['css']; ?>; background-image: url('<?= $package['images'][0]['details']['seo']['1_small']; ?>');"></div>
                    <?php if(count($package['valid_times']) > 0){ ?>
                    <span class="times">
                        <?php foreach($package['valid_times'] as $time){ ?>
                            <span class="time"><?= $time['from'] . ' - ' . $time['to']; ?></span>
                        <?php } ?>
                    </span>
                    <?php } ?>
                    <h2><?= $package['title']; ?></h2>
                    <div class="text"><?= $package['teaser']; ?></div>
                    <?php if(array_key_exists($package['id'], $element_content['details']['_details']['prices']['connections'])){ ?>
                        <div class="prices">
                            <div class="price">
                                <span><?= $element_content['details']['_details']['prices']['drafts'][$element_content['details']['_details']['prices']['connections'][$package['id']]['ranges'][$element_content['details']['_details']['prices']['infos']['global']]['min']['draft']]['translations']['title']; ?></span>
                                <span class="price"><?= __d('fe', 'from € %s', number_format($element_content['details']['_details']['prices']['connections'][$package['id']]['ranges'][$element_content['details']['_details']['prices']['infos']['global']]['min']['value'], 2, ",", ".")); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if($url){ ?><a class="button" href="<?= $url; ?>"><?= __d('fe', 'show offer'); ?></a><?php } ?>
                </article>
            </li>
        <?php } ?>
        </ul>
    </div>
</section>
<?php } ?>