<?php
    use Cake\Core\Configure;
    use Cake\Datasource\ConnectionManager;
?>
<section class="quicklinks">

    <!-- language -->
    <span class="quicklink languages">
        <i class="fa fa-globe" aria-hidden="true"></i>
        <span class="label"><?= __d('fe', 'Languages'); ?></span>
        <span class="info">
            <ul>
            <?php foreach(Configure::read('translations') as $k => $v){ ?>
                <?php if($v['active'] && $this->request->params['language'] != $k){ ?>
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
    </span>

    <!-- search -->
    <span class="quicklink search">
        <i class="fa fa-search" aria-hidden="true"></i>
        <span class="label"><?= __d('fe', 'Search'); ?></span>
        <span class="info">
            <form action="<?= $this->Url->build(['node' => Configure::read('config.default.search.0.org'), 'language' => $this->request->params['language']]); ?>" method="GET">
                <input type="text" name="s" value="" placeholder="" />
                <button><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
        </span>
    </span>

    <!-- tour -->
    <a class="quicklink tour" href="<?= $this->Url->build(['node' => Configure::read('config.default.tours.0.org'), 'language' => $this->request->params['language']]); ?>">
        <i aria-hidden="true">360°</i>
        <span class="label"><?= __d('fe', 'Tour'); ?></span>
        <span class="info">
            <span class="line-1"><?= __d('fe', 'Virtual journey'); ?></span>
            <span class="line-2"><?= __d('fe', 'Take a look around!'); ?></span>
        </span>
    </a>

    <!-- videos -->
    <a class="quicklink videos" href="<?= $this->Url->build(['node' => Configure::read('config.default.videos.0.org'), 'language' => $this->request->params['language']]); ?>">
        <i class="fa fa-play-circle-o" aria-hidden="true"></i>
        <span class="label"><?= __d('fe', 'Videos'); ?></span>
        <span class="info">
            <span class="line-1"><?= __d('fe', 'Videos to dream'); ?></span>
            <span class="line-2"><?= __d('fe', 'Seductively beautiful ...'); ?></span>
        </span>
    </a>

    <!-- weather -->
    <a class="quicklink weather hidden" href="<?= $this->Url->build(['node' => Configure::read('config.default.weather.0.org'), 'language' => $this->request->params['language']]); ?>">
        <i aria-hidden="true">H</i>
        <span class="label"><?= __d('fe', 'Weather'); ?></span>
        <span class="info">
            <span class="line-1"><?= __d('fe', 'Weather forecast'); ?></span>
            <span class="line-2"><?= __d('fe', 'for Serfaus'); ?></span>
        </span>
    </a>

    <!-- slideshow -->
    <a class="quicklink slideshow" href="javascript:slideshow(false);">
        <i class="fa fa-picture-o" aria-hidden="true"></i>
        <span class="label"><?= __d('fe', 'Insights'); ?></span>
        <span class="info">
            <span class="line-1"><?= __d('fe', 'Image'); ?></span>
            <span class="line-2"><?= __d('fe', 'Gallery'); ?></span>
        </span>
    </a>

    <!-- backlink -->
    <a class="quicklink back hidden" href="javascript:backlink();">
        <i class="fa fa-chevron-left" aria-hidden="true"></i>
        <span class="label"><?= __d('fe', 'Back'); ?></span>
        <span class="info">
            <span class="line-1"><?= __d('fe', 'Previous'); ?></span>
            <span class="line-2"><?= __d('fe', 'Page'); ?></span>
        </span>
    </a>

</section>
