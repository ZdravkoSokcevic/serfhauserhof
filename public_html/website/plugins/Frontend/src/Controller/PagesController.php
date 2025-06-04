<?php
namespace Frontend\Controller;

use Frontend\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Core\Configure;

class PagesController extends AppController {

    public function index($id = false) {
        
        // get content
        $content = $this->getContent($id, $this->getCode($id));
        
        // set vars
        $this->set('title', $content['html']);
        $this->set('content', $content);

        // render
        $this->render(DS . ucfirst($this->request->params['structure']['theme']) . DS . $this->name . DS . 'index' . DS);
    }
    
    public function sitemap(){
        
        $sitemap = [];
        foreach(Configure::read('translations') as $k => $v){
            if($v['active']){
                $sitemap[$k] = $this->__crawl('', $this->request->params['structure']['id'], $k, false, false, false, false);
            }
        }
        
        $this->set('sitemap', $sitemap);
        
        $this->set('_serialize', false);
        $this->render('/Sitemaps/xml');
    }

}
