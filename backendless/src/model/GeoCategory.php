<?php
namespace backendless\model;

class GeoCategory
{

    private $objectId;
    private $name;
    private $size;
    
    public function __construct( ) {
        
    }

    public function getId() {
        
        return $this->objectId;
    }
 

    public function getName() {
    
        return $this->name;
    }
 

    public function getSize() {
        
        return $this->size;
    }
 
    public function setName( $name ) {
    
        $this->name = $name;
    }
    
}
 