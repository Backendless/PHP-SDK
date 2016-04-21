<?php
namespace backendless\services\files;

use backendless\exception\BackendlessException;

class File {

    private $path;
    private $file_name;
    private $file_content;
    private $overwrite = false;
    
    public function setPath( $path ) {
        
        $this->path = $path;
        return $this;
        
    }
    
    public function getPath() {
        
        return $this->path;
    }
    
    public function validate() {
        
        if( ! file_exists( $this->path ) ) {
            
            throw new BackendlessException( 'File with file path {$this->path} does not exist.' );
        
        }
        
    }
    
    public function setFileName( $file_name ) {
        
        $this->file_name = $file_name;
        return $this;
        
    }
    
    
    public function getFileName() {
        
        if( empty( $this->file_name ) ) {
            
            $this->file_name = basename( $this->path );
            
        }
        
        return $this->file_name;
        
    }
    
    public function setFileContent( $content ) {
        
        $this->file_content = $content;
        return $this;
        
    }
    
    public function getFileContent(){
        
        return $this->file_content;
        
    }
    
    public function overwrite( $overwrite = false ) {
        
        $this->overwrite = $overwrite;
        return $this;
        
    }
    
    public function getOverwrite() {
        
        return $this->overwrite;
        
    }
    
    public function isOverwrite( ) {
        
        return $this->overwrite;
        
    }
    
    public function getPathWithName() {
     
        $path_string = '';
        
        if ( isset( $this->path ) ) {
            
            $path_string = trim( $this->path, $charlist = '/' );
            $path_string .= '/';
            
        }
        
        return $path_string .=  trim( $this->file_name, $charlist = '/' );
        
    }
   
}  
