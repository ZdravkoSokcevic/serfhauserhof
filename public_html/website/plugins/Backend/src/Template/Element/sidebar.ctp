<?php use Cake\Core\Configure; ?>
<aside>
    <div>
        <span class="logo"><a href="#"><?= __d('be', 'Backend'); ?></a></span>
        <div class="profile"><?= __d('be', 'Hello'); ?> <a href="<?php echo $this->Url->build(['controller' => 'users', 'action' => 'edit']); ?>" title="<?= __d('be', 'Edit login information'); ?>"><?= $this->request->session()->read('Auth.User.firstname'); ?></a>! | <a href="<?php echo $this->Url->build(['controller' => 'users', 'action' => 'logout']); ?>" title="<?= __d('be', 'Logout'); ?>"><?= __d('be', 'Logout'); ?></a></div>
        <?php $auth = $this->request->session()->read('Auth'); ?>
        <ul>
            <?php foreach(Configure::read('navigation') as $menu){ ?>
                <?php
                    $name = $url = $active = false;
                    $sub = 0;
                    foreach($menu['elements'] as $pos => $element){
                        if(array_key_exists('elements', $element)){
                            foreach(Configure::read('elements') as $code => $info){
                                $_url = ['controller' => 'elements', 'action' => 'index', $code];
                                if($info['active'] && $info['show'] === true && __cp($_url, $auth)){
                                    
                                    if($url === false){
                                        $name = $info['translations']['menu'];
                                        $url = $_url;
                                    }
                                    
                                    // active?
                                    if(strtolower($this->request->params['controller']) == 'elements' && in_array(strtolower($this->request->params['action']), ['index', 'update']) && count($this->request->params['pass']) > 0 && strtolower($this->request->params['pass'][0]) == strtolower($code)){
                                        $active = $code;
                                    }else if(strtolower($this->request->params['controller']) == 'categories' && in_array(strtolower($this->request->params['action']), ['update']) && count($this->request->params['pass']) > 1 && strtolower($this->request->params['pass'][0]) == 'elements' && strtolower($this->request->params['pass'][1]) == strtolower($code)){
                                        $active = $code;
                                    }
                                    $sub++;
                                }
                            }
                        }else if($element['show'] === true && __cp($element['url'], $auth)){
                            if($url === false){
                                $name = $element['name'];
                                $url = $element['url'];
                            }

                            // active?
                            if(is_array($element['url'])){
                                if(strtolower($element['url']['controller']) == strtolower($this->request->params['controller']) && strtolower($element['url']['action']) == strtolower($this->request->params['action'])){
                                    if(count($this->request->params['pass']) > 0){
                                        $check = true;
                                        foreach($element['url'] as $k => $v){
                                            if(is_int($k)){
                                                if(!array_key_exists($k, $this->request->params['pass']) || $this->request->params['pass'][$k] != $v){
                                                    $check = false;
                                                }
                                            }
                                        }
                                        $active = $check === true ? $pos : $active;
                                    }else{
                                        $active = $pos;
                                    }
                                }else if(is_array($element['active']) && count($element['active']) > 0){
                                    foreach($element['active'] as $e){
                                        if(strtolower($e['controller']) == strtolower($this->request->params['controller']) && strtolower($e['action']) == strtolower($this->request->params['action'])){
                                            if(count($e) > 2){ // go deeper ;-)
                                                $check = true;
                                                foreach($e as $_k => $_v){
                                                    if(is_int($_k)){
                                                        if(!array_key_exists($_k, $this->request->params['pass']) || strtolower($_v) != strtolower($this->request->params['pass'][$_k])){
                                                            $check = false;
                                                        }
                                                    }
                                                }
                                                if($check){
                                                    $active = $pos;
                                                }
                                            }else{
                                                $active = $pos;
                                            }
                                        }
                                    }
                                }
                            }

                            $sub++;
                        }
                    }

                    // target
                    $target = array_key_exists('options',$menu) && is_array($menu['options']) && array_key_exists('target', $menu['options']) ? $menu['options']['target'] : '_self';

                ?>
                <?php if($menu['show'] == true && $url && __cp($url, $auth)){ ?>
                <li>
                    <a href="<?= $this->Url->build($url); ?>" target="" class="<?= $sub > 1 ? '' : ' no-submenu'; ?><?= $active !== false ? ' current' : ''; ?>"><?php echo $sub > 1 ? $menu['name'] : $name; ?></a>
                    <?php if($sub > 1){ ?>
                        <ul>
                            <?php foreach($menu['elements'] as $pos => $element){ ?>
                                <?php if(array_key_exists('elements', $element)){ ?>
                                    <?php foreach(Configure::read('elements') as $code => $info){ ?>
                                        <?php $_url = ['controller' => 'elements', 'action' => 'index', $code]; ?>
                                        <?php if($info['active'] && $info['show'] == true && __cp($_url, $auth)){ ?>
                                            <li><a href="<?= $this->Url->build($_url); ?>" target="" class="<?= $code === $active ? 'current' : ''; ?>"><?= $info['translations']['menu']; ?></a></li>
                                        <?php } ?>
                                    <?php } ?>
                                <?php }else if($element['show'] && __cp($element['url'], $auth)){ ?>
                                    <?php
                                    
                                        // target
                                        $target = array_key_exists('options',$element) && is_array($element['options']) && array_key_exists('target', $element['options']) ? $element['options']['target'] : '_self';

                                    ?>
                                    <li><a href="<?= $this->Url->build($element['url']); ?>" target="" class="<?= $pos === $active ? 'current' : ''; ?>"><?= $element['name']; ?></a></li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</aside>
<script>
    
    $(document).ready(function(){
    
        $("aside > div > ul > li > ul").hide();
        $("aside > div > ul > li > ul > li > a.current").parents("ul:first").show();
    
        $("aside > div > ul > li > a").click(function () {
            if(!$(this).hasClass('no-submenu')){
                $(this).parent().siblings().find("ul").slideUp("normal");
                $(this).next().slideToggle("normal");
                return false;
            }
        });
    
        $("aside > div > ul > li > a").hover(
            function () {
                $(this).stop().animate({ paddingRight: "25px" }, 200);
            },
            function () {
                $(this).stop().animate({ paddingRight: "15px" });
            }
        );
    
    });
    
</script>