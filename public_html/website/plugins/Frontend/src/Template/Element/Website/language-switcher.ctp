<?php
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
$connection = ConnectionManager::get('default');
?>
<div class="languages hidden-print">
    <span><i class="fa fa-angle-down" aria-hidden="true"></i>&nbsp;<span><?= Configure::read('languages.' . $this->request->params['language'] . '.title'); ?></span></span>
    <div class="options">
        <ul>
            <?php foreach(Configure::read('languages') as $k => $v){ ?>
                <?php if($v['active'] && $v['released'] && $this->request->params['language'] != $k){ ?>
                	
                    <?php
                    $url = '/redirect/' . $k . '/' . $this->request->params['node']['route'];
                    $node = $connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `route` = :route", ['route' => $this->request->params['node']['route']])->fetch('assoc');
                    if(is_array($node) && count($node) > 0){
	                	$lang_check = $connection->execute("SELECT `content` FROM `i18n` WHERE `locale`='".$k."' AND `foreign_key`='".$node['foreign_id']."'")->fetch('assoc');
						if(!is_array($lang_check) || !isset($lang_check['content'])){
	                        $url = '/redirect/' . $k . '/' . Configure::read('redirects.'.$k.'.default');//Configure::read('config.default.home.0.details.node.route'); 
						}
					} ?>
                    <li><a href="<?= $url ?>" class="<?= $k; ?>"><?= $v['title']; ?></a></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</div>