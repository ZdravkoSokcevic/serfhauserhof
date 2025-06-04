<?php use Cake\Core\Configure; ?>
<?php

    $_headers = [];
    if(array_key_exists('media', $content) && is_array($content['media']) && array_key_exists('header-' . Configure::read('config.default.season'), $content['media']) && is_array($content['media']['header-' . Configure::read('config.default.season')])){
        foreach($content['media']['header-' . Configure::read('config.default.season')] as $h){
            if(is_array($h) && array_key_exists('type', $h) && in_array($h['type'], ['image','element']) && is_array($h['details'])){
                if($h['type'] == 'image'){
                    $_headers[] = [
                        'type' => 'image',
                        'url' => $h['details']['seo'][1],
                        'focus' => $h['details']['focus'][1],
                        'alt' => $h['details']['title'],
                        'line1' => '',
                        'line2' => '',
                    ];
                }else if($h['type'] == 'element' && array_key_exists('code', $h['details']) && $h['details']['code'] == 'header-teaser'){
                    if(array_key_exists('image', $h['details']) && is_array($h['details']['image']) && array_key_exists(0, $h['details']['image']) && is_array($h['details']['image'][0]) && count($h['details']['image'][0]) > 0){
                        $_headers[] = [
                            'type' => 'teaser',
                            'url' => $h['details']['image'][0]['details']['seo'][1],
                            'focus' => $h['details']['image'][0]['details']['focus'][1],
                            'alt' => $h['details']['image'][0]['details']['title'],
                            'line1' => $h['details']['line1'],
                            'line2' => $h['details']['line2'],
                        ];
                    }
                }
            }
        }
    }
    
    //set default header
	if(count($_headers) <= 0 && file_exists(WWW_ROOT . '/frontend/img/default-header-' . Configure::read('config.default.season') . '.jpg')){
		$_headers[] = array(
			'type' => 'image',
			'url' => '/frontend/img/default-header-' . Configure::read('config.default.season') . '.jpg',
			'focus' => array(
				'x' => 50,
                'y' => 50,
                'css' => '50% 50%',
			),
			'alt' => __d('fe','Header image'),
            'line1' => '',
            'line2' => '',
		);
	}

?>
<?php if(count($_headers) > 0){ ?>
<header class="images images-<?= count($_headers) ?> hidden-print">
    <ul class="viewport bxslider">
        <?php foreach($_headers as $header){ ?>
            <li>
                <div class="bxslide" style="background-position: <?= $header['focus']['css']; ?>; background-image: url('<?= $header['url']; ?>');" data-type="<?= $header['type']; ?>" data-line-1="<?= $header['line1']; ?>" data-line-2="<?= $header['line2']; ?>"></div>
            </li>
        <?php } ?>
    </ul>
    <div class="header-text" style="<?php echo $_headers[0]['type'] == 'teaser' ? '' : 'opacity: 0;'; ?>">
        <div class="line-1"><?=  $_headers[0]['line1']; ?></div>
        <div class="line-2"><?=  $_headers[0]['line2']; ?></div>
        <span class="line"></span>
    </div>

    <?php
        $news = Configure::read('config.news');
        $image = $news['image'][0]['details']; //  Configure::read('config.news.image.0.details');
    ?>    
    <?php if($news['active']){ ?>
    <section class="desktop-news news<?php echo $news['open'] ? ' open' : ''; ?>">
        <a href="#" class="arrow"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        <a href="#" class="button rgba star"><i class="fa fa-star-o" aria-hidden="true"></i><span><?= __d('fe', 'News'); ?></span></a>
        <div class="hidden news-content">
            <h2><?= $news['headline-' . $this->request->params['language']]; ?></h2>
            <div class="img" style="background-image: url(<?= $image['seo'][4]; ?>); background-position: <?= $image['focus'][4]['css']; ?>;"></div>
            <p><?= $news['content-' . $this->request->params['language']]; ?></p>
            <?php if(is_array($news['link'])){ ?>
            <a href="<?= $this->Url->build(['node' => $news['link'][0]['org'], 'language' => $this->request->params['language']]); ?>" class="button"><?php echo !empty($news['linktext-' . $this->request->params['language']]) ? $news['linktext-' . $this->request->params['language']] : __d('fe', 'more'); ?></a>
            <?php } ?>
        </div>
    </section>
    <span class="news-border"></span>
    <?php } ?>
</header>
<?php if($news['active']){ ?>
    <section id="mobile-news" style="display:none; padding:20px;" class="mobile-news news<?php echo $news['open'] || $this->request->params['route'] == Configure::read('config.default.home.0.details.node.route') ? ' open' : ''; ?>">
        <div class="">
            <h2 style="text-align: center;padding: 20px;"><?= $news['headline-' . $this->request->params['language']]; ?></h2>
            <div class="img" style="background-image: url(<?= $image['seo'][1]; ?>); height: 210px; background-position: <?= $image['focus'][4]['css']; ?>;"></div>
            <p><?= $news['content-' . $this->request->params['language']]; ?></p>
            <?php if(is_array($news['link'])){ ?>
            <a href="<?= $this->Url->build(['node' => $news['link'][0]['org'], 'language' => $this->request->params['language']]); ?>" class="button"><?php echo !empty($news['linktext-' . $this->request->params['language']]) ? $news['linktext-' . $this->request->params['language']] : __d('fe', 'more'); ?></a>
            <?php } ?>
        </div>
    </section>
<?php } ?>
<?php } ?>