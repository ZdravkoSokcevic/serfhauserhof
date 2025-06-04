<?php use Cake\Core\Configure; ?>
<main class="room">
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="text">
        <?= $content['content']; ?>
        <?php if(array_key_exists('tours', $content) && is_array($content['tours']) && count($content['tours']) > 0){ ?>
        <div class="pano-tours">
            <div class="icon">
                <span>360°</span>
                <span class="small"><?= __d('fe', 'Tour'); ?></span>
            </div>
            <div class="links">
                <table><tr><td>
                    <?php foreach($content['tours'] as $tour){ ?>
                        <a href="<?= $tour['details']['link']; ?>" target="<?= $tour['details']['target']; ?>"><i class="fa fa-angle-right" aria-hidden="true"></i><span><?= __d('fe', '360°-Panoramatour'); ?>: </span><?= $tour['details']['title']; ?></a>
                    <?php } ?>
                </td></tr></table>
            </div>
            <div class="clear"></div>
        </div>
        <?php } ?>
        <?php if(is_array($content['info'])){ ?>
        <div class="price-info room"><?= $content['info'][0]['details']['textblock']; ?></div>
        <?php } ?>
        <?= $this->element('Frontend.Website/booking-actions', ['standalone' => false, 'type' => 'room', 'request' => $content['id'], 'book' => array_key_exists('vioma', $content) ? $content['vioma'] : false]) ?>
    </div>
    <div class="impressions">
        <ul class="viewport bxslider">
            <?php foreach($content['sketch'] as $img){ ?>
                <li>
                    <div class="bxslide sketch" style="background-position: <?= $img['details']['focus'][4]['css']; ?>; background-image: url('<?= $img['details']['seo'][4]; ?>');"></div>
                </li>
            <?php } ?>
            <?php foreach($content['images'] as $img){ ?>
                <li>
                    <div class="bxslide" style="background-position: <?= $img['details']['focus'][4]['css']; ?>; background-image: url('<?= $img['details']['seo'][4]; ?>');"></div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="clear"></div>
</main>
<?php if(array_key_exists($content['id'], $prices['values']) && count($prices['values'][$content['id']]) > 0){ ?>
<section class="prices room">
    <?php foreach($prices['containers'] as $container){ ?>
        <?php if(array_key_exists($content['id'], $prices['connections']) && count($prices['connections'][$content['id']]) > 0 && $prices['connections'][$content['id']]['ranges'][$container]['min']['draft']){ ?>
        <div class="price-table desktop prices-<?= $container; ?>">
            <div class="th">
                <h2><?php echo $container == 'summer' ? __d('fe', 'Summer') : __d('fe', 'Winter'); ?></h2>
                <div class="options">
                    <?php foreach($prices['options'] as $option => $name){ ?>
                        <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['options']) && in_array($option, $prices['connections'][$content['id']]['used']['options'][$container])){ ?>
                            <div class="option option-<?= $option; ?>">
                                <span><?= $name; ?></span>
                                <div class="drafts drafts-<?= count($prices['infos']['drafts-per-option'][$container][$option]); ?> overall-drafts-<?= count($prices['connections'][$content['id']]['used']['drafts'][$container]); ?>">
                                    <?php foreach($prices['drafts'] as $draft){ ?>
                                        <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['drafts']) && in_array($draft['id'], $prices['connections'][$content['id']]['used']['drafts'][$container]) && array_key_exists($container, $draft['used']['options']) && in_array($option, $draft['used']['options'][$container])){ ?>
                                            <div><?= $draft['translations']['title'] ?></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <?php foreach($prices['seasons'] as $season){ ?>
                <?php if($season['container'] == $container && array_key_exists($season['id'], $prices['values'][$content['id']])){ ?>
                    <div class="season">
                        <div class="title">
                            <?php if(is_array($prices['season_links']) && array_key_exists($season['id'], $prices['season_links']) && $prices['season_links'][$season['id']]['node']){ ?>
                            <a href="<?= $this->Url->build(['node' => 'node:' . $prices['season_links'][$season['id']]['node'], 'language' => $this->request->params['language']]); ?>"><i class="fa fa-angle-right" aria-hidden="true"></i><?= $season['translations']['title']; ?></a>
                            <?php }else{ ?>
                            <span><i class="fa fa-angle-right" aria-hidden="true"></i><?= $season['translations']['title']; ?></span>
                            <?php } ?>
                            <div class="times">
                                <div><?php $glue = ''; ?><?php foreach($season['times'] as $time){ ?><?= $glue . date("d.m.Y", $time['from']) . " - " . date("d.m.Y", $time['to']); ?><?php $glue = ', '; ?><?php } ?></div>
                            </div>
                        </div>
                        <div class="options">
                            <?php foreach($prices['options'] as $option => $name){ ?>
                                <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['options']) && in_array($option, $prices['connections'][$content['id']]['used']['options'][$container])){ ?>
                                    <div class="option option-<?= $option; ?>">
                                        <div class="drafts drafts-<?= count($prices['infos']['drafts-per-option'][$container][$option]); ?>">
                                            <?php foreach($prices['drafts'] as $draft){ ?>
                                                <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['drafts']) && in_array($draft['id'], $prices['connections'][$content['id']]['used']['drafts'][$container]) && array_key_exists($container, $draft['used']['options']) && in_array($option, $draft['used']['options'][$container])){ ?>
                                                    <?php if(array_key_exists($draft['id'], $prices['values'][$content['id']][$season['id']]) && array_key_exists($option, $prices['values'][$content['id']][$season['id']][$draft['id']])){ ?>
                                                        <div>
                                                            <div class="price">
                                                                <div class="sign">&euro;</div>
                                                                <div class="value"><?= number_format($prices['values'][$content['id']][$season['id']][$draft['id']][$option]['standard']['value'], 2, ',', '.'); ?></div>
                                                                <div class="clear"></div>
                                                            </div>
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div>--</div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="price-table mobile prices-<?= $container; ?>">
            <h2><?php echo $container == 'summer' ? __d('fe', 'Summer') : __d('fe', 'Winter'); ?></h2>
            <div class="options">
                <?php foreach($prices['options'] as $option => $name){ ?>
                    <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['options']) && in_array($option, $prices['connections'][$content['id']]['used']['options'][$container])){ ?>
                        <div class="th">
                            <div class="season">&nbsp;</div>
                            <div class="option option-<?= $option; ?>">
                                <span><?= $name; ?></span>
                                <div class="drafts drafts-<?= count($prices['infos']['drafts-per-option'][$container][$option]); ?> overall-drafts-<?= count($prices['connections'][$content['id']]['used']['drafts'][$container]); ?>">
                                    <?php foreach($prices['drafts'] as $draft){ ?>
                                        <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['drafts']) && in_array($draft['id'], $prices['connections'][$content['id']]['used']['drafts'][$container]) && array_key_exists($container, $draft['used']['options']) && in_array($option, $draft['used']['options'][$container])){ ?>
                                            <div><?= $draft['translations']['title'] ?></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php foreach($prices['seasons'] as $season){ ?>
                            <?php if($season['container'] == $container && array_key_exists($season['id'], $prices['values'][$content['id']])){ ?>
                                <div class="tr">
                                    <div class="season">
                                        <?php if(is_array($prices['season_links']) && array_key_exists($season['id'], $prices['season_links']) && $prices['season_links'][$season['id']]['node']){ ?>
                                        <a href="<?= $this->Url->build(['node' => 'node:' . $prices['season_links'][$season['id']]['node'], 'language' => $this->request->params['language']]); ?>"><?= $season['translations']['title']; ?></a>
                                        <?php }else{ ?>
                                        <span><?= $season['translations']['title']; ?></span>
                                        <?php } ?>
                                        <div class="times">
                                            <div><?php $glue = ''; ?><?php foreach($season['times'] as $time){ ?><?= $glue . date("d.m.Y", $time['from']) . " - " . date("d.m.Y", $time['to']); ?><?php $glue = ', '; ?><?php } ?></div>
                                        </div>
                                    </div>
                                    <div class="option option-<?= $option; ?>">
                                        <div class="drafts drafts-<?= count($prices['infos']['drafts-per-option'][$container][$option]); ?>">
                                            <?php foreach($prices['drafts'] as $draft){ ?>
                                                <?php if(array_key_exists($container, $prices['connections'][$content['id']]['used']['drafts']) && in_array($draft['id'], $prices['connections'][$content['id']]['used']['drafts'][$container]) && array_key_exists($container, $draft['used']['options']) && in_array($option, $draft['used']['options'][$container])){ ?>
                                                    <?php if(array_key_exists($draft['id'], $prices['values'][$content['id']][$season['id']]) && array_key_exists($option, $prices['values'][$content['id']][$season['id']][$draft['id']])){ ?>
                                                        <div>
                                                            <div class="price">
                                                                <div class="sign">&euro;</div>
                                                                <div class="value"><?= number_format($prices['values'][$content['id']][$season['id']][$draft['id']][$option]['standard']['value'], 2, ',', '.'); ?></div>
                                                                <div class="clear"></div>
                                                            </div>
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div>--</div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
</section>
<?= $this->element('Frontend.Website/booking-actions', ['standalone' => true, 'type' => 'room', 'request' => $content['id'], 'book' => array_key_exists('vioma', $content) ? $content['vioma'] : false]) ?>
<?php } ?>
<?= $this->element('Frontend.Website/back-link', ['back' => array_key_exists('back', $content) ? $content['back'] : false, 'next' => $next]) ?>
<?php if(is_array($overview)){ ?>
<section class="media media contain-1">
    <div class="media-group overview-group mg-contains-1">
        <div class="media-group-inner"><?= $this->element('Frontend.Website/overview', ['element_content' => $overview, 'skip' => $content['id']]) ?></div>
    </div>
</section>
<?php } ?>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>
