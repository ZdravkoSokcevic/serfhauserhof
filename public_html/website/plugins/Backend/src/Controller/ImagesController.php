<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;

class ImagesController extends AppController {

    public function index($category = false, $term = false) {
        
        // init
        $use_categories = Configure::read('images.use_categories');
        $editable_categories = false;
        $imagick_check = class_exists('Imagick') ? true : false;
        
        // categories
        $categories = [];
        if($use_categories === true){
            $categories = $this->getCategories('images', Configure::read('categories.code'));
            if(count($categories) < 1){
                
                // error
                $this->Flash->error(__d('be', 'You need to create a category in order to upload images!'));
                
                // redirect
                return $this->redirect(['controller' => 'categories', 'action' => 'update', 'images']);
                
            }else{
                
                // category
                $categoriesTable = TableRegistry::get('Backend.Categories');
                $category = $category === false || !array_key_exists($category, $categories) ? key($categories) : $category;
                
                // category infos
                $category = $categoriesTable->get($category);
        
                // editable
                $editable_categories = true;
                
            }
        }else if($use_categories === false){
            $category = ['id' => Configure::read('categories.code')];
        }else if(is_string($use_categories) && array_key_exists($use_categories, Configure::read('elements')) && Configure::read('elements.' . $use_categories . '.active')){
            
            // TODO: maybe make optgroups here ...
            $elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`, `internal`", ['code' => $use_categories])->fetchAll('assoc');

            if(is_array($elements)){
                foreach($elements as $k => $v){
                    $categories[$v['id']] = $v['internal'];
                }
            }

            if(count($categories) < 1){
                
                // error
                $this->Flash->error(__d('be', 'You need to create a related element first!'));
                
                // redirect
                return $this->redirect(['controller' => 'elements', 'action' => 'index', $use_categories]);
                
            }else{

                // category infos
                $category = ['id' => $category === false || !array_key_exists($category, $categories) ? key($categories) : $category];

            }
            
        }else{
            
            // error
            $this->Flash->error(__d('be', 'Invalid categories setting for images!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            
        }
        $this->set('category', $category);
        
        // fetch images
        if($term !== false){
            $images = $this->Images->find('translations')->where(["Images_title_translation.content LIKE '%" . $term . "%' OR original LIKE '%" . $term . "%'"])->order(['Images_title_translation.content' => 'ASC'])->toArray();
        }else{
            $images = $this->Images->find('translations')->where(['category_id' => $category['id']])->order(['Images_title_translation.content' => 'ASC'])->toArray();
        }
        $this->set('images', $images);
        
        // image info
        $info = [];
        foreach($images as $image){
            $info[$image->id] = array_merge(
                $this->Images->information($image),
                [
                    'upload' => filemtime($this->Images->path . $this->Images->original . DS . $image['id'] . '.' . $image['extension']),
                    'purposes' => array_filter(explode(',', $image->purpose)),
                    'category' => count($categories) > 0 ? $categories[$image->category_id] : false,
                ]
            );
        }
        $this->set('info', $info);
        
        // any categories inside this category?
        try {
             $folders = [];
            if($editable_categories){
                $folders = $categoriesTable->find('all')->select(['id', 'internal'])->where(['parent_id' => $category['id']])->order(['internal' => 'ASC'])->toArray();
            }
        } catch (RecordNotFoundException $e) {
            $folders = [];
        }
        $this->set('folders', $folders);
        
        // menu
        $gmove = $editable_categories && count($images) > 0 && count($categories) > 1 && __cp(['controller' => 'images', 'action' => 'group', 'move'], $this->request->session()->read('Auth')) ? true : false;
        $gdel = count($images) > 0 && __cp(['controller' => 'images', 'action' => 'group', 'delete'], $this->request->session()->read('Auth')) ? true : false;
        $gcrop = count($images) > 0 && __cp(['controller' => 'images', 'action' => 'auto'], $this->request->session()->read('Auth')) ? true : false;
        $menu = [
            'left' => [
                [
                    'show' => count($categories) > 0 ? true : false,
                    'type' => 'select',
                    'name' => 'image_category',
                    'attr' => [
                        'options' => $categories,
                        'class' => 'dropdown',
                        'default' => $category['id'],
                        'escape' => false,
                    ],
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'update', 'images', Configure::read('categories.code'), $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Edit category'),
                    'url' => ['controller' => 'categories', 'action' => 'update', 'images', Configure::read('categories.code'), $category['id']],
                    'icon' => 'pencil',
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'delete', 'images', Configure::read('categories.code'), $category['id']], $this->request->session()->read('Auth'))? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Delete category'),
                    'url' => ['controller' => 'categories', 'action' => 'delete', 'images', Configure::read('categories.code'), $category['id']],
                    'icon' => 'trash',
                    'confirm' => __d('be', 'Do you realy want to delete this category with all containing subcategories and images?'),
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'update', 'images', Configure::read('categories.code'), $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Create new category'),
                    'url' => ['controller' => 'categories', 'action' => 'update', 'images', Configure::read('categories.code')],
                    'icon' => 'plus',
                ],
            ],
            'right' => [
                [
                    'show' => $imagick_check && $gmove,
                    'type' => 'icon',
                    'text' => __d('be', 'Move selected files'),
                    'url' => ['controller' => 'images', 'action' => 'group', 'move'],
                    'icon' => 'folder-open-o',
                    'class' => 'move group hidden',
                    'action' => 'select',
                    'select' => [
                        'name' => 'move_category',
                        'attr' => [
                            'options' => $categories,
                            'class' => 'dropdown',
                            'escape' => false,
                        ]
                    ]
                ],
                [
                    'show' => $imagick_check && $gdel,
                    'type' => 'icon',
                    'text' => __d('be', 'Delete selected files'),
                    'url' => ['controller' => 'images', 'action' => 'group', 'delete'],
                    'icon' => 'trash',
                    'class' => 'delete group hidden',
                ],
                [
                    'show' => $imagick_check && $gcrop,
                    'type' => 'icon',
                    'text' => __d('be', 'Auto. crop selected files'),
                    'url' => ['controller' => 'images', 'action' => 'auto'],
                    'icon' => 'magic',
                    'class' => 'auto group hidden',
                ],
                [
                    'show' => $imagick_check && ($gmove || $gdel || $gcrop) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Reverse selection'),
                    'url' => ['controller' => 'images', 'action' => 'index', $category['id']],
                    'icon' => 'check',
                    'class' => 'reverse-selection',
                ],
                [
                    'show' => $imagick_check && count($images) > 0 && __cp(['controller' => 'images', 'action' => 'search', $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Search'),
                    'url' => ['controller' => 'images', 'action' => 'search', $category['id']],
                    'icon' => 'search',
                    'class' => 'search',
                    'action' => 'input',
                    'input' => [
                        'name' => 'search_term',
                        'attr' => [
                            'placeholder' => __d('be', 'Search'),
                        ]
                    ]
                ],
                [
                    'show' => $imagick_check && __cp(['controller' => 'images', 'action' => 'upload', $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'upload',
                    'text' => __d('be', 'Add images'),
                    'url' => ['controller' => 'images', 'action' => 'upload', $category['id']],
                    'id' => 'htmluploadzone',
                ],
                [
                    'show' => $imagick_check && __cp(['controller' => 'images', 'action' => 'override'], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'upload',
                    'text' => __d('be', 'Replace images'),
                    'url' => ['controller' => 'images', 'action' => 'override', 'upload'],
                    'id' => 'htmloverridezone',
                ],
            ],
        ];
        
        $this->set('imagick_check', $imagick_check);
        $this->set('title', __d('be', 'Images'));
        $this->set('menu', $menu);

    }

    public function translate($id){
        
        // init
        $this->autoRender = false;
        $ret = ['success' => false, 'id' => $id, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('translation', $this->request->data) && count($this->request->data['translation']) > 0){
                $image = $this->Images->get($id, ['finder' => 'translations']);
                foreach($this->request->data['translation'] as $locale => $translation){
                    if(!empty($translation)){
                        $image->translation($locale)->set(['title' => $translation]);
                        $image->_translations[$locale]->title = $translation;
                    }else{
                        $this->connection->execute("DELETE FROM `i18n` WHERE `foreign_key` = :id AND `locale` = :locale", ['id' => $id, 'locale' => $locale]);
                    }
                }
                if ($result = $this->Images->save($image)) {
                    $ret = ['success' => true, 'msg' => __d('be', 'Translations saved!'), 'id' => $id, 'result' => $this->Images->get($id, ['finder' => 'translations'])];
                }
            }
        }
        
        echo json_encode($ret);
        exit;
    }
    
    public function upload($category){

        // init
        $use_categories = Configure::read('images.use_categories');
        $this->autoRender = false;
        $ret = ['success' => false, 'error' => __d('be', 'Upload failed.')];
        
        // category infos
        if($use_categories === true){
            $categoriesTable = TableRegistry::get('Backend.Categories');
            try {
                $category = $categoriesTable->get($category, ['finder' => 'translations'])->toArray();
            } catch (RecordNotFoundException $e) {
                $category = false;
            }
        }else if($use_categories === false){
            $category = ['id' => Configure::read('categories.code')];
        }else if(is_string($use_categories) && array_key_exists($use_categories, Configure::read('elements')) && Configure::read('elements.' . $use_categories . '.active')){
            $category = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `id` = :id AND `code` = :code ORDER BY `sort`, `internal`", ['id' => $category, 'code' => $use_categories])->fetch('assoc');
            if(!is_array($category) || count($category) < 1){
                $category = false;
            }
        }else{
            $category = false;
        }
        
        // save
        if ($category && $this->request->is('ajax') && $this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('Filedata', $this->request->data) && count($this->request->data['Filedata']) > 0){
                foreach($this->request->data['Filedata'] as $file){
                    
                    // check filetype
                    if(!in_array($file['type'], Configure::read('upload.images.mime'))){
                        $ret = ['success' => false, 'error' => __d('be', 'Invalid image type!'), 'file' => $file];
                    }else{
                        $image = $this->Images->newEntity();
                        $this->Images->patchEntity($image, [
                            'original' => $file['name'],
                            'category_id' => $category['id'],
                            'mime' => $file['type'],
                            'extension' => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
                            'file' => $file
                        ]);
                        
                        foreach(Configure::read('translations') as $locale => $info){
                            if(array_key_exists('_translations', $category) && is_array($category['_translations']) && array_key_exists($locale, $category['_translations'])){
                                $image->translation($locale)->set(['title' => $category['_translations'][$locale]['seo']], ['guard' => false]);
                            }else{
                                $image->translation($locale)->set(['title' => pathinfo($file['name'], PATHINFO_FILENAME)], ['guard' => false]);
                            }
                        }
                        
                        if ($result = $this->Images->save($image)) {
                            
                            // create "auto" sizes
                            $check = true;
                            $filename = $result->id . '.' . $result->extension;
                            foreach(Configure::read('images.sizes.auto') as $folder => $dimension){
                                if($check === true){
                                    if($this->Images->_resize($this->Images->path . $this->Images->original . DS . $filename, $this->Images->path . $folder . DS . $filename, $dimension['width'], $dimension['height']) == false){
                                        $check = false;
                                    }
                                }
                            }
                            if($check){
                                $ret = ['success' => true, 'file' => $file, 'filename' => $filename, 'result' => $result];
                            }else{
                                $ret = ['success' => false, 'error' => [], 'file' => $file];
                                $this->delete($category, $result->id, true);
                            }
                        }else{
                            $ret = ['success' => false, 'error' => [], 'file' => $file];
                        }
                    }
                    
                }
            }
        }
        
        echo json_encode($ret);
        exit;
    }
    
    public function exchange($category){
        
        // init
        $this->autoRender = false;
        $ret = ['success' => false, 'error' => __d('be', 'Upload failed.')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('image_id', $this->request->data) && array_key_exists('Filedata', $this->request->data)){
                
                // init
                $file = $this->request->data['Filedata'];
                
                // check filetype
                if(!in_array($file['type'], Configure::read('upload.images.mime'))){
                    $ret = ['success' => false, 'error' => __d('be', 'Invalid image type!'), 'file' => $file];
                }else{
                    $image = $this->Images->get($this->request->data['image_id']);
                    $this->Images->patchEntity($image, [
                        'original' => $file['name'],
                        'mime' => $file['type'],
                        'extension' => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
                        'file' => $file
                    ]);
                    if ($result = $this->Images->save($image)) {
                        
                        // create "auto" sizes
                        $filename = $result->id . '.' . $result->extension;
                        foreach(Configure::read('images.sizes.auto') as $folder => $dimension){
                            $this->Images->_resize($this->Images->path . $this->Images->original . DS . $filename, $this->Images->path . $folder . DS . $filename, $dimension['width'], $dimension['height']);
                        }
                        
                        $ret = ['success' => true, 'id' => $result->id, 'file' => $file, 'filename' => $filename, 'result' => $result];
                    }
                }
                    
            }
        }
        
        echo json_encode($ret);
        exit;
    }

    public function delete($category, $id, $ajax = false){
    
        // init
        $success = false;
        if($ajax){
            $this->autoRender = false;
        }
        
        // check
        if($used = $this->isUsed($id)){
            $success = false;
            $this->Flash->error(__d('be', "The image could not be deleted because it's in use!") . "<br /><ul><li>" . join("</li><li>", $used) . "</li></ul>");
        }else{
            
            // delte
            try {
                $entity = $this->Images->get($id);
                if($this->Images->delete($entity)){
                    $success = true;
                }
            } catch (RecordNotFoundException $e) { }
    
            // message
            if($ajax === false){
                if($success){
                    $this->Flash->success(__d('be', 'The image has been successfully removed!'));
                }else{
                    $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
                }
            }
        
        }
            
        // redirect
        return $ajax ? $success : $this->redirect(['action' => 'index', $category]);
        
    }

    public function group($action){
        
        // init
        $success = true;
        $this->autoRender = false;
        $ret = ['success' => false, 'action' => $action, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post') && array_key_exists('images', $this->request->data)) {
            if($action == 'move' && array_key_exists('category', $this->request->data)){
                
                // move
                $query = $this->Images->query();
                $success = $query->update()
                    ->set(['category_id' => $this->request->data['category']])
                    ->where(function ($exp, $q) {
                        return $exp->in('id', $this->request->data['images']);
                    })
                    ->execute();
                if($success == true){
                    $this->Flash->success(__d('be', 'The images have been successfully moved!'));
                    $ret = ['success' => true, 'action' => $action];
                }
                
            }else if($action == 'delete'){
                        
                // check
                $used = [];
                $_used = $this->isUsed($this->request->data['images']);
                if(is_array($_used) && count($_used) > 0){
                    foreach($_used as $k => $v){
                        list($c, $i) = explode(":", $k , 2);
                        $used[$i] = true;
                    }
                }
                $ret['used'] = $used;
                
                foreach($this->request->data['images'] as $image){
                    if(!is_array($ret['used']) || !array_key_exists($image, $ret['used'])){
                        if(!$this->delete(false, $image, true)){
                            $success = false;
                        }
                    }
                }
                
                if($success == true){
                    if($ret['used']){
                        $this->Flash->error(__d('be', 'Some images could not be deleted because they are in use!'));
                    }else{
                        $this->Flash->success(__d('be', 'The images have been successfully removed!'));
                    }
                    $ret = ['success' => true, 'action' => $action];
                }
            }
        }
        
        echo json_encode($ret);
        exit;
    }
    
public function revolve($id){
        
        // init
        $rotated = false;
        
        // get image
        try{
            $image = $this->Images->get($id, ['finder' => 'translations'])->toArray();
        }catch(RecordNotFoundException $e){
            $this->Flash->error(__d('be', 'Invalid request!'));
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->request->is('post') && array_key_exists('degrees', $this->request->data)) {
            
            $filename = $id . '.' . $image['extension'];
            $src = $this->Images->path . $this->Images->original . DS . $filename;
            if(file_exists($src)){
                $rotated = $this->Images->_revolve($src, (int) $this->request->data['degrees']);
                if($rotated){
                    foreach(Configure::read('images.sizes.auto') as $folder => $dimension){
                        $this->Images->_resize($src, $this->Images->path . $folder . DS . $filename, $dimension['width'], $dimension['height']);
                    }
                }
            }
        }
        
        // redirect
        if($rotated){
            $this->Flash->success(__d('be', 'The image has been successfully rotated'));
        }else{
            $this->Flash->error(__d('be', 'Image could not be rotated. Please try again!'));
        }
        return $this->redirect(array('action' => 'index', $image['category_id'], '#' => $image['id']));
    }

    public function crop($id, $override = false){

        // init
        $errors = [];

        // get image
        try{
            $image = $this->Images->get($id, ['finder' => 'translations'])->toArray();
            $this->set('image', $image);
        }catch(RecordNotFoundException $e){
            if($override === false){
                $this->Flash->error(__d('be', 'Invalid request!'));
                return $this->redirect(['action' => 'index']);
            }else{
                return false;
            }
        }
        
        // purposes
        $purposes = Configure::read('images.sizes.purposes');
        
        // focus point
        $focus = Configure::read('images.focus');
        $this->set('focusPoint', $focus);
        
        // original
        $original = [
            'url' => $this->Images->url . $this->Images->original . DS . $image['id'] . '.' . $image['extension'],
            'path' => $this->Images->path . $this->Images->original . DS . $image['id'] . '.' . $image['extension'],
        ];
        $this->set('original', $original);

        // size
        $size = getimagesize($original['path']);
        
        // existing
        $existing = array_filter(explode(",",$image['purpose']));

        // crop image
        $data = $override === false ? $this->request->data : $override;
        
        if (($this->request->is('post') || is_array($override)) && array_key_exists('image', $data) && $data['image'] == $id && array_key_exists('crop', $data) && is_array($data['crop'])) {
        
            // init
            $success = 0;

            foreach($data['crop'] as $i){
                if(array_key_exists('crop',$i) && $i['crop'] == 1){
                    
                    // infos
                    $info = $purposes[$i['purpose']];

                    // invalid size?
                    if(($info['width'] > 0 && $info['width'] > $size[0]) || ($info['height'] > 0 && $info['height'] > $size[1])){
                        $errors[$i['purpose']] = __d('be', 'The selected image is to small for this purpose!');
                        continue;
                    }

                    // rotation?
                    $rotation = array_key_exists('deg',$i) && $i['deg'] > 0 && $i['deg'] < 360 ? $i['deg'] : false;
                    
                    // exact fit?
                    $exact = $info['width'] > 0 && $info['height'] > 0 && $info['width'] == $size[0] && $info['height'] == $size[1] ? true : false;
                    
                    // process
                    if($rotation === false && $exact === true){
                        if(!$this->Images->_duplicate($image,$i['purpose']) || !$this->Images->_purpose($image['id'],$i['purpose'],'add') || !$this->Images->_info($image['id'], $i['purpose'], $i)){
                            $errors[$i['purpose']] = __d('be', 'An error occured while saving image for this purpose! Please try again.');
                        }
                    }else{
                        if($i['sw'] < (int) $i['nw'] || $i['sh'] < (int) $i['nh']){
                            $errors[$i['purpose']] = __d('be', 'The selected image area is to small!');
                        }else{
                            
                            if($rotation !== false){ // rotate + crop
                                if($exact){
                                    $success = $this->Images->_crop(0,0,$org[0],$org[1],$image['id'],$i['purpose'],false,false,$rotation);
                                }else{
                                    $success = $this->Images->_crop($i['x'],$i['y'],$i['sw'],$i['sh'],$image['id'],$i['purpose'],$i['nw'],$i['nh'],$rotation);
                                }
                            }else{ // crop
                                $success = $this->Images->_crop($i['x'],$i['y'],$i['sw'],$i['sh'],$image['id'],$i['purpose'],$i['nw'],$i['nh']);
                            }
                            
                            if($success === false){
                                $errors[$i['purpose']] = __d('be', 'There was an error resizing this image. Try again later.');
                            }else{
                                if(!$this->Images->_purpose($image['id'],$i['purpose'],'add') || !$this->Images->_info($image['id'], $i['purpose'], $i)){
                                    $errors[$i['purpose']] = __d('be', 'An error occured while saving image for this purpose! Please try again.');
                                }
                            }
                        }
                    }
                }
            }

            // redirect
            if(count($errors) < 1){
                if($override === false){
                    $this->Flash->success(__d('be', 'The image has been successfully cropped'));
                    return $this->redirect(array('action' => 'index', $image['category_id'], '#' => $image['id']));
                }else{
                    return true;
                }
            }else{
                if($override === false){
                    $this->Flash->error(__d('be', 'One or more images couldn\'t be cropped. Please try again!'));
                }else{
                    return false;
                }
            }
        }

        // calc ratio / org image size
        foreach($purposes as $idx => $info){
            
            $purposes[$idx]['ratio'] = $info['width'] && $info['height'] ? $info['width']/$info['height'] : false;
            $purposes[$idx]['original'] = $size;
            $purposes[$idx]['valid'] = ($info['width'] > 0 && $info['width'] > $size[0]) || ($info['height'] > 0 && $info['height'] > $size[1]) ? false : true;
            $purposes[$idx]['exact'] = $info['width'] && $info['height'] && $info['width'] == $size[0] && $info['height'] == $size[1] ? true : false;
            $purposes[$idx]['crop'] = false;
            $purposes[$idx]['cropped'] = in_array($idx,$existing) ? true : false;
            
            // js values
            $purposes[$idx]['js'] = [
                'ratio' => $purposes[$idx]['ratio'],
                'width' => $info['width'] ? $info['width'] : 100,
                'height' => $info['height'] ? $info['height'] : 100,
            ];
            
            // crop infos
            $crop = $this->connection->execute("SELECT `info` FROM `image_blanks` WHERE `image_id` = :id AND `purpose` = :purpose LIMIT 1", ['id' => $id, 'purpose' => $idx])->fetchAll('assoc');
            if(is_array($crop) && count($crop) > 0){
                $_blank = json_decode($crop[0]['info'], true);
                if(file_exists($this->Images->path . $idx . DS . $image['id'] . '.' . $image['extension'])){
                    if(!isset($_blank['nh']) || empty($_blank['nh'])){
                        $_size = getimagesize($this->Images->path . $idx . DS . $image['id'] . '.' . $image['extension']);
                        $_blank['fw'] = $_size[0];
                        $_blank['fh'] = $_size[1];
                    }else if(!isset($_blank['nw']) || empty($_blank['nw'])){
                        $_size = getimagesize($this->Images->path . $idx . DS . $image['id'] . '.' . $image['extension']);    
                        $_blank['fw'] = $_size[0];
                        $_blank['fh'] = $_size[1];
                    }else {
                        $_blank['fw'] = $purposes[$idx]['width'];
                        $_blank['fh'] = $purposes[$idx]['height'];
                    }
                    $purposes[$idx]['crop'] = $_blank;
                }
            }
        }
        
        // fit org image to screen
        $maxW = 1024;
        $maxH = 768;
        $aspect = $size[0]/$size[1];
        $w = $size[0];
        $h = $size[1];
        if($w > $maxW || $h > $maxH) {
            if($w > $h) {
                $w = $maxW;
                $h = intval($maxW/$aspect);
            } else {
                $h = $maxH;
                $w = intval($maxH*$aspect);
            }
        }
        $fit = array($w,$h);
        
        // set
        $this->set(compact('errors','image','purposes','fit','size','maxW','maxH'));
        
        // info
        $this->Flash->success(__d('be', 'Please select the purpose for which you want to crop this image!'));
        
        // menu
        $menu = [
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => 'images', 'action' => 'index', $image['category_id'], '#' => $id],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', __d('be', 'Crop image') . ' <span><span class="purpose"></span> <span class="size"></span></span>');
        $this->set('menu', $menu);
    }

    public function auto($id){

        $this->autoRender = false;
        $ret = ['success' => false, 'id' => $id, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // get image
        if(($info = $this->Images->_auto($id))){
            $ret['success'] = true;
            $ret['msg'] =  __d('be', 'Blanks automatically created!');
            $ret['info'] = $info;
        }
        
        echo json_encode($ret);
        exit;
    }

    public function search($category){

        // init
        $term = array_key_exists('term', $this->request->query) ? $this->request->query['term'] : '';

        // index
        $this->index($category, $term);

        // menu
        $menu = [
            'right' => [
                [
                    'type' => 'icon',
                    'text' => __d('be', 'Search'),
                    'url' => ['controller' => 'images', 'action' => 'search', $category],
                    'icon' => 'search',
                    'class' => 'search',
                    'action' => 'input',
                    'input' => [
                        'name' => 'search_term',
                        'attr' => [
                            'placeholder' => __d('be', 'Search'),
                        ]
                    ]
                ],
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => 'images', 'action' => 'index', $category],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];

        $this->set('title', __d('be', 'Image search') . ' <span>' . $term . '</span>');
        $this->set('menu', $menu);
        $this->set('search', true);
        
        // render
        $this->render('index');

    }

    public function override($action = 'upload'){
        
        // init
        $path = $this->Images->path . 'override' . DS;
        $limit = 1;
        
        if($action == 'upload'){

            // init
            $this->autoRender = false;
            $ret = ['success' => false, 'error' => __d('be', 'Upload failed.')];
            
            // save
            if ($this->request->is('ajax') && $this->request->is('post')) {
                if(isset($this->request->data) && array_key_exists('Filedata', $this->request->data) && count($this->request->data['Filedata']) > 0){
                    foreach($this->request->data['Filedata'] as $file){
                        
                        // check filetype
                        if(!in_array($file['type'], Configure::read('upload.images.mime'))){
                            $ret = ['success' => false, 'error' => __d('be', 'Invalid image type!'), 'file' => $file];
                        }else{
                            if ($this->Images->_copy($file['tmp_name'], $path . $file['name'])) {
                                $ret = ['success' => true, 'file' => $file];
                            }else{
                                $ret = ['success' => false, 'error' => [], 'file' => $file];
                            }
                        }
                        
                    }
                }
            }
            
            echo json_encode($ret);
            exit;

        }else if($action == 'process'){

            // init
            $refresh = false;
            $processed = 0;

            // images
            $dir = new Folder($path);
            $images = $dir->findRecursive();

            // check
            foreach($images as $image){
                
                // reload
                if($processed >= $limit){
                    $refresh = true;
                    continue;
                }
                
                // basename
                $name = basename($image);
                
                // check
                $check = $this->Images->find('all')->where(['original' => $name])->toArray();
                
                if(is_array($check) && count($check) > 0){
                    
                    // filename
                    $filename = $check[0]->id . '.' . $check[0]->extension;
                    
                    // replace original
                    @copy($image, $this->Images->path . $this->Images->original . DS . $filename);
                    
                    // create "auto" sizes
                    foreach(Configure::read('images.sizes.auto') as $folder => $dimension){
                        $this->Images->_resize($this->Images->path . $this->Images->original . DS . $filename, $this->Images->path . $folder . DS . $filename, $dimension['width'], $dimension['height']);
                    }
                    
                    // create blanks
                    $data = ['image' => $check[0]->id, 'crop' => []];
                    $infos = $this->connection->execute("SELECT * FROM `image_blanks` WHERE `image_id` = :id", ['id' => $check[0]->id])->fetchAll('assoc');
                    foreach($infos as $info){
                        $data['crop'][] = json_decode($info['info'], true);
                    }
                    $this->crop($check[0]->id, $data);
                    
                    $processed++;
                    
                }
                
                // delete
                @unlink($image);
                
            }

            // refresh
            if($refresh === false){
                $this->Flash->success(__d('be', 'The image override process is finished.'));
                return $this->redirect(['action' => 'index']);
            }
            
            // menu
            $menu = [
                'left' => [],
                'right' => [],
            ];
            
            $this->set('title', __d('be', 'Override images'));
            $this->set('refresh', $refresh);
            $this->set('menu', $menu);

        }else{
            
            // redirect
            return $this->redirect(['controller' => 'images', 'action' => 'index']);
            
        }
    }

}
