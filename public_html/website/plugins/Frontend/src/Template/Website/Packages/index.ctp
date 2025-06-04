<main class="room">
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <?= $content['content']; ?>
    <h2 class="underlined"><?= $content['services_headline']; ?></h2>
    <?= $content['services']; ?>
</main>
<?= $this->element('Frontend.Website/booking-actions', ['standalone' => true, 'type' => 'package', 'request' => $content['id'], 'book' => array_key_exists('vioma', $content) ? $content['vioma'] : false]) ?>
<?php if(array_key_exists($content['id'], $prices['values']) && count($prices['values'][$content['id']]) > 0){ ?>
<?php $pi = array_key_exists('spipt', $content) && $content['spipt'] ? true : false; ?>
<section class="prices inner package<?php echo $pi ? ' with-person-info' : ''; ?>">
    <div class="price-table desktop">
        <div class="desc">
            <div class="title th">&nbsp;<?php if($pi){ ?><span class="pax"><?= __d('fe', 'Price for*'); ?></span><span class="clear"></span><?php } ?></div>
            <?php foreach($prices['elements'] as $room){ ?>
                <?php if(array_key_exists('node', $room) && strlen($room['node']) == 36){ ?>
                <a href="<?= $this->Url->build(['node' => 'node:'.$room['node'], 'language' => $this->request->params['language']]); ?>" class="room"><i class="fa fa-angle-right" aria-hidden="true"></i><?= $room['title']; ?><?php if($pi){ ?><span class="pax"><?= __d('fe', '%s p.', $room['fields']['occupancy']); ?></span><span class="clear"></span><?php } ?></a>
                <?php }else{ ?>
                <div class="room"><?= $room['title']; ?><?php if($pi){ ?><span class="pax"><?= __d('fe', '%s p.', $room['fields']['occupancy']); ?></span><span class="clear"></span><?php } ?></div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="viewport">
            <?php foreach($prices['seasons'] as $season){ ?>
                <?php if(array_key_exists($season['id'], $prices['values'][$content['id']])){ ?>
                    <?php foreach($prices['drafts'] as $draft){ ?>
                        <?php if(array_key_exists($draft['id'], $prices['infos']['elements-per-draft-and-season']) && array_key_exists($season['id'], $prices['infos']['elements-per-draft-and-season'][$draft['id']]) && count($prices['infos']['elements-per-draft-and-season'][$draft['id']][$season['id']]) > 0){ ?>
                        <div class="draft">
                            <div class="title th">
                                <span><?= $draft['translations']['title']; ?></span>
                                <div class="times">
                                    <div><?php $glue = ''; ?><?php foreach($season['times'] as $time){ ?><?= $glue . date("d.m.Y", $time['from']) . " - " . date("d.m.Y", $time['to']); ?><?php $glue = ', '; ?><?php } ?></div>
                                </div>
                            </div>
                            <?php foreach($prices['elements'] as $id => $room){ ?>
                            <div class="room">
                                <?php if(array_key_exists($id, $prices['values'][$content['id']][$season['id']]) && is_array($prices['values'][$content['id']][$season['id']][$id]) && array_key_exists($draft['id'], $prices['values'][$content['id']][$season['id']][$id])){ ?>
                                    <div>
                                        <div class="price">
                                            <div class="sign">&euro;</div>
                                            <div class="value"><?= number_format($prices['values'][$content['id']][$season['id']][$id][$draft['id']]['standard']['value'], 2, ',', '.'); ?></div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                <?php }else{ ?>
                                    <div>--</div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <div class="price-table mobile">
        <?php foreach($prices['elements'] as $id => $room){ ?>
        <div class="room">
            <?php if(array_key_exists('node', $room) && strlen($room['node']) == 36){ ?>
            <h2><a href="<?= $this->Url->build(['node' => 'node:'.$room['node'], 'language' => $this->request->params['language']]); ?>" class="room"><?= $room['title']; ?></a><?php if($pi){ ?><span><?= __dn('fe', 'Price for %s person', 'Price for %s persons', $room['fields']['occupancy'], $room['fields']['occupancy']); ?></span><?php } ?></h2>
            <?php }else{ ?>
            <h2><?= $room['title']; ?><?php if($pi){ ?><span><?= __dn('fe', 'Price for %s person', 'Price for %s persons', $room['fields']['occupancy'], $room['fields']['occupancy']); ?></span><?php } ?></h2>
            <?php } ?>
            <div class="drafts">
                <?php foreach($prices['seasons'] as $season){ ?>
                    <?php if(array_key_exists($season['id'], $prices['values'][$content['id']])){ ?>
                        <?php foreach($prices['drafts'] as $draft){ ?>
                            <?php if(array_key_exists($draft['id'], $prices['infos']['elements-per-draft-and-season']) && array_key_exists($season['id'], $prices['infos']['elements-per-draft-and-season'][$draft['id']]) && count($prices['infos']['elements-per-draft-and-season'][$draft['id']][$season['id']]) > 0){ ?>
                            <div class="draft">
                                <div class="title">
                                    <span><?= $draft['translations']['title']; ?></span>
                                    <div class="times">
                                        <div><?php $glue = ''; ?><?php foreach($season['times'] as $time){ ?><?= $glue . date("d.m.Y", $time['from']) . " - " . date("d.m.Y", $time['to']); ?><?php $glue = ', '; ?><?php } ?></div>
                                    </div>
                                </div>
                                <div class="price">
                                    <?php if(array_key_exists($id, $prices['values'][$content['id']][$season['id']]) && is_array($prices['values'][$content['id']][$season['id']][$id]) && array_key_exists($draft['id'], $prices['values'][$content['id']][$season['id']][$id])){ ?>
                                        <div>
                                            <div class="price">
                                                <div class="sign">&euro;</div>
                                                <div class="value"><?= number_format($prices['values'][$content['id']][$season['id']][$id][$draft['id']]['standard']['value'], 2, ',', '.'); ?></div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div>--</div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<?= $this->element('Frontend.Website/booking-actions', ['standalone' => true, 'type' => 'package', 'request' => $content['id'], 'book' => array_key_exists('vioma', $content) ? $content['vioma'] : false]) ?>
<?php if(is_array($content['info'])){ ?>
<div class="price-info package inner"><?= $content['info'][0]['details']['textblock']; ?></div>
<?php } ?>
<?php } ?>
<?= $this->element('Frontend.Website/back-link', ['back' => array_key_exists('back', $content) ? $content['back'] : false]) ?>
<?php if(is_array($overview)){ ?>
<section class="media media contain-1">
    <div class="media-group overview-group mg-contains-1">
        <div class="media-group-inner"><?= $this->element('Frontend.Website/overview', ['element_content' => $overview, 'skip' => $content['id']]) ?></div>
    </div>
</section>
<?php } ?>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>
