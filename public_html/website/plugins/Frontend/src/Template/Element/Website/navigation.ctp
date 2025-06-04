<?php
    use Cake\Core\Configure;
    use Cake\Datasource\ConnectionManager;
?>
<div class="menu">
    <div class="logo">
        <a itemscope itemtype="http://schema.org/Organization" itemref="organisation" href="<?= $this->Url->build(['node' => Configure::read('config.default.home.0.org'), 'language' => $this->request->params['language']]); ?>">
            <img itemprop="logo" src="/frontend/img/logo.svg" alt="<?= Configure::read('config.default.hotel') ?>" />
        </a>
    </div>
    <div class="right">
        <div class="top">
            <a class="phone" href="tel:<?= Configure::read('config.default.phone-plain'); ?>"><span><?= __d('fe', 'Booking hotline'); ?></span><i class="fa fa-phone" aria-hidden="true"></i><?= Configure::read('config.default.phone'); ?></a>
            <span class="misc"><a href="<?= $this->Url->build(['node' => Configure::read('config.default.arrival.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Arrival'); ?></a> | <a href="<?= $this->Url->build(['node' => Configure::read('config.default.contact.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Contact'); ?></a> | <a href="<?= $this->Url->build(['node' => Configure::read('config.default.brochures.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Brochures'); ?></a></span>
            <span class="languages">
                <span class="sep"> | </span>
                <a href="#"><span><?= $this->request->params['language']; ?></span></a>
                <ul class="options">
                    <?php foreach(Configure::read('translations') as $k => $v){ ?>
                        <?php if($v['active'] && $v['released'] && $this->request->params['language'] != $k){ ?>
                            <?php
                            $connection = ConnectionManager::get('default');
                            $url = '/redirect/' . $k . '/' . $this->request->params['node']['route'];
                            $node = $connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `route` = :route", ['route' => $this->request->params['node']['route']])->fetch('assoc');
                            if(is_array($node) && count($node) > 0){
                                $lang_check = $connection->execute("SELECT `content` FROM `i18n` WHERE `locale`='".$k."' AND `foreign_key`='".$node['foreign_id']."'")->fetch('assoc');
                                if(!is_array($lang_check) || !isset($lang_check['content'])){
                                    $url = '/redirect/' . $k . '/' . Configure::read('redirects.'.$k.'.default');//Configure::read('config.default.home.0.details.node.route'); 
                                }
                            } ?>
                            <li><a href="<?= $url ?>" class="<?= $k; ?>"><span><?= $v['title']; ?></span><i class="flag-icon flag-icon-<?= $k ?>"></i></a></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </span>
            <span class="request-n-book"><a href="<?= $this->Url->build(['node' => Configure::read('config.default.request.0.org'), 'language' => $this->request->params['language']]); ?>" class="request button"><?= __d('fe', 'request'); ?></a><a href="<?= $this->Url->build(['node' => Configure::read('config.default.book.0.org'), 'language' => $this->request->params['language']]); ?>" class="book button dark"><?= __d('fe', 'booking'); ?></a></span>
        </div>
        <div class="bottom">
            <nav class="menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
                <ul class="menu">
                    <?php foreach($menu as $idx1 => $lvl1){ ?>
                        <?php if($idx1 > 0){ ?>
                            <li class="<?php echo 'pos-' . $idx1 . ' children-' . count($lvl1['children']); ?><?php echo $lvl1['highlight'] || $lvl1['active'] ? ' active open' : ''; ?>">
                                <?php if($lvl1['linkable']){ ?>
                                <a href="<?= $this->Url->build(['node' => 'node:' . $lvl1['id'], 'language' => $this->request->params['language']]); ?>" itemprop="url"><?= $lvl1['content']; ?><i class="fa fa-angle-double-down" aria-hidden="true"></i><span class="clear"></span></a>
                                <?php }else if($lvl1['type'] == 'link'){ ?>
                                <a href="<?= $lvl1['details']['link']; ?>" target="<?= $lvl1['details']['target']; ?>"><?= $lvl1['content']; ?></a>
                                <?php }else{ ?>
                                <span class="fake" style="color:unset" style="color:unset"><?= $lvl1['content']; ?><i class="fa fa-angle-double-down" aria-hidden="true"></i><span class="clear"></span></span>
                                <?php } ?>
                                <?php if(count($lvl1['children']) > 0){ ?>
                                <ul class="level-2">
                                    <?php foreach($lvl1['children'] as $idx2 => $lvl2){ ?>
                                        <li class="<?php echo 'children-' . count($lvl2['children']); ?><?php echo $idx2 == 0 ? 'first' : ''; ?><?php echo $lvl2['active'] ? ' active' : ''; ?>">
                                            <?php if($lvl2['linkable']){ ?>
                                            <a href="<?= $this->Url->build(['node' => 'node:' . $lvl2['id'], 'language' => $this->request->params['language']]); ?>"><?= $lvl2['content']; ?></a><i class="fa fa-angle-right" aria-hidden="true"></i><span class="clear"></span>
                                            <?php }else if($lvl2['type'] == 'link'){ ?>
                                            <a href="<?= $lvl2['details']['link']; ?>" target="<?= $lvl2['details']['target']; ?>"><?= $lvl2['content']; ?></a>
                                            <?php }else{ ?>
                                            <span class="fake" style="color:unset" style="color:unset"><?= $lvl2['content']; ?></span><i class="fa fa-angle-down" aria-hidden="true"></i><span class="clear"></span>
                                            <?php } ?>
                                            <?php if(count($lvl2['children']) > 0){ ?>
                                            <ul class="level-3">
                                                <?php foreach($lvl2['children'] as $idx3 => $lvl3){ ?>
                                                    <li class="<?php echo $lvl3 == 0 ? 'first' : ''; ?><?php echo $lvl3['active'] ? ' active' : ''; ?>">
                                                        <?php if($lvl3['linkable']){ ?>
                                                        <a href="<?= $this->Url->build(['node' => 'node:' . $lvl3['id'], 'language' => $this->request->params['language']]); ?>"><?= $lvl3['content']; ?></a>
                                                        <?php }else if($lvl3['type'] == 'link'){ ?>
                                                        <a href="<?= $lvl3['details']['link']; ?>" target="<?= $lvl3['details']['target']; ?>"><?= $lvl3['content']; ?></a>
                                                        <?php }else{ ?>
                                                        <span class="fake" style="color:unset" style="color:unset"><?= $lvl3['content']; ?></span>
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <li class="search children-0">
                        <form action="<?= $this->Url->build(['node' => Configure::read('config.default.search.0.org'), 'language' => $this->request->params['language']]); ?>" method="GET">
                            <input type="text" name="s" value="" placeholder="" />
                            <button><i class="fa fa-search" aria-hidden="true"></i></button>
                        </form>
                    </li>
                </ul>
            </nav>
            <div class="buttons">
                <a href="<?= $this->Url->build(['node' => Configure::read('config.default.request.0.org'), 'language' => $this->request->params['language']]); ?>" class="button uc two-rows"><span><?= __d('fe', 'Non-binding'); ?></span><span><?= __d('fe', 'request'); ?></span></a>
                <a href="<?= $this->Url->build(['node' => Configure::read('config.default.book.0.org'), 'language' => $this->request->params['language']]); ?>" class="button uc two-rows dark"><span><?= __d('fe', 'Best price'); ?></span><span><?= __d('fe', 'booking'); ?></span></a>
                <div class="button grey angular nav-trigger-wrapper">
                    <div class="nav-trigger">
                        <i></i><i></i><i></i>
                    </div>
                    <div class="nav-label"><?= __d('fe', 'Menu'); ?></div>
                </div>
            </div>
        </div>    
    </div>
</div>
<a class="button rgba jump" href="#header"><i class="fa fa-chevron-up"></i></a>