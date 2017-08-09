<?php
class InsureController extends Common {
    
    public function __construct()
    {    
        parent::__construct();        
        $this->view->setTheme('');
    }

    public function indexAction(){
        $this->view->display('mobi2/safety/agreeMent1');        
    }
    
    public function secAction(){    
       $this->view->display('mobi2/safety/agreeMent2');      
    }   
    
   public function thirdAction(){    
       $this->view->display('mobi2/safety/agreeMent3');      
    }
    
    public function fourthAction(){    
       $this->view->display('mobi2/safety/agreeMent4');      
    }  
}