<?php namespace backendless\services;

use backendless\lib\RequestBuilder;
use backendless\Backendless;

class Events
{
    
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Events();
            
        }
        
        return self::$instance;
        
    }
    
    public function dispatch( $event_name, $event_args_array = null ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/servercode/events/" . $event_name, $event_args_array, 'POST' );
      
    }
    
}

