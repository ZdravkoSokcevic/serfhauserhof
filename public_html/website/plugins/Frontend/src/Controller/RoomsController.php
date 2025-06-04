<?php
namespace Frontend\Controller;

use Frontend\Controller\AppController;
use Cake\Controller\Controller;

class RoomsController extends AppController {

    public function index($id = false) {
        
		//get code
		$code = $this->getCode($id);
		
        // get content
        $content = $this->getContent($id, $code);
        
        // get prices
        $prices = $this->getPrices($id, $code);
        
        // overview
        $overview = $this->getCorrectOverview($content);
        $next = false;
        if(is_array($overview) && array_key_exists('details', $overview) && is_array($overview['details']) && array_key_exists('_details', $overview['details']) && is_array($overview['details']['_details']) && array_key_exists('nodes', $overview['details']['_details']) && is_array($overview['details']['_details']['nodes']) && array_key_exists('rooms', $overview['details']) && is_array($overview['details']['rooms']) && array_key_exists(0, $overview['details']['rooms']) && is_array($overview['details']['rooms'][0]) && array_key_exists('details', $overview['details']['rooms'][0]) && is_array($overview['details']['rooms'][0]['details']) && array_key_exists('contain', $overview['details']['rooms'][0]['details']) && is_array($overview['details']['rooms'][0]['details']['contain'])){
            $act = false;
            foreach($overview['details']['rooms'][0]['details']['contain'] as $k => $v){
                if($v === $id){
                    $act = $k;
                }
            }
            if($act !== false){
                $i = $act + 1;
                $next = array_key_exists($i, $overview['details']['rooms'][0]['details']['contain']) ? $overview['details']['rooms'][0]['details']['contain'][$i] : $overview['details']['rooms'][0]['details']['contain'][0];
                if(array_key_exists($next, $overview['details']['_details']['nodes'])){
                    $next = $overview['details']['_details']['nodes'][$next];
                }else{
                    $next = false;
                }
            }
        }
        
        // set vars
        $this->set('title', $content['html']);
        $this->set('content', $content);
        $this->set('prices', $prices);
        $this->set('overview', $overview);
        $this->set('next', $next);

        // render
        $this->render(DS . ucfirst($this->request->params['structure']['theme']) . DS . $this->name . DS . 'index' . DS);
    }

}
