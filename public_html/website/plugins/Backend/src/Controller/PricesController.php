<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class PricesController extends AppController {

    public function update($model, $code, $category, $season = false, $related = false) {

        // settings
        if(Configure::read($model) && array_key_exists($code, Configure::read($model)) && Configure::read($model . '.' . $code . '.active') ){
            $settings = Configure::read($model . '.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));

            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }

        // check category / prices "needed"?
        $categories = $this->getCategories($model, $code);
        if(!is_array($categories) || !array_key_exists($category, $categories) || !array_key_exists('prices', $settings) || !is_array($settings['prices'])){

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));

            // redirect
            return $this->redirect(['controller' => $model, 'action' => 'index', $code]);

        }

        // check seasons
        if(!array_key_exists('prices', $settings) || !is_array($settings['prices']) || !array_key_exists('seasons', $settings['prices']) || !is_array($settings['prices']['seasons']) || !array_key_exists('active', $settings['prices']['seasons']) || $settings['prices']['seasons']['active'] !== true){
            $season = false;
            $related_seasons = false;
            $seasons = [];
        }else{

            $related_seasons = array_key_exists('rel', $settings['prices']['seasons']) && is_string($settings['prices']['seasons']['rel']) ? $settings['prices']['seasons']['rel'] : false;
            $seasons = $this->getSeasons($related_seasons ? $related_seasons : $code);
            if(count($seasons) < 1){

                // error
                if($related_seasons){
                    $this->Flash->error(__d('be', 'You need to create a related season first!'));
                }else{
                    $this->Flash->error(__d('be', 'You need to create a season first!'));
                }

                // redirect
                return $this->redirect(['controller' => 'seasons', 'action' => 'update', $model, $related_seasons ? $related_seasons : $code, $category, 'false', $related]);

            }else{
                $season = $season && array_key_exists($season, $seasons) ? $season : key($seasons);
            }
        }

        // fetch items
        $modelTable = TableRegistry::get('Backend.' . ucfirst($model));
        if($related){
            $items = $modelTable->find('all')->where(['code' => $code, 'category_id' => $category, 'id' => $related])->order(['sort' => 'ASC', 'internal' => 'ASC'])->toArray();
        }else{
            $items = $modelTable->find('all')->where(['code' => $code, 'category_id' => $category])->order(['sort' => 'ASC', 'internal' => 'ASC'])->toArray();
        }
        $this->set('items', $items);

        // fetch prices
        $prices = $this->Prices->interlace($this->Prices->find('all')->where(['Prices.foreign_model' => $model, 'Prices.foreign_code' => $code, 'Prices.season_id' => $season])->toArray());
        $this->set('prices', $prices);

        // fetch drafts
        $drafts = $order = [];
        $draftTable = TableRegistry::get('Backend.Drafts');
        foreach($draftTable->find('all')->select(['id','internal','sort'])->where(['Drafts.model' => $model, 'Drafts.code' => $code])->order(['internal' => 'ASC'])->toArray() as $d){
            $drafts[$d->id] = $d->internal;
            $order[$d->id] = $d->sort;
        }
        asort($order);
        $draft = count($drafts) > 0 ? key($drafts) : false;
        $this->set('drafts', $drafts);
        $this->set('draft', $draft);
        $this->set('order', $order);

		//fetch flags
		$flags = $settings['prices']['flags'];
        $flag = count($flags) > 0 ? key($flags) : false;
        $this->set('flags', $flags);
        $this->set('flag', $flag);

        // options
        $options = array_key_exists('drafts', $settings['prices']) && is_array($settings['prices']['drafts']) && array_key_exists('options', $settings['prices']['drafts']) && is_array($settings['prices']['drafts']['options']) ? $settings['prices']['drafts']['options'] : false;
        $this->set('options', $options);

        // elements
        $elements = false;
        if(array_key_exists('elements', $settings['prices']) && $settings['prices']['elements'] !== false){

            // check "element"
            if(!Configure::read('elements.' . $settings['prices']['elements'])){

                // error
                $this->Flash->error(__d('be', 'Invalid request!'));

                // redirect
                return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

            }

            $elementTable = TableRegistry::get('Backend.Elements');
            $elements = $elementTable->find('list')->where(['code' => $settings['prices']['elements']])->order(['sort' => 'ASC'])->toArray();
            if(count($elements) == 0){

                // error
                $this->Flash->error(__d('be', '%s are needed in order to set prices!', Configure::read('elements.' . $settings['prices']['elements'] . '.translations.menu')));

                // redirect
                return $this->redirect(['controller' => 'elements', 'action' => 'index', $settings['prices']['elements']]);

            }

        }
        $this->set('elements', $elements);

        // save
        if ($this->request->is(['post', 'put'])) {

            // ids
            if($related){
                $_ids = [['id' => $related]];
            }else{
                $_ids = $this->connection->execute("SELECT `id` FROM `" . $model . "` WHERE `category_id` = :category", ['category' => $category])->fetchAll('assoc');
            }

            if(is_array($_ids) && count($_ids) > 0){

                // ids
                $ids = [];
                foreach($_ids as $_i){
                    $ids[] = $_i['id'];
                }

                // cleanup
                if($season){
                    $this->connection->execute("DELETE FROM `prices` WHERE `foreign_id` IN ('" . join("','", $ids) . "') AND `foreign_model` = :model AND `foreign_code` = :code AND `season_id` = :season", ['model' => $model, 'code' => $code, 'season' => $season]);
                }else{
                    $this->connection->execute("DELETE FROM `prices` WHERE `foreign_id` IN ('" . join("','", $ids) . "') AND `foreign_model` = :model AND `foreign_code` = :code", ['model' => $model, 'code' => $code]);
                }

                // save
                if(array_key_exists('prices', $this->request->data) && is_array($this->request->data['prices']) && count($this->request->data['prices']) > 0){
                    foreach($this->request->data['prices'] as $item => $options){
                        foreach($options as $option => $elements){
                            foreach($elements as $element => $drafts){
                                foreach($drafts as $draft => $flags){
                                    foreach($flags as $flag => $input){
                                        $this->connection->execute("INSERT INTO `prices` (`id`, `foreign_id`, `foreign_model`, `foreign_code`, `season_id`, `price_draft_id`, `option`, `element`, `value`, `flag`) VALUES (:id, :foreign_id, :foreign_model, :foreign_code, :season, :draft, :option, :element, :value, :flag)", [
                                            'id' => Text::uuid(),
                                            'foreign_id' => $item,
                                            'foreign_model' => $model,
                                            'foreign_code' => $code,
                                            'season' => $season,
                                            'draft' => $draft,
                                            'option' => is_array($options) ? $option : '',
                                            'element' => is_array($elements) ? $element : '',
                                            'value' => str_replace(',', '.', $input['value']),
                                            'flag' => $flag,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                $this->Flash->success(__d('be', 'The prices have been successfully saved.'));

                if($this->request->params['redirect']){
                    return $this->redirect(['controller' => $model, 'action' => 'index', $code, $category]);
                }else{
                    return $this->redirect(['action' => 'update', $model, $code, $category, $season ? $season : 'false', $related]);
                }

            }else{

                // error
                $this->Flash->error(__d('be', 'No elements found, to save prices for!'));

                // redirect
                return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category]);

            }
        }

        // selector
        $selector = ['find' => '', 'delete' => ''];
        if(is_array($options) && is_array($elements)){
            $selector['find'] = ' > fieldset > fieldset';
            $selector['delete'] = '[data-item="\' + item + \'"] > fieldset[data-option="\' + option + \'"] > fieldset[data-element="\' + element + \'"]';
        }else if(is_array($options)){
            $selector['find'] = ' > fieldset';
            $selector['delete'] = '[data-item="\' + item + \'"] > fieldset[data-option="\' + option + \'"]';
        }else if(is_array($elements)){
            $selector['find'] = ' > fieldset';
            $selector['delete'] = '[data-item="\' + item + \'"] > fieldset[data-element="\' + element + \'"]';
        }
        $this->set('selector', $selector);

        // menu
        $menu = [
            'left' => [
                [
                    'show' => $related === false ? true : false,
                    'type' => 'select',
                    'name' => 'price_category',
                    'attr' => [
                        'options' => $categories,
                        'class' => 'dropdown',
                        'default' => $category,
                        'escape' => false,
                    ],
                ],
                [
                    'show' => $seasons ? true : false,
                    'type' => 'select',
                    'name' => 'price_season',
                    'attr' => [
                        'options' => $seasons,
                        'class' => 'dropdown',
                        'default' => $season,
                    ],
                ],
                [
                    'show' => $seasons && $related_seasons === false ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Edit season'),
                    'url' => ['controller' => 'seasons', 'action' => 'update', $model, $code, $category, $season, $related],
                    'icon' => 'pencil',
                ],
                [
                    'show' => $seasons && $related_seasons === false ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Delete season'),
                    'url' => ['controller' => 'seasons', 'action' => 'delete', $model, $code, $category, $season, $related],
                    'icon' => 'trash',
                    'confirm' => __d('be', 'Do you realy want to delete this season with all containing prices?'),
                ],
                [
                    'show' => $seasons && $related_seasons === false ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Add new season'),
                    'url' => ['controller' => 'seasons', 'action' => 'update', $model, $code, $category, 'false', $related],
                    'icon' => 'plus',
                ],
            ],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => $model, 'action' => 'index', $code, $category],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];

        if($related){
            $add = count($items) > 0 ? ' <span>' . $items[0]['internal'] . '</span>' : '';
            $this->set('title', __d('be', 'Prices') . $add);
        }else{
            $this->set('title', __d('be', 'Prices') . ' <span>' . $settings['translations']['menu'] . '</span>');
        }
        $this->set('menu', $menu);
        $this->set('related', $related);
        $this->set('code', $code);
        $this->set('model', $model);
        $this->set('category', $category);
        $this->set('season', $season);
        $this->set('settings', $settings);

    }

}
