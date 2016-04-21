<?php
namespace backendless\services;

use backendless\services\files\File;
use backendless\Backendless;
use backendless\lib\HttpRequest;
use backendless\lib\RequestBuilder;
use backendless\model\BackendlessCollection;
use backendless\exception\BackendlessException;

class Files
{
    
    protected static $instance;
    
    private static $APP_ID_KEY = 'application-id';
    private static $SECRET_KEY = 'secret-key';
    private static $VERSION = 'AppVersion';
    
    private function __construct() { }

    public static function getInstance() {
        
        if( !isset( self::$instance ) ) {
            
            self::$instance = new Files();
            
        }
        
        return self::$instance;
        
    }
    
    public function upload( $file, $remote_path = null ) {
        
        if( !is_object( $file ) ) {
            
            $path = $file;
            $file = new File();
            $file->setPath( $path );
            
        }
        
        $file->validate();
        
        $target = Backendless::getUrl() . '/' . Backendless::getApplicationId(). '/' . Backendless::getVersion() . '/files'; 
            
        if( $remote_path !== null ) {
            
            $remote_path = trim( $remote_path," \t\n\r\0\x0B\\" );
            $remote_path = str_replace( "\\", "/", $remote_path );
            
            $target .= '/' . $remote_path;

        }
        
        $target .= '/' . $file->getFileName();
                
        $http_request = new HttpRequest();

        $multipart_boundary = "------BackendlessFormBoundary" . md5( uniqid() ) . microtime( true );

        $file_contents = file_get_contents($file->getPath());

        $content =   "--". $multipart_boundary ."\r\n".
                     "Content-Disposition: form-data; name=\"model-file\"; filename=\"" . $file->getFileName() . "\"\r\n".
                     "Content-Type: application/json\r\n\r\n".
                     $file_contents."\r\n";
       
        $content .= "--".$multipart_boundary."--\r\n";
        
        RequestBuilder::addUserTokenHeader( $http_request );
       
        $http_request->setTargetUrl( $target )
                     ->setHeader( self::$APP_ID_KEY, Backendless::getApplicationId() )
                     ->setHeader( self::$SECRET_KEY, Backendless::getSecretKey() )
                     ->setHeader( self::$VERSION, Backendless::getVersion() )
                     ->setHeader( 'Content-type', ' multipart/form-data; boundary=' .$multipart_boundary )
                     ->request( $content  );
        
        
        if( $http_request->getResponseCode() != 200 ) {
            
            $error =  json_decode( $http_request->getResponse(), true );
            
            if( !isset( $error[ 'message' ] ) ) {
                
                throw new Exception( 'API responce ' .$http_request->getResponseStatus() . ' ' . $http_request->getResponseCode() . $http_request->getResponse() );
                
            } else {
                
                throw new Exception( $error[ 'message' ], $error[ 'code' ] );
                
            }

        }
        
        return json_decode( $http_request->getResponse(), true );
        
    }
    
    public function saveFile( $file_path_name, $file_content = '',  $overwrite = false ) {
        
        if( is_object( $file_path_name ) ) {
            
            $overwrite = $file_path_name->getOverwrite();
            $file_content = $file_path_name->getFileContent();
            $file_path_name = $file_path_name->getPathWithName();
            
        }
        
        $file_path_name = trim( $file_path_name, " \t\n\r\0\x0B/" );
         
        $target = Backendless::getUrl() . "/" . Backendless::getApplicationId(). "/" . Backendless::getVersion() . "/files/binary/" . $file_path_name;
        
        if( $overwrite ) {  $target .= "?overwrite=true";  }

        if( is_array( $file_content ) ) {
            
            $file_content = implode( $file_content );
            
        }
        
        $file_content = base64_encode( $file_content );
        
        $http_request = new HttpRequest();

        $multipart_boundary ="------BackendlessFormBoundary" . md5( uniqid() ) . microtime( true );

        $content =   "--" . $multipart_boundary . "\r\n".
                     "Content-Disposition: form-data; name=\"model-file\"; filename=\"" . basename( $file_path_name ) . "\"\r\n".
                     "Content-Type: text/plain\r\n\r\n".
                     $file_content."\r\n";
       
        $content .= "--" . $multipart_boundary . "--\r\n";
        
        RequestBuilder::addUserTokenHeader( $http_request );
        
        $http_request->setTargetUrl( $target )
                     ->setHeader( self::$APP_ID_KEY, Backendless::getApplicationId() )
                     ->setHeader( self::$SECRET_KEY, Backendless::getSecretKey() )
                     ->setHeader( self::$VERSION, Backendless::getVersion() )
                     ->setHeader( 'Content-type', 'multipart/form-data; boundary=' . $multipart_boundary )
                     ->setHeader( 'application-type:', 'REST' )
                     ->request( $content  );
        
        
        if( $http_request->getResponseCode() != 200 ) {
            
            $error =  json_decode( $http_request->getResponse(), true );
            
            if( !isset( $error[ 'message' ] ) ) {
                
                throw new Exception( 'API responce ' . $http_request->getResponseStatus() . ' ' . $http_request->getResponseCode() . $http_request->getResponse() );
                
            }else{
                
                throw new Exception( $error[ 'message' ], $error[ 'code' ] );
                
            }

        }
        
        return json_decode( $http_request->getResponse(), true );
        
    }
    
