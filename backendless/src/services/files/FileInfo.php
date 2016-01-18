<?php
namespace backendless\services\files;

class FileInfo
{
    
    private $name;
    private $create_on;
    private $public_url;
    private $url;
    private $size;

    public function getName() {

      return $this->name;

    }

    public function setName( $name ) {

        $this->name = $name;
        return $this;        

    }

    public function getCreatedOn() {
        
      return $this->createOn/1000;
      
    }

    public function setCreatedOn( $create_on ) {
      
        $this->create_on = $create_on*1000;
        return $this;
    }

    public function getPublicUrl() {

        return $this->public_url;
        
    }

    public function setPublicUrl( $public_url ) {
      
        $this->public_url = $public_url;
        return $this;
    
    }

    public function getURL() {
        
        return $this->url;
        
    }

    public function setURL( $url ) {
        
        $this->url = $url;
        return $this;
      
    }

    public function getSize() {
        
      return $this->size;
      
    }

    public function setSize( $size ) {
        
        $this->size;
      
    }
}
