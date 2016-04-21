<?php
namespace backendless\lib;

use backendless\Backendless;
use backendless\lib\HttpRequest;
use backendless\exception\BackendlessException;

class RequestBuilder
{
    
    private static $headers = [];

    private function __construct() {

    }
    
    public static function addHeader( $name, $val ) {
        
        self::$headers[$name] = $val;
        
    }
    
    public static function doRequest( $service = null, $action = null, $data_array = null, $method = 'POST' ) {
        
        
        $target = Backendless::getUrl() . '/' . Backendless::getVersion();
        
        if( $service !== null ) {
            
            $target .= '/' . $service;
            
        }
        
        if( $action !== null ) {
            
            $target .= '/' . $action;
            
        }
        
        
        return self::doRequestByUrl( $target, $data_array, $method );

    } 
    
    public static function doRequestByUrl( $url, $data_array, $method ) {
        
        $http_request = new HttpRequest();
        
        $http_request->setTargetUrl( $url )
                     ->setHeader( 'application-id', Backendless::getApplicationId() )
                     ->setHeader( 'secret-key', Backendless::getSecretKey() )
                     ->setHeader( 'application-type', 'REST' )
                     ->setHeader( 'Accept', '*/*' )
                     ->setHeader( 'Content-Type', 'application/json' );
        
        foreach ( self::$headers as $heder_n => $h_val ) {
            
            $http_request->setHeader( $heder_n, $h_val );
            
        }
        
        self::addUserTokenHeader( $http_request );
        
        self::$headers = [];
                
        $http_request->request( json_encode( $data_array ), $method );
        
        self::handleError( $http_request );
        
        return json_decode( $http_request->getResponse(), true );
        
    }
    
    public static function doRequestWithHeaders( $service = null, $action = null, $data_array = null, $method = 'POST', $headers = [] ) {
        
        $url = Backendless::getUrl() . '/' . Backendless::getVersion();
        
        if( $service !== null ) {
            
            $url .= '/' . $service;
            
        }
        
        if( $action !== null ) {
            
            $url .= '/' . $action;
            
        }
        
         $http_request = new HttpRequest();
        
        $http_request->setTargetUrl( $url )
                     ->setHeader( 'application-id', Backendless::getApplicationId() )
                     ->setHeader( 'secret-key', Backendless::getSecretKey() );

        
        foreach ( $headers as $name => $val ) {
            
            $http_request->setHeader( $name, $val );
            
        }
        
        foreach ( self::$headers as $heder_n => $h_val ) {
            
            $http_request->setHeader( $heder_n, $h_val );
            
        }
        
        self::addUserTokenHeader( $http_request );
        
        self::$headers = [];
                
        $http_request->request( json_encode( $data_array ), $method );
        
        self::handleError( $http_request );
        
        return json_decode( $http_request->getResponse(), true );
        
    }
    
    public static function Get( $url ) {
        
        $http_request = new HttpRequest();
        
        $http_request->setTargetUrl( $url )
                     ->setHeader( 'application-id', Backendless::getApplicationId() )
                     ->setHeader( 'secret-key', Backendless::getSecretKey() )
                     ->setHeader( 'application-type', 'REST' )
                     ->setHeader( 'Accept:', '*/*' )
                     ->setHeader( 'Content-Type', 'application/json' );
        
        foreach ( self::$headers as $heder_n => $h_val ) {
            
            $http_request->setHeader( $heder_n, $h_val );
            
        }
        
        self::addUserTokenHeader( $http_request );
        
        self::$headers = [];
                
        $http_request->request( '', 'GET' );
        
        self::handleError( $http_request );
        
        return  $http_request->getResponse();
        
    }
    
    protected static function handleError( $http_request ) {
        
        if( $http_request->getResponseCode() != 200 ) {

            $error =  json_decode( $http_request->getResponse(), true );
            
            if( !isset( $error[ 'message' ] ) ) {
                
                throw new BackendlessException( 'API responce ' .$http_request->getResponseStatus() . ' ' . $http_request->getResponseCode() . $http_request->getResponse() );
                
            } else {
                
                throw new BackendlessException( 'Backendless API return error: ' . $error[ 'message' ] . ' Error code:' .$error[ 'code' ] , $error[ 'code' ] );
                
            }

        }
        
    }
    
    public static function addUserTokenHeader( $http_request ) {
        
        $current_user = Backendless::$UserService->getCurrentUser();
        
        if( $current_user == null ) {
            
            return;
            
        }
        
        $user_token = $current_user->getUserToken();
        
        if( $user_token == null ) {
            
            return;
            
        }
        
        $http_request->setHeader( 'user-token', $user_token );

    }
    
}
