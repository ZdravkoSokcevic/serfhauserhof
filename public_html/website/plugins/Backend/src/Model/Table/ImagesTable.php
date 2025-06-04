<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\RecordNotFoundException;

class ImagesTable extends Table
{

    public $path = false;
    public $url = false;
    public $tmp = 'img';
    public $original = 'original';
    public $connection;

    public function initialize(array $config)
    {
        
        // init
        $this->connection = ConnectionManager::get('default');
        $this->url = DS . Configure::read('upload.images.dir');
        $this->path = ROOT . DS . Configure::read('App.webroot') . DS . Configure::read('upload.images.dir');
        
        // behaviors
        $this->addBehavior('Timestamp');
        $this->addBehavior('Translate', ['fields' => ['title'], 'defaultLocale' => false]);
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('title', __d('be', 'A title is required'))
            ->notEmpty('original', __d('be', 'A original title is required'))
            ->notEmpty('mime', __d('be', 'A mime type is required'))
            ->notEmpty('extension', __d('be', 'An file extension is required'))
            ->notEmpty('category_id', __d('be', 'A category is required'));
    }
    
    public function beforeSave($event, $entity, $options){
        if(isset($entity->file)){
            if(!isset($entity->id)){
                $entity->id = Text::uuid();
            }
            
            if($this->_copy($entity->file['tmp_name'], $this->path . $this->original . DS . $entity->id . '.' . $entity->extension) == false){
                return false;
            }
        }
    }

    public function beforeDelete($event, $entity, $options){
        $this->_cleanup($entity['id'] . '.' . $entity['extension']);
    }
    
    public function _cleanup($file){
        
        // get files
        $dir = new Folder($this->path);
        $files = $dir->findRecursive($file);

        // delete files
        foreach ($files as $file) {
            @unlink($file);
        }
        
        return true;
    }

    public function _category($category, $code){
        $success = true;
        $images = $this->find('list')->where(['category_id' => $category])->toArray();
        foreach($images as $id => $title){
            $entity = $this->get($id);
            if(!$this->delete($entity)){
                $success = false;
            }
        }
        return $success;
    }
    
    public function _copy($src, $dst){
        
        // destination
        $dir = dirname($dst);
        if(!file_exists($dir)) mkdir($dir, 0777, true);
        
        if(class_exists('Imagick')){

            // copy
            $image = new \Imagick($src);
            $image->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
            $image->writeImage($dst);
            $image->destroy();
            
            // set premissions
            chmod($dst, 0777);
            
            return $dst;
        }
        return false;
    }
    
