<?php
namespace Frontend\Controller;

use Frontend\Controller\AppController;
use Cake\Controller\Controller;

class PackagesController extends AppController {

    public function index($id = false) {
        
		//get code
		$code = $this->getCode($id);
		
        // get content
        $content = $this->getContent($id, $code);
        
        // get prices
        $prices = $this->getPrices($id, $code);
        
        // overview
        $overview = $this->getCorrectOverview($content);
        
        // set vars
        $this->set('title', $content['html']);
        $this->set('content', $content);
        $this->set('prices', $prices);
        $this->set('overview', $overview);

        // render
        $this->render(DS . ucfirst($this->request->params['structure']['theme']) . DS . $this->name . DS . 'index' . DS);
    }

}
