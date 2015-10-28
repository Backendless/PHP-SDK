<?php namespace backendless\services;

use backendless\lib\RequestBuilder;
use backendless\Backendless;

class Cache
{
    
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Cache();
            
        }
        
        return self::$instance;
        
    }
    
    public function put( $key, $value, $time_to_live ) {
        
        $data = base64_encode(serialize($value));

        $user = Backendless::$UserService->getCurrentUser();
        
        if( $user != null ) {
        
            RequestBuilder::addHeader( "user-token", $owner->getUserToken());
            
        }
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key ."?timeout=".$time_to_live, $data, 'PUT' );
        
    }
    
    public function get( $key ) {

        
        $user = Backendless::$UserService->getCurrentUser();
        
        if( $user != null ) {
        
            RequestBuilder::addHeader( "user-token", $owner->getUserToken());
            
        }
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key, '', 'GET' );
        
        $result = RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key, '', 'GET' );
        
        return unserialize(base64_decode($result));
        
    }
    
    public function delete( $key ) {

        
        $user = Backendless::$UserService->getCurrentUser();
        
        if( $user != null ) {
        
            RequestBuilder::addHeader( "user-token", $owner->getUserToken());
            
        }
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key, '', 'DELETE' );
        
    }
    
    public function contains ($key) {

        $user = Backendless::$UserService->getCurrentUser();
        
        if( $user != null ) {
        
            RequestBuilder::addHeader( "user-token", $owner->getUserToken());
            
        }
        
         return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key . "/check", '', 'get' );
        
    }
    
    public function expireIn( $key, $seconds ) {
        
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key . "expireIn?timeout=" . $seconds, '', 'PUT' );
        
    }
    
    public function expireAt( $key, $timestamp ) {
    
        $timestamp = $timestamp * 1000;
        RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/cache/" . $key . "expireAt?timestamp=" . $timestamp, '', 'PUT' );
        
    }

}

