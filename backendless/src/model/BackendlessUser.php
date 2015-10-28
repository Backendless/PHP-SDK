<?php
namespace backendless\model;

use backendless\model\Data;

class BackendlessUser extends Data {
   
    public function  __construct() {
        
        parent::__construct();
        
    }

    public function getUserId() {
    
        return $this->getObjectId();
            
    }
    
    public function getUserToken() {
        
        return ( isset( $this->data["user-token"] ) ) ? $this->data["user-token"] : null;
        
    }
    
    public function unsetUserToken() {
        
        if ( isset( $this->data["user-token"] ) ) {
            
            unset($this->data["user-token"]);
            
        }
        
    }

}
