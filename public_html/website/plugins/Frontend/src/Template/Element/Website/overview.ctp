<?php use Cake\Core\Configure; ?>
<?php if(array_key_exists('details', $element_content) && is_array($element_content['details']) && array_key_exists('_details', $element_content['details']) && is_array($element_content['details']['_details']) && count($element_content['details']['_details']['infos']) > 0){ ?>
    <?php if($element_content['details']['type'] == 'treatment'){ ?>
    <section class="treatments inner <?= $element_content['details']['type']; ?>">
        <div class="description">
        <h2><?= $element_content['details']['headline']; ?></h2>
        <?php if(!empty($element_content['details']['content'])){ ?>
        <p><?= $element_content['details']['content']; ?></p>
        <?php } ?>
        </div>
        <?php $nr = 0; ?>
        <div class="treatments-wrapper">
        <?php foreach($element_content['details']['_details']['infos'] as $k => $v){ ?>
            <div class="treatment-wrapper">
                <div class="treatment">
                    <h3><?= $v['title']; ?></h3>
                    <div class="details enumeration">
                        <?= $v['content']; ?>
                        <?php if(array_key_exists($v['id'], $element_content['details']['_details']['prices']['values']) && count($element_content['details']['_details']['prices']['values'][$v['id']]) > 0){ ?>
                        <div class="prices">
                            <?php foreach($element_content['details']['_details']['prices']['drafts'] as $draft){ ?>
                                <?php if(array_key_exists($draft['id'], $element_content['details']['_details']['prices']['values'][$v['id']])){ ?>
                                    <div class="draft"><?= join(", ", array_filter($draft['translations'])) ?></div>
                                    <div class="price"><span>&euro;</span> <?= number_format($element_content['details']['_details']['prices']['values'][$v['id']][$draft['id']]['standard']['value'], 2, ',', '.'); ?><div class="clear"></div></div>
                                    <div class="clear"></div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <a class="button" data-text-open="<?= __d('fe', 'more information'); ?>" data-text-close="<?= __d('fe', 'close'); ?>"><i class="fa fa-times" aria-hidden="true"></i><span><?= __d('fe', 'more information'); ?></span></a>
                <div class="clear"></div>
            </div>
            <?php $nr++; ?>
        <?php } ?>
        </div>
    </section>
    <?php }else if(in_array($element_content['details']['type'], ['room','package'])){ ?>
    <?php
        $key = $element_content['details']['type'] . 's';
        $text = $element_content['details']['type'] == 'room' ? 'content' : 'teaser';
        $usage = $element_content['details']['type'] == 'room' ? 4 : '1_small';
        $focus = $element_content['details']['type'] == 'room' ? 4 : 1;
        $image = $element_content['details']['type'] == 'room' ? 'sketch' : 'images';
        $add = $element_content['details']['type'] == 'room' ? ' show-all' : ' show-all';
    ?>
    <?php if(is_array($element_content['details']['_details']['nav']) && count($element_content['details']['_details']['nav']) > 1){ ?>
    <ul class="overview-navi">
        <?php foreach($element_content['details']['_details']['nav'] as $nav){ ?>
        <li class="<?= $element_content['details'][$key][0]['id'] == $nav['id'] ? 'active' : ''; ?>"><a href="<?= $this->Url->build(['node' => $nav['rel'], 'language' => $this->request->params['language']]); ?>"><?= $nav['title']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    <section class="overview <?= $element_content['details']['type'] . $add; ?>">
        <?php $nr = 0; ?>
        <?php foreach($element_content['details']['_details']['infos'] as $k => $v){ ?>
            <?php if(!isset($skip) || $skip === false || $skip !== $v['id']){ ?>
            <?php $nr++; ?>
            <div class="overview-teaser col-1-line-<?= $nr; ?> col-2-line-<?= ceil($nr/2); ?> col-3-line-<?= ceil($nr/3); ?> col-4-line-<?= ceil($nr/4); ?>">
                <?php if(array_key_exists($v['id'], $element_content['details']['_details']['nodes'])){ ?>
                <a href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$v['id']], 'language' => $this->request->params['language']]); ?>" class="img" style="background-position: <?= $v[$image][0]['details']['focus'][$focus]['css']; ?>; background-image: url('<?= $v[$image][0]['details']['seo'][$usage]; ?>');"></a>
                <?php }else{ ?>
                <div class="img" style="background-position: <?= $v[$image][0]['details']['focus'][$focus]['css']; ?>; background-image: url('<?= $v[$image][0]['details']['seo'][$usage]; ?>');"></div>
                <?php } ?>
                <?php if(array_key_exists($v['id'], $element_content['details']['_details']['nodes'])){ ?>
                <a href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$v['id']], 'language' => $this->request->params['language']]); ?>">
                <?php }else{ ?>
                <div>
                <?php } ?>
                    <div class="txt">
                        <?php if($element_content['details']['type'] == 'package'){ ?>
                        <?php if(count($v['valid_times']) > 0){ ?>
                        <div class="times">
                            <?php foreach($v['valid_times'] as $t){ ?>
                            <div><?= $t['from'] . ' - ' . $t['to']; ?></div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <h2><?= $v['title'] ?></h2>
                        <?php if($element_content['details']['type'] == 'package'){ ?>
                        <?= $v[$text]; ?>
                        <?php }else{ ?>
                        <?= $this->Text->truncate($v[$text], 250, ['html' => true]); ?>
                        <?php } ?>
                    </div>
                    <?php if(array_key_exists($v['id'], $element_content['details']['_details']['prices']['connections'])){ ?>
                        <div class="prices">
                            <?php if($element_content['details']['type'] == 'room'){ ?>
                            <?php foreach($element_content['details']['_details']['prices']['containers'] as $container){ ?>
                            <?php if($element_content['details']['_details']['prices']['connections'][$v['id']]['ranges'][$container]['min']['draft']){ ?>
                            <div class="price">
                                <span class="price"><?php echo  $container == 'summer' ? __d('fe', 'Summer') : __d('fe', 'Winter'); ?> <?= __d('fe', 'from € %s', number_format($element_content['details']['_details']['prices']['connections'][$v['id']]['ranges'][$container]['min']['value'], 2, ",", ".")); ?></span>
                                <span><?php echo $element_content['details']['_details']['prices']['drafts'][$element_content['details']['_details']['prices']['connections'][$v['id']]['ranges'][$container]['min']['draft']]['translations']['caption']; ?></span>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <?php }else{ ?>
                            <div class=price">
                                <span><?= $element_content['details']['_details']['prices']['drafts'][$element_content['details']['_details']['prices']['connections'][$v['id']]['ranges'][$element_content['details']['_details']['prices']['infos']['global']]['min']['draft']]['translations']['title']; ?></span>
                                <span class="price"><?= __d('fe', 'from € %s', number_format($element_content['details']['_details']['prices']['connections'][$v['id']]['ranges'][$element_content['details']['_details']['prices']['infos']['global']]['min']['value'], 2, ",", ".")); ?></span>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php if(array_key_exists($v['id'], $element_content['details']['_details']['nodes'])){ ?>
                </a>
                <?php }else{ ?>
                </div>
                <?php } ?>
                <div class="buttons <?= $element_content['details']['type'] == 'room' && array_key_exists($v['id'], $element_content['details']['_details']['nodes']) ? ' two' : ''; ?>">
                    <?php if(array_key_exists($v['id'], $element_content['details']['_details']['nodes'])){ ?>
                    <a href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$v['id']], 'language' => $this->request->params['language']]); ?>" class="button s2u"><?= __d('fe', 'Details'); ?></a>
                    <?php } ?>
                    <?php if($element_content['details']['type'] == 'room'){ ?>
                    <a href="<?= $this->Url->build(['node' => Configure::read('config.default.book.0.org'), 'language' => $this->request->params['language'], '?' => array_key_exists('vioma', $v) && !empty($v['vioma']) ? [$element_content['details']['type'] => $v['vioma']] : false]); ?>" class="button dark s2u"><?= __d('fe', 'Book'); ?></a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
        <?php if($nr === 0){ ?>
        <div class="no-cat-info"><?= __d('fe', 'No categories available!'); ?></div>
        <?php } ?>
    </section>
    <?php }else if($element_content['details']['type'] == 'room-total'){ ?>
    <?php
        $wrapper_width = "width: " .  100*(count($element_content['details']['_details']['infos'])/5) . "%;";
        $room_width = "width: calc((100% / " . count($element_content['details']['_details']['infos']) . ") - 9px);";
    ?>
    <section class="overview <?= $element_content['details']['type']; ?>">
        <div class="row th">
            <div class="season"><table><tr><td><?= __d('fe', 'Period'); ?></td></tr></table></div>
            <div class="options"><table><tr><td><?= __d('fe', 'Duration'); ?></td></tr></table></div>
            <div class="rooms-wrapper">
                <div class="rooms" style="<?= $wrapper_width; ?>">
                <?php foreach($element_content['details']['_details']['infos'] as $room){ ?>
                    <div class="room" style="<?= $room_width; ?>">
                        <table><tr><td><?php if(array_key_exists($room['id'], $element_content['details']['_details']['nodes'])){ ?><a href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$room['id']], 'language' => $this->request->params['language']]); ?>"><?= $room['title']; ?></a><?php }else{ ?><?= $room['title']; ?><?php } ?></td></tr></table>
                    </div>
                <?php } ?>
                <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <?php if(count($element_content['details']['_details']['infos']) > 5){ ?>
            <a href="javascript:void(0);" onclick="slidePrices('prev')" class="nav prev hidden"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-chevron-left fa-stack-1x"></i></span></a>
            <a href="javascript:void(0);" onclick="slidePrices('next')" class="nav next"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-chevron-right fa-stack-1x"></i></span></a>
            <?php } ?>
        </div>
        <div class="row">
            <?php $line = 0; ?>
            <?php foreach($element_content['details']['_details']['prices']['seasons'] as $season){ ?>
                <?php if(array_key_exists($season['id'], $element_content['details']['_details']['prices']['infos']['drafts-per-season']) && count($element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']]) > 0){ ?>
                <?php
                    $valid = false;
                    foreach($season['times'] as $time){
                        if($time['to'] > time()) $valid = true;
                    }

                    $lh = 21;
                    $row_height = 20; // padding
                    $co = 1;
                    foreach($element_content['details']['_details']['prices']['options'] as $k1 => $option){
                        if(array_key_exists($k1, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']])){
                            $row_height += $lh;
                            foreach($element_content['details']['_details']['prices']['drafts'] as $k2 => $draft){
                                if(in_array($k2, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']][$k1])){
                                    $row_height += $lh;
                                }
                            }
                            if($co > 1){
                                $row_height += 11; // border
                            }
                            $co++;
                        }
                    }
                ?>
                <div class="season<?php echo $line%2 ? ' even' : ' odd'; ?><?php echo $valid ? '' : ' expired-season'; ?>" style="height: <?= $row_height; ?>px;">
                    <?php if(is_array($element_content['details']['_details']['prices']['season_links']) && array_key_exists($season['id'], $element_content['details']['_details']['prices']['season_links']) && $element_content['details']['_details']['prices']['season_links'][$season['id']]['node']){ ?>
                    <a class="h3-like" href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['prices']['season_links'][$season['id']]['node'], 'language' => $this->request->params['language']]); ?>"><?= $season['translations']['title']; ?></a>
                    <?php }else{ ?>
                    <h3><?= $season['translations']['title']; ?></h3>
                    <?php } ?>
                    <div class="times">
                        <?php foreach($season['times'] as $time){ ?>
                        <div class="time"><?= date("d.m.Y", $time['from']) . ' - ' . date("d.m.Y", $time['to']); ?></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="options<?php echo $line%2 ? ' even' : ' odd'; ?><?php echo $valid ? '' : ' expired-season'; ?>">
                    <?php $co = 1; ?>
                    <?php foreach($element_content['details']['_details']['prices']['options'] as $k1 => $option){ ?>
                        <?php if(array_key_exists($k1, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']])){ ?>
                        <div class="option option-<?= $co; ?>">
                            <h3><?= $option; ?></h3>
                            <div class="drafts">
                            <?php $cd = 1; ?>
                            <?php foreach($element_content['details']['_details']['prices']['drafts'] as $k2 => $draft){ ?>
                                <?php if(in_array($k2, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']][$k1])){ ?>
                                <div class="draft-<?= $cd; ?>"><?= $draft['translations']['title']; ?></div>
                                <?php $cd++; ?>
                                <?php } ?>
                            <?php } ?>
                            </div>
                        </div>
                        <?php $co++; ?>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="rooms-wrapper rooms-<?= count($element_content['details']['_details']['infos']); ?><?php echo $line%2 ? ' even' : ' odd'; ?><?php echo $valid ? '' : ' expired-season'; ?>">
                    <div class="rooms" style="<?= $wrapper_width; ?>">
                        <?php $rn = 0; ?>
                        <?php foreach($element_content['details']['_details']['infos'] as $room){ ?>
                            <div class="room pos-<?= $rn%2; ?>" style="<?= $room_width; ?>">
                                <div class="title" style="height: <?= $row_height; ?>px;">
                                    <table><tr><td><?php if(array_key_exists($room['id'], $element_content['details']['_details']['nodes'])){ ?><a href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['_details']['nodes'][$room['id']], 'language' => $this->request->params['language']]); ?>"><?= $room['title']; ?></a><?php }else{ ?><?= $room['title']; ?><?php } ?></td></tr></table>
                                </div>
                                <div class="options">
                                    <?php $co = 1; ?>
                                    <?php foreach($element_content['details']['_details']['prices']['options'] as $k1 => $option){ ?>
                                        <?php if(array_key_exists($k1, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']])){ ?>
                                        <div class="option option-<?= $co; ?>">
                                            <h3><?= $option; ?></h3>
                                            <div class="drafts">
                                            <?php $cd = 1; ?>
                                            <?php foreach($element_content['details']['_details']['prices']['drafts'] as $k2 => $draft){ ?>
                                                <?php if(in_array($k2, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']][$k1])){ ?>
                                                <div class="draft-<?= $cd; ?>"><?= $draft['translations']['title']; ?></div>
                                                <?php $cd++; ?>
                                                <?php } ?>
                                            <?php } ?>
                                            </div>
                                        </div>
                                        <?php $co++; ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                                <?php $co = 1; ?>
                                 <div class="prices">
                                <?php foreach($element_content['details']['_details']['prices']['options'] as $k1 => $option){ ?>
                                    <?php if(array_key_exists($k1, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']])){ ?>
                                    <div class="price price-<?= $co; ?>">
                                        <h3>&nbsp;</h3>
                                        <div>
                                        <?php $cd = 1; ?>
                                        <?php foreach($element_content['details']['_details']['prices']['drafts'] as $k2 => $draft){ ?>
                                            <?php if(in_array($k2, $element_content['details']['_details']['prices']['infos']['drafts-per-season'][$season['id']][$k1])){ ?>
                                                <?php if(array_key_exists($room['id'], $element_content['details']['_details']['prices']['values']) && array_key_exists($season['id'], $element_content['details']['_details']['prices']['values'][$room['id']]) && array_key_exists($draft['id'], $element_content['details']['_details']['prices']['values'][$room['id']][$season['id']]) && array_key_exists($k1, $element_content['details']['_details']['prices']['values'][$room['id']][$season['id']][$draft['id']])){ ?>
                                                    <div class="value-<?= $cd; ?>"><div class="currency">&euro;</div><div class="value"><?= custom_number_format($element_content['details']['_details']['prices']['values'][$room['id']][$season['id']][$draft['id']][$k1]['standard']['value'], 2, ',', '.'); ?></div><div class="clear"></div></div>
                                                <?php }else{ ?>
                                                    <div class="value-<?= $cd; ?>">--</div>
                                                <?php } ?>
                                                <?php $cd++; ?>
                                            <?php } ?>
                                        <?php } ?>
                                        </div>
                                    </div>
                                    <?php $co++; ?>
                                    <?php } ?>
                                <?php } ?>
                                </div>

                                <div class="clear"></div>
                            </div>
                            <?php $rn++; ?>
                        <?php } ?>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
                <?php $line++; ?>
                <?php } ?>
            <?php } ?>
        </div>
    </section>
    <?php } ?>
<?php } ?>
