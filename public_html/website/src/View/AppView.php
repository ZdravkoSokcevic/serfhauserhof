<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link https://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize()
    {
        
        if($this->plugin == 'Backend'){
            
            // paginator
            $this->loadHelper('Paginator');
            
            // form helper templates
            $templates = [
                'formStart' => '<form autocomplete="off" novalidate="novalidate"{{attrs}}>',
                'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}<div class="clear"></div>{{help}}</div>',
                'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}<div class="clear"></div>{{help}}{{error}}</div>',
            ];
            $this->Form->templates($templates);
        
        }else if($this->plugin == 'Frontend'){

            // form helper templates
            $templates = [
                'label' => '<label{{attrs}}>{{text}}<span class="asterisks">*</span></label>',
                'formStart' => '<form autocomplete="off" novalidate="novalidate"{{attrs}}>',
                'inputContainer' => '<div class="input {{cc}} {{type}}{{required}}">{{content}}<div class="clear"></div></div>',
                'inputContainerError' => '<div class="input {{cc}} {{type}}{{required}} error">{{content}}<div class="clear"></div>{{error}}<div class="clear"></div></div>',
            ];
            $this->CustomForm->templates($templates);

        }
        
    }
}
