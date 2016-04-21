<?php
namespace backendless;

use backendless\lib\Log;
use backendless\services\UserService;
use backendless\services\Persistence;
use backendless\services\Geo;
use backendless\services\Files;
use backendless\services\Messaging;
use backendless\services\Cache;
use backendless\services\Counters;
use backendless\services\Logging;
use backendless\services\Events;
use backendless\exception\BackendlessException;


class Backendless
{
    
    private static $url = "https://api.backendless.com";
    
    public static $UserService;
    public static $Persistence;
    public static $Data;
    public static $Messaging;
    public static $Geo;
    public static $Events; 
    public static $Cache;
    public static $Counters;
    public static $Logging;
    public static $Files;
    
    public static $InvocationContext;

    private static $application_id;
    private static $secret_key;
    private static $version;
    
    private static $ignore_map_exception = false;    
    private static $classes_map = [];
    
    private static $sdk_mode_bl = false;
    
    private function __construct() { }

    public static function staticConstruct() {

        self::$UserService = UserService::getInstance();
        self::$Persistence = Persistence::getInstance();
        self::$Data = Persistence::getInstance();
        self::$Geo  = Geo::getInstance();
        self::$Files  = Files::getInstance();
        self::$Messaging = Messaging::getInstance();
        self::$Events   = Events::getInstance();
        self::$Cache = Cache::getInstance();
        self::$Counters = Counters::getInstance();
        self::$Logging = Logging::getInstance();
        
        self::mapTableToClass( 'GeoPoint', 'backendless\model\GeoPoint' );
        self::mapTableToClass( 'Users', 'backendless\model\BackendlessUser' );

    }

    public static function initApp( $application_id, $secret_key, $version ) {

        if( $application_id != null && $application_id !== '' ) {
            if( $secret_key != null && $secret_key != '' ) {
                if( $version != null && $version != '' ) {

                    self::$application_id = $application_id;
                    self::$secret_key = $secret_key;
                    self::$version = $version;

                } else {

                    throw new BackendlessException( 'Version cannot be null' );

                }
            } else {

                throw new BackendlessException( 'Secret key cannot be null' );

            }
        } else {

            throw new BackendlessException( 'Application id cannot be null' );

        }
        
        self::phpEnviromentInit();
        
    }
    
    public static function setUrl( $api_url ) {
        
        self::$url = $api_url;
        
    }
    
    public static function getUrl() {
        
        return self::$url;
        
    }
    
    public static function getApplicationId() {
        
        if( !isset( self::$application_id ) ) {
            
            throw new BackendlessException( 'Backendless application was not initialized' );
            
        } else {
            
            return self::$application_id;
            
        }
        
    }

    public static function getSecretKey() {
        
        if( !isset( self::$secret_key ) ) {
            
            throw new BackendlessException( 'Backendless application was not initialized' );
            
        } else {
            
            return self::$secret_key;
            
        }
    }

    public static function getVersion() {
        
        if( !isset( self::$version) ) {
            
            throw new BackendlessException( 'Backendless application was not initialized' );
            
        } else {
            
            return self::$version;
            
        }
        
    }

    public static function ignoreMapException() {
        
        self::$ignore_map_exception = true;
        
    }
    
    public static function mapTableToClass( $table_name, $class_name ) {

        if ( ! class_exists( $class_name ) && self::$ignore_map_exception == false ) {

            throw new BackendlessException( 'Class ' . $class_name . ' not available. Please verify class name and verify that the set fully qualified name of the class with a namespace.'
                                . ' Also make sure that the class or namespace for class is added to autoloading using the method BackendlessAutoloader::addNamespace ( $namespace, $path )' );
            
        }
        
        self::$classes_map[ $table_name ] = [ 'class_name' => $class_name ];
        
    }
    
    public static function getModelByClass( $class_name ) {
        
        if( isset( self::$classes_map[ $class_name ] ) ) {
            
            return self::$classes_map[ $class_name ][ 'class_name' ];
            
        }
        
        return null;
        
    }
    
    public static function setInvocationContext(  $invocation_context ) {
        
        self::$InvocationContext = $invocation_context;
        
    }
    
    public static function switchOnBlMode() {
        
        self::$sdk_mode_bl = true;
        
    }
    
    public static function switchOffBlMode() {
        
        self::$sdk_mode_bl = false;
        
    }
    
    public static function isBlMode() {
        
        return self::$sdk_mode_bl;
        
    }
    
     protected static function  phpEnviromentInit() {
          
        //set default timezone need for WIN and OS X
        date_default_timezone_set( 'UTC' );
        
        // check if available openssl for use https
        if( ! extension_loaded( 'openssl') ) {
            
            self::$url = preg_replace( '/^http:\/\/|https:\/\/(.*)$/', 'http://${1}', self::$url );
            
        }
          
    }
    
    public static function devMode() {
        
        define( 'DEV_MODE', true );
        Log::init();
        
    }
    
    
} Backendless::staticConstruct();
