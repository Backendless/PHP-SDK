<?php namespace backendless\services;

use backendless\services\logging\Logger;


class Logging
{
    
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Logging();
            
        }
        
        return self::$instance;
        
    }
    
    public function getLogger( $logger_name  ) {
        
        $logger = new Logger();
        $logger->setName( $logger_name );
        
        return $logger;
        
    }

}

