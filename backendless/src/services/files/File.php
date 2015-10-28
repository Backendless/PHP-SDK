<?php
namespace backendless\services\files;

use Exception;

class File {

    //upload 
    private $path;
    //upload binary
    private $file_name;
    private $file_content;
    private $overwrite= false;
    
    public function setPath( $path ) {
        
        $this->path = $path;
        
    }
    
    public function getPath() {
        
        return $this->path;
    }
    
    public function validate() {
        
        if( !file_exists($this->path) ) {
            
            throw new Exception("File with file path {$this->path} does not exist.");
        
        }
        
    }
    
    public function setFileName( $file_name ) {
        
        $this->file_name  = $file_name;
        
    }
    
    
    public function getFileName() {
        
        return $this->file_name;
        
    }
    
    public function setFileContent($content) {
        
        $this->file_content = $content;
        
    }
    
    public function getFileContent(){
        
        return $this->file_content;
        
    }
    
    public function overwrite( $overwrite = false ) {
        
        $this->overwrite = $overwrite;
        
    }
    
    public function isOverwrite( ) {
        
        return $this->overwrite;
        
    }
   
}  