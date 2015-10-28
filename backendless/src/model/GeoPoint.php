<?php
namespace backendless\model;

class GeoPoint{

    protected $latitude;
    protected $longitude;
    protected $categories = [];
    protected $metadata = [];
    protected $objectId;
        
    public function  __construct() {
        
    }
    
    public function getObjectId(){
        
        return $this->objectId;
        
    }
    
    public function setObjectId( $object_id){
        
        $this->objectId = $object_id;
        return $this;        
        
    }
    
    public function getLatitude(){
        
        return $this->latitude;
        
    }
    
    public function setLatitude( $latitude ){
        
        $this->latitude = $latitude;
        return $this;        
        
    }
    
    public function getLongitude(){
        
        return $this->longitude;
        
    }
    
    public function setlongitude( $longitude ){
        
        $this->longitude = $longitude;
        return $this;        
        
    }
    
    public function getCategories(){
        
        return $this->categories;
        
    }
    
    public function setCategories( $categories ){
        
        if( is_array( $categories ) ) {
            
            $this->categories = $categories;
            
        }else{
            
            $this->categories[] = $categories;
            
        }
        
        return $this;        
        
    }
    
    public function addCategory( $category ){
        
        if( ! in_array($category, $this->categories) ) {
            
            $this->categories[] = $category;
            
        }
        
        return $this;
        
    }
    
    public function getMetadata(){
        
        return $this->metadata;
        
    }
    
    public function setMetadata( $metadata ){
        
        $this->metadata = $metadata;
        return $this;        
        
    }
    
    public function addMetadata( $key, $data ){
        
        $this->metadata[$key] = $data;
        return $this;
        
    }
    
}