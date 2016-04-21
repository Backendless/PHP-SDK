<?php
namespace backendless\lib;

use backendless\lib\Log;
use backendless\Backendless;

class HttpRequest
{
    protected $target_url;
    protected $request_headers;
    
    protected $response_headers;
    protected $response;
    
    protected $response_code;
    protected $response_status;

    public function __construct() {
        
        $this->headers = [];
        
    }
    
    private function resetResponse() {
        
        $this->response = null;
        $this->response_code = null;
        $this->response_status = null;
        $this->response_headers = null;
        
    }

    public function setTargetUrl( $target ) {
        
        $target = trim( $target );
        
        if( ! preg_match( "/http:\/\/|https:\/\//", $target, $matches ) ) {
            
            $target = 'http://' . $target;
            
        }
        
        $this->target_url = $target;
        
        return $this;
        
    }
    
    public function setHeader( $name, $val ) {
      
        $this->request_headers[$name] = $val;
        
        return $this;
        
    }
    
    public function request( $content, $method = 'POST' ) {
        
        $this->resetResponse();
        
        if( Backendless::isBlMode() ) {
            
            $this->setHeader( 'application-type', 'BL' );
            
        }
        
        if( $content !== 'null' ) {
            
            $this->request_headers[ 'Content-length' ] = strlen( $content );    
                                
        }
        
        $headers = [];
        
        if( is_array( $this->request_headers ) ) {
            
            $headers = array_map( 
                                    function ( $v, $k ) { 
                                        return sprintf( "%s: %s", $k, $v ); 
                                    }, 

                                    $this->request_headers,
                                    array_keys( $this->request_headers )

                                );

        }
        
        $http = [
            
            'ignore_errors' => true,
            'method' => $method,
            'header' => implode("\r\n", $headers), 
            'timeout' => 10

        ];                                
        
        if( $content !== 'null' ) {
            
           $http['content'] = $content;
           
        }
        
        $context = stream_context_create(
                                            [
                                                'http' => $http,
                                                
                                                'ssl' => [
                                                            'verify_peer' => false,                                                                             //1
                                                            'allow_self_signed' => false,
                                                            //'cafile' => '/etc/ssl/certs/ca-certificates.crt', // <-- EDIT FOR NON-DEBIAN/UBUNTU SYSTEMS       //1    

                                                         ]
                                            ]
                                        );
        
        
        $this->beforeRequest( $http, $method );
        
        $this->response = file_get_contents( $this->target_url, false, $context );
        
        $this->afterRequest();

        $this->response_headers = $http_response_header;
        
    }
    
    public function getResponseCode(){

        if( isset( $this->response_code ) ) {
            
            return $this->response_code;
            
        } else{
            
            $this->parseResponseCode();
            
        }
        
        return $this->response_code;
        
    }
   
    public function getResponseStatus() {
        
        if( isset( $this->response_status ) ) {
            
            return $this->response_status;
            
        } else{
            
            $this->parseResponseCode();
        }
        
        return $this->response_status;
        
    }
    
    protected function parseResponseCode() {
        
        foreach ( $this->response_headers as $key => $header ) {
            
            if ( strpos( $header, 'HTTP' ) !== false ) {
                
                list( , $this->response_code, $this->response_status) = explode( ' ', $header );
                
            }
        }
        
    }
    
    public function getResponseHeader( $header ) {
        
        if( isset( $this->response_headers ) ) {
            
            foreach ( $this->response_headers as $key => $response_header ) {
            
                if ( stripos( $response_header, $header ) !== false ) {

                    list( $headername, $headervalue ) = explode( ":", $response_header );
                    return trim( $headervalue );

                }
            }
        }
        
        return null;
        
    }
    
    public function getResponse() {
        
        return $this->response;
        
    }
    
    private function beforeRequest( &$http, $method ) {
        
        if( defined( 'DEV_MODE' ) ) {
            
            Log::writeInfo( '---------------------------------------------------------------------------------------------', 'file' );
            Log::writeInfo( 'Request to: ' . $this->target_url , 'file' );
            Log::writeInfo( 'Method: ' . $method , 'file' );
            
            if( isset( $http[ 'content' ] ) ) {
                
                Log::writeInfo( 'Request content: \'' .  $http[ 'content' ] . '\'', 'file' );
                
            }
        
            echo '\n';
            Log::writeInfo( 'Send request...', 'console' );
            
        }
    }
    
    private function afterRequest() {
        
        if( defined( 'DEV_MODE' ) ) {
        
            Log::writeInfo( 'Server response: ' . $this->getResponse(), 'file' );
            Log::writeInfo( '---------------------------------------------------------------------------------------------', 'file' );
            
            echo 'n';
            Log::writeInfo( 'Processing results', 'console' );
            
        }
        
    }
  
}