    public function download( $file_path ) {
                    
        $file_path = trim($file_path);
        $file_path = trim($file_path, "\\\/");
        
        $url_part = Backendless::getUrl() . "/" . Backendless::getApplicationId() . "/" . Backendless::getVersion();
        
        return RequestBuilder::Get( $url_part . '/files/' . $file_path);
        
    }
    
    public function remove( $file_path ) {
        
        $file_path = trim($file_path);
        $file_path = trim($file_path, "\\\/");
        
        return RequestBuilder::doRequest('files', $file_path, '', 'DELETE');
        
    }
    
    public function removeDirectory( $directory_path ) {
        
        $directory_path = trim( $directory_path );
        $directory_path = trim($directory_path, "\\\/");
        
        return RequestBuilder::doRequest('files', $directory_path, '', 'DELETE');
        
    }
    
    public function renameFile( $old_path_mame, $new_name) {
        
        $file_path = trim( $old_path_mame );
        $file_path = trim( $old_path_mame, "\\\/" );
        $new_name = trim( $new_name );
        $new_name = trim( $new_name, "\\\/" );
        
        $url_part = Backendless::getUrl() . "/" . Backendless::getApplicationId() . "/" . Backendless::getVersion();
        
        $request_body = [
            
            'oldPathName' => $old_path_mame,
            'newName' => $new_name
            
        ];
        
        return RequestBuilder::doRequest( 'files', 'rename', $request_body, 'PUT' );
        
        
    }
    
    public function copyFile( $source_path_name, $target_path ) {
        
        $source_path_name = trim( $source_path_name );
        $source_path_name = trim( $source_path_name, "\\\/" );
        $target_path = trim( $target_path );
        $target_path = trim( $target_path, "\\\/" );
        
        $url_part = Backendless::getUrl() . "/" . Backendless::getApplicationId() . "/" . Backendless::getVersion();
        
        $request_body = [
            
            'sourcePath' => $source_path_name,
            'targetPath' => $target_path
            
        ];
        
        return RequestBuilder::doRequest( 'files', 'copy', $request_body, 'PUT' );
        
    }
    
    public function moveFile( $source_path_name, $target_path ) {
        
        $source_path_name = trim( $source_path_name );
        $source_path_name = trim( $source_path_name, "\\\/" );
        $target_path = trim( $target_path );
        $target_path = trim( $target_path, "\\\/" );
        
        $request_body = [
            
            'sourcePath' => $source_path_name,
            'targetPath' => $target_path
            
        ];
        
        return RequestBuilder::doRequest( 'files', 'move', $request_body, 'PUT' );
        
    } 
    
    public function listing( $path, $pattern = null, $recursive = false, $page_size = null, $offset = null ) {
        
        $path = trim( $path );
        $path = trim( $path, "\\\/" );
        $path = "/" . $path;
        
        $pattern = trim( $pattern );
        
        $url = "" ;
        $url = $path;
        
        $query_data = [];
        
        if(  $pattern != null ) {
            
            $query_data[ 'pattern' ] = $pattern;
            
        }
        
        if(  $recursive != false ) {
            
            $query_data[ 'sub' ] = 'true';
            
        }
        
        if(  $page_size != null && $offset != null ) {

            $query_data[ 'pagesize' ] = $page_size;
            $query_data[ 'offset' ] = $offset;
            
            
        }
        
        $query = http_build_query( $query_data);
        
        if( !empty( $query ) ) {
            
            $url .= '?' . $query;
            
        }

        return new BackendlessCollection( RequestBuilder::doRequest( 'files', $url, '', 'GET' ) );
        
    }
    
    public function exists( $file_path ) {

        $file_path = trim( $file_path );
        $file_path = trim( $file_path, "\\\/" );
        
        if( empty( $file_path ) ) {
            
            throw new BackendlessException( 'File path variable empty' );
            
        }
        
        return RequestBuilder::doRequest( 'files/exists', $file_path, '', 'GET' );
        
    }

}
