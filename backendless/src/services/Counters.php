<?php namespace backendless\services;

use backendless\lib\RequestBuilder;
use backendless\Backendless;

class Counters
{
    
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Counters();
            
        }
        
        return self::$instance;
        
    }
    
    public function getAndIncrement( $counter_name ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/increment", '', 'PUT' );
        
    }
    
    public function incrementAndGet( $counter_name ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/increment/get", '', 'PUT' );
        
    }
    
    public function getAndDecrement( $counter_name) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/decrement", '', 'PUT' );
        
    }   
    
    public function decrementAndGet( $counter_name ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/decrement/get", '', 'PUT' );
        
    }    
    
    public function addAndGet( $counter_name, $number ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/incrementby/get?value=" . $number, '', 'PUT' );
        
    }    
    
    public function getAndAdd( $counter_name, $number ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/incrementby?value=" . $number, '', 'PUT' );
        
    }   
    
    public function compareAndSet( $counter_name, $expected, $updated ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/compareandset?expected=" . $expected . "&updatedvalue=". $updated, '', 'PUT' );
        
    }
    
    public function get( $counter_name ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name, '', 'GET' );
        
    }
    
    public function reset( $counter_name ) {
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name ."/reset", '', 'PUT' );
        
    }
    
}