    public function _resize($src, $dst, $width, $height) {
        
        if(class_exists('Imagick')){
            
            // destination
            $dir = dirname($dst);
            if(!file_exists($dir)) mkdir($dir, 0777, true);

            // image info
            $image = new \Imagick($src);
            $imageprops = $image->getImageGeometry();
            $ratio = min($width / $imageprops['width'], $height / $imageprops['height']);
            $width = ceil($imageprops['width'] * $ratio);
            $height = ceil($imageprops['height'] * $ratio);
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

            // resize
            if ($imageprops['width'] <= $width and $imageprops['height'] <= $height){
                return $this->_copy($src, $dst);
            }
            $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 0.9, true);
            
            // quality
            switch($ext){
                case "jpeg":
                case "jpg":
                    $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
                    $image->setImageCompressionQuality(Configure::read('upload.images.quality.jpg'));
                    $image->stripImage(); 
                    break;
                case "png":
                    $image->setImageCompression(\Imagick::COMPRESSION_LZW);
                    $image->setImageCompressionQuality(Configure::read('upload.images.quality.png'));
                    $image->stripImage(); 
                    break;
                case "gif":
                    break;
            }
            $image->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
            $image->writeImage($dst);
            $image->destroy();
            return $dst;
        }
        return false;
    }

    public function _crop($x, $y, $sw, $sh, $id, $purpose = false, $width = false, $height = false, $degrees = false)
    {
            
        // check image
        try {
            $image = $this->get($id)->toArray();
        } catch (RecordNotFoundException $e) {
            return false;
        }

        // check usage
        $purpose = (int) $purpose;
        if(!array_key_exists($purpose, Configure::read('images.sizes.purposes'))){
            return false;
        }
        
        // original
        $original = $this->path . $this->original . DS . $id . '.' . $image['extension'];
        
        // rotate?
        $degrees = (int) $degrees;
        if($degrees > 0 && $degrees < 360){
            $cleanup = true;
            $src = $this->_rotate($original, $purpose, $degrees);
            if($src === false) return false;
        }else{
            $cleanup = false;
            $src = $original;
        }
        
        if(!$this->_cropNow($src, $x, $y, $sw, $sh, $purpose, $width, $height)){
            return false;
        }

        // create thumbs
        $this->_thumbs($image, $purpose);

        // cleanup
        if($cleanup && file_exists($src)){
            @unlink($src);
        }

        return true;
    }
    
    public function _cropNow($src, $x, $y, $sw, $sh, $purpose = false, $width = false, $height = false) {
        
        // init
        $info = getimagesize($src);
        if (!$info) return false;
    
        // picture is too small!
        if ($info[0] < $sw and $info[1] < $sh){
            return false;
        }
    
        // type?
        $mime = $info['mime'];
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        $file = basename($src);
        $dir = $this->path . $purpose;
        $dest = $dir . DS . $file;
        
        if(!file_exists($dir)) mkdir($dir, 0777, true);
        if(class_exists('Imagick')){
            
            // image
            $image = new \Imagick($src);
            $image->setImageFormat($ext); 
            $image->cropImage($sw, $sh, $x, $y);

            // resize
            if($width || $height){
                if($width && $height){
                    // nothing ...
                }else if($width){
                    $height = round(($width/$sw)*$sh,0);
                }else if($height){
                    $width = round(($height/$sh)*$sw,0);
                }
                if($sw > $width || $sh > $height){
                    $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 0.9, false);
                }
            }

            // quality
            switch($ext){
                case "jpeg":
                case "jpg":
                    $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
                    $image->setImageCompressionQuality(Configure::read('images.quality.' . $ext));
                    $image->stripImage(); 
                    break;
                case "png":
                    $image->setImageCompression(\Imagick::COMPRESSION_LZW);
                    $image->setImageCompressionQuality(Configure::read('images.quality.' . $ext));
                    $image->stripImage(); 
                    break;
                case "gif":
                    break;
            }

            // save
            $image->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
            $image->writeImage($dest);
            $image->destroy();
        }else{
            return false;
        }

        return true;
    }

    public function _rotate($src, $purpose, $degrees = false) {
        
        // init
        $info = getimagesize($src);
        if (!$info) return false;
        
        $mime = $info['mime'];
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        $file = basename($src);
        $dir = TMP . $this->tmp;
        $dest = $dir . DS . $file;
        
        if(!file_exists($dir)) mkdir($dir, 0777, true);
        if(class_exists('Imagick')){
            
            // image
            $image = new \Imagick($src);
            $image->rotateImage(new \ImagickPixel('transparent'), $degrees);
            $image->setGravity(\Imagick::GRAVITY_NORTHWEST);
            $size = $image->getImageGeometry();
            
            // change canvas size
            if($size['width'] < $info[0]){
                $image->extentImage($info[0], $size['height'], floor((($info[0] - $size['width'])/2)*-1) , 0);
                $size = $image->getImageGeometry();
            }
            if($size['height'] < $info[1]){
                $image->extentImage($size['width'], $info[1], 0, floor((($info[1] - $size['height'])/2)*-1));
                $size = $image->getImageGeometry();
            }
            
            // crop org size
            $image->cropImage($info[0], $info[1], floor(($size['height']/2) - ($info[1]/2)), floor(($size['width']/2) - ($info[0]/2)));
            
            // quality
            switch($ext){
                case "jpeg":
                case "jpg":
                    $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
                    $image->stripImage(); 
                    break;
                case "png":
                    $image->setImageCompression(\Imagick::COMPRESSION_LZW);
                    $image->stripImage(); 
                    break;
                case "gif":
                    break;
            }

            // save
            $image->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
            $image->writeImage($dest);
            $image->destroy();
        }else{
            return false;
        }

        return $dest;
    }

    public function _revolve($src, $degrees) {
        
        // init
        $info = getimagesize($src);
        if (!$info) return false;
        
        $mime = $info['mime'];
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

        if(file_exists($src)){
        
            if(class_exists('Imagick')){

                // image
                $image = new \Imagick($src);
                $image->rotateImage(new \ImagickPixel('transparent'), $degrees);
                $image->setGravity(\Imagick::GRAVITY_CENTER);

                // quality
                switch($ext){
                    case "jpeg":
                    case "jpg":
                        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
                        $image->stripImage(); 
                        break;
                    case "png":
                        $image->setImageCompression(\Imagick::COMPRESSION_LZW);
                        $image->stripImage(); 
                        break;
                    case "gif":
                        break;
                }

                // save
                $image->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
                $image->writeImage($src);
                $image->destroy();
            }else{
                return false;
            }
        
        }else{
            return false;
        }

        return true;
        
    }

    public function _duplicate($image, $purpose){
        
        // init
        $src = $this->path . $this->original . DS . $image['id'] . '.' . $image['extension'];

        if(file_exists($src) && array_key_exists($purpose, Configure::read('images.sizes.purposes'))){

            // init
            $info = getimagesize($src);
            if (!$info) return false;
            
            $mime = $info['mime'];
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            $file = basename($src);
            $dir = $this->path . $purpose;
            $dest = $dir . DS . $file;
            
            if(!file_exists($dir)) mkdir($dir, 0777, true);
            if(!$this->_copy($src,$dest)){
                return false;
            }
            
            // create thumbs
            $this->_thumbs($image, $purpose);
            
            return true;
        }
        return false;
    }

    public function _thumbs($image, $purpose){
        
        // init
        $file = $image['id'] . '.' . $image['extension'];
        $src = $this->path . $purpose . DS . $file;
        if(file_exists($src) && array_key_exists($purpose, Configure::read('images.sizes.purposes')) && array_key_exists('thumbs', Configure::read('images.sizes.purposes.' . $purpose)) && is_array(Configure::read('images.sizes.purposes.' . $purpose . '.thumbs'))){
            
            // init
            $info = getimagesize($src);
            if (!$info) return false;
            
            $mime = $info['mime'];
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            
            foreach(Configure::read('images.sizes.purposes.' . $purpose . '.thumbs') as $thumb){
                if(!$this->_resize($src, $this->path . $purpose . DS . $thumb['folder'] . DS . $file, $thumb['width'], $thumb['height'])){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function _purpose($id, $purpose, $mode){
        try {

            $image = $this->get($id);
            $data = [];
            $purposes = array_filter(explode(",", $image->purpose));
            if($mode == 'add'){
                if(in_array($purpose, $purposes)){
                    return true;
                }
                array_push($purposes,$purpose);
                sort($purposes);
                $data['purpose'] = join(',', $purposes);
            }else if($mode == 'remove'){
                if(!in_array($purpose, $purposes)){
                    return true;
                }
                $data['purpose'] = [];
                foreach($purposes as $k => $v){
                    if($v != $purpose && !empty($purpose)) $data['purpose'][] = $v;
                }
                sort($data['purpose']);
                $data['purpose'] = join(',', $data['purpose']);
            }
            if(count($data) > 0){
                $this->patchEntity($image, $data);
                if ($this->save($image)) {
                    return true;
                }
            }
        } catch (RecordNotFoundException $e) { }
        
        return false;
    }

    public function _info($id, $purpose, $info){
        
        // init
        $datetime = date('Y-m-d H:i:s');
        $info = json_encode(array_merge($info,array('id' => $id)));

        // check
        $check = $this->connection->execute("SELECT `image_id` FROM `image_blanks` WHERE `image_id` = :id AND `purpose` = :purpose LIMIT 1", ['id' => $id, 'purpose' => $purpose])->fetchAll('assoc');        
        if(count($check) > 0){
            $sql = "UPDATE `image_blanks` SET `info` = '" . $info . "', `modified` = '" . $datetime . "' WHERE `image_id` = '" . $id . "' AND `purpose` = '" . $purpose . "' LIMIT 1";
        }else{
            $sql = "REPLACE INTO `image_blanks` (`image_id`, `purpose`, `info`, `modified`, `created`) VALUES ('" . $id . "', '" . $purpose . "', '" . $info . "', '" . $datetime . "', '" . $datetime . "')";
        }
        
        // save
        $this->connection->execute($sql);
        
        return true;
    }
    
    public function _auto($id){
        
        // check image
        try {
            $image = $this->get($id)->toArray();
        } catch (RecordNotFoundException $e) {
            return false;
        }
        
        // init
        $success = 0;
        $purposes = Configure::read('images.sizes.purposes');
        $src = $this->path . $this->original . DS . $id . '.' . $image['extension'];
        $size = getimagesize($src);
        $size['ratio'] = $size[0]/$size[1];
        $blanks = $this->_blanks($image);
        
        // loop through purposes
        foreach($purposes as $purpose => $info){
                   
            // check for existing
            if(array_key_exists($purpose, $blanks) && $blanks[$purpose]['recrop'] === false){
                continue;
            }
            
            // check size
            if(($info['width'] > 0 && $info['width'] > $size[0]) || ($info['height'] > 0 && $info['height'] > $size[1])){
                continue;
            }

            // calc
            $x = $y = $sw = $sh = 0;
            $ratio = $info['width'] && $info['height'] ? $info['width']/$info['height'] : false;
            
            if($info['width'] && $info['height'] && $info['width'] == $size[0] && $info['height'] == $size[1]){ // exact?
                $x = $y = 0;
                $sw = $size[0];
                $sh = $size[1];
            }else if($ratio == false){
                $x = $y = 0;
                $sw = $size[0];
                $sh = $size[1];
            }else{
                $sw = $size[0];
                $sh = ceil(($size[0]/$info['width'])*$info['height']);
                if($sh > $size[1]){
                    $sw = ceil(($size[1]/$info['height'])*$info['width']);
                    $sh = $size[1];
                }
                $x = ceil(($size[0]-$sw)/2); // h align
                $y = ceil(($size[1]-$sh)/2); // v align
            }
            
            if($sw > 0 && $sh > 0){
                    
                // mockup
                $i = [
                    'purpose' => $purpose,
                    'x' => $x,
                    'y' => $y,
                    'sw' => $sw,
                    'sh' => $sh,
                    'ratio' => $ratio,
                    'deg' => 0,
                    'focus' => 1,
                    'fx' => 0,
                    'fy' => 0,
                    'nw' => $info['width'],
                    'nh' => $info['height'],
                    'exact' => $info['width'] && $info['height'] && $info['width'] == $size[0] && $info['height'] == $size[1] ? true : false,
                ];
                
                // process
                if($i['exact'] === true){
                    if(!$this->_duplicate($image,$i['purpose']) || !$this->_purpose($image['id'],$i['purpose'],'add') || !$this->_info($image['id'], $i['purpose'], $i)){
                        return false;
                    }
                }else{
                    if($i['sw'] < (int) $i['nw'] || $i['sh'] < (int) $i['nh']){
                        return false;
                    }else{
                        $success = $this->_crop($i['x'],$i['y'],$i['sw'],$i['sh'],$image['id'],$i['purpose'],$i['nw'],$i['nh']);
                        if($success === false){
                            return false;
                        }else{
                            if(!$this->_purpose($image['id'],$i['purpose'],'add') || !$this->_info($image['id'], $i['purpose'], $i)){
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return $this->information($image, "Y-m-d H:i:s");
    }
    
    public function _blanks($image, $format = false){

        // init
        $blanks = [];

        // original
        $original = $this->path . $this->original . DS . $image['id'] . '.' . $image['extension'];
        $modified = filemtime($original);
        $_blanks = $this->connection->execute("SELECT * FROM `image_blanks` WHERE `image_id` = :id", ['id' => $image['id']])->fetchAll('assoc');
        if(count($_blanks) > 0){
            foreach($_blanks as $_b){
                $m = strtotime($_b['modified']);
                $c = strtotime($_b['created']);
                $blanks[$_b['purpose']] = [
                    'recrop' => $modified > strtotime($_b['modified']) ? true : false,
                    'modified' => $format ? date($format, $m) : $m,
                    'created' => $format ? date($format, $c) : $c,
                ];
            }
        }
        
        return $blanks;
    }
    
    public function information($image, $format = false){
        
        // init
        $blanks = $this->_blanks($image, $format);
        $recrop = false;
        $used = false;
        
        // recrop
        foreach($blanks as $k => $v){
            if($v['recrop']){
                $recrop = true;
            }
        }

        // used
        // TODO: check if image is used ...
        
        return ['used' => $used, 'recrop' => $recrop, 'blanks' => $blanks];
    }
    
}