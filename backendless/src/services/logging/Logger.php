<?php
namespace backendless\services\logging;
use backendless\lib\RequestBuilder;

class Logger {

    private $name;
    
    public function setName( $name ) {
        
        $this->name = $name;
        
    }
    
    public function getName() {
        
        return $this->name;
        
    }
    
    public function  debug( $message ) {
        
        $this->writeLog($message, "DEBUG" );
        
    }
    
    public function info( $message ) {
        
        $this->writeLog($message, "INFO" );
        
    }
    
    public function  warn( $message ) {
        
        $this->writeLog($message, "WARN" );
        
    }

    
    public function error( $message ) {
        
        $this->writeLog($message, "ERROR" );
        
    }
    

    public function fatal( $message ) {
        
        $this->writeLog($message, "FATAL" );
        
    }

    public function trace( $message ) {
        
        $this->writeLog($message, "TRACE" );
        
    }
    
    public function writeLog( $message, $type ) {
        
        $log_data = [];
        $log_data[0] = [];
        $log_data[0]["log-level"] = $type;
        $log_data[0]["logger"] = $this->getName();
        $log_data[0]["timestamp"] = time();
        $log_data[0]["message"] = $message;
        $log_data[0]["exception"] = "";
        
        RequestBuilder::doRequest( 'log', null, $log_data, 'PUT' );
        
    }

}  
