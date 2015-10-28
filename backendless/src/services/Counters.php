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
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/increment", '', 'PUT' );
        
    }
    
    public function incrementAndGet( $counter_name ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/increment/get", '', 'PUT' );
        
    }
    
    public function getAndDecrement( $counter_name) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/decrement", '', 'PUT' );
        
    }   
    
    public function decrementAndGet( $counter_name ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/decrement/get", '', 'PUT' );
        
    }    
    
    public function addAndGet( $counter_name, $number ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/incrementby/get?value=" . $number, '', 'PUT' );
        
    }    
    
    public function getAndAdd( $counter_name, $number ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/incrementby?value=" . $number, '', 'PUT' );
        
    }   
    
    public function compareAndSet( $counter_name, $expected, $updated ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name . "/get/compareandset?expected=" . $expected . "&updatedvalue=". $updated, '', 'PUT' );
        
    }
    
    public function get( $counter_name ) {
        
        $this->addUserToken();
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name, '', 'GET' );
        
    }
    
    public function reset( $counter_name ) {
        
        $this->addUserToken();
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/counters/" . $counter_name ."/reset", '', 'PUT' );
        
    }
    
    private function addUserToken() {
        
        $user = Backendless::$UserService->getCurrentUser();
        
        if( $user != null ) {
        
            RequestBuilder::addHeader( "user-token", $owner->getUserToken());
            
        }
        
    }
    
    
}