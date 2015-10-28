<?php
namespace backendless\services\user;

class FacebookFieldMappings {

    protected $data_keys = [];
    
    public function __construct() {
        
    }
    
    public function put( $key, $val ) {
        
        $this->data_keys[$key] = $val;
        
    }
    
    public function getFields() {
        
        return $this->data_keys;
        
    }

}