<?php

namespace Frontend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;
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
        $this->addBehavior('Translate', ['fields' => ['title'], 'defaultLocale' => false]);
        
    }

    public function afterFind($row){
        
        // purpose
        $row->purpose = array_filter(explode(",", $row->purpose));
        
        // urls/paths
        list($row->urls, $row->seo, $row->paths) = $this->_blanks($row);

        // focus
        $row->focus = $this->_focus($row, $row->paths);

        return $row;
    }
    
    public function _blanks($row){

        // init
        $file = $row['id'] . '.' . $row['extension'];
        $check = $urls = $seo = $paths = [];
        $name = array_key_exists('name', $row) ? strtolower(Text::slug($row['title'])) . '.' . $row['extension'] : 'image' . '.' . $row['extension'];
        
        // check
        $check[$this->original] = [
            'url' => $this->url . $this->original . DS . $file,
            'seo' => DS . Configure::read('seo.images.folder') . DS . $this->original . DS . $row['id'] . DS . $name,
            'path' => $this->path . $this->original . DS . $file,
        ];
        foreach(Configure::read('images.sizes.auto') as $k => $v){
            $check[$k] = [
                'url' => $this->url . $k . DS . $file,
                'seo' => DS . Configure::read('seo.images.folder') . DS . $k . DS . $row['id'] . DS . $name,
                'path' => $this->path . $k . DS . $file,
            ];
        }
        foreach(Configure::read('images.sizes.purposes') as $k => $v){
            $check[$k] = [
                'url' => $this->url . $k . DS . $file,
                'seo' => DS . Configure::read('seo.images.folder') . DS . $k . DS . $row['id'] . DS . $name,
                'path' => $this->path . $k . DS . $file,
            ];
            if(array_key_exists('thumbs', $v) && is_array($v['thumbs'])){
                foreach($v['thumbs'] as $_k => $_v){
                    $check[$k . '_' . $_v['folder']] = [
                        'url' => $this->url . $k . DS . $_v['folder'] . DS . $file,
                        'seo' => DS . Configure::read('seo.images.folder') . DS . $k . '-' . $_v['folder'] . DS . $row['id'] . DS . $name,
                        'path' => $this->path . $k . DS . $_v['folder'] . DS . $file,
                    ];
                }
            }
        }
        foreach($check as $k => $v){
            if(file_exists($v['path'])){
                $urls[$k] = $v['url'];
                $seo[$k] = $v['seo'];
                $paths[$k] = $v['path'];
            }
        }
        
        return [$urls, $seo, $paths];
    }

    public function _focus($row, $paths){
        
        // init
        $focus = false;

        // check
        $blanks = $this->connection->execute("SELECT * FROM `image_blanks` WHERE `image_id` = :id", ['id' => $row['id']])->fetchAll('assoc');
        foreach($blanks as $blank){
            $info = json_decode($blank['info'], true);
            $size = false;
            if(array_key_exists($blank['purpose'], $paths) && array_key_exists('fx',$info) && array_key_exists('fy',$info)){

                //get image size
                if(!isset($info['nh']) || empty($info['nh'])){
                    $size = $size === false ? getimagesize($paths[$blank['purpose']]) : $size;
                    $info['nh'] = $size[1];
                }   
                if(!isset($info['nw']) || empty($info['nw'])){
                    $size = $size === false ? getimagesize($paths[$blank['purpose']]) : $size;   
                    $info['nw'] = $size[0];
                }       
                
                // x/y position
                $x = 100/$info['nw']*(substr($info['fx'],0,1)=='-'?($info['nw']/2)-(substr($info['fx'],1)):($info['nw']/2)+$info['fx']);
                $y = 100-(100/$info['nh']*(substr($info['fy'],0,1)=='-'?($info['nh']/2)-(substr($info['fy'],1)):($info['nh']/2)+$info['fy']));
                
                $focus[$info['purpose']] = array('x' => round($x), 'y' => round($y), 'css' => round($x) . '% ' . round($y) . '%');
            }else{
                $focus[$info['purpose']] = array('x' => 0, 'y' => 0, 'css' => 'center center');
            }
        }        

        return $focus;
    }
    
}