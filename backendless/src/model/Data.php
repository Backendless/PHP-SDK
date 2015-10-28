<?php
namespace backendless\model;

use Exception;

class Data {
    
    protected $data = [];
   
    public function __construct() {
        
    }
    
    public function __set( $key, $value ) {
        
        $this->data[ $key ] = $value;
       
    }
    
    public function __get( $key ) {
        
        if( isset( $this->data[ $key ] ) ) {
            
            return $this->data[ $key ];
            
        }
        
        return null;
        
    }
    
    protected function setData( $data ) {
        
        $this->data = $data;
        
    }
    
    protected function getData() {
        
        return $this->data;
        
    }
    
    protected function unsetData() {
        
        $this->data = [];
        
    }
    
    public function setProperty( $key, $value) {
        
        $this->data[ $key ] = $value;
        
    }
    
    public function getProperty( $key ) {
        
        if( isset( $this->data[ $key ] ) ) {
            
            return $this->data[ $key ];
            
        }
        
        return null;
        
    }
    
    public function __call( $name, $arguments ) {
        
        $action_name = substr( $name, 0, 3);
        $property = substr( $name, 3);
        
        $property = lcfirst($property);
        
        switch ( $action_name ){
            
            case 'set': $this->setProperty( $property, $arguments[0] ); break;
            case 'get': return $this->getProperty( $property );
            default :
                        $action_name = substr( $name, 0, 5);
                        $property = substr( $name, 5);
        
                        $property = lcfirst($property);
        
                        if( $action_name === 'unset') {

                            if( isset($this->data[$property]) ) {

                                unset($this->data[$property]);

                            }

                        } else {
                
                            throw  new Exception("Called undefined function $name.");
                        }
                
        }
        

      
        
    }
    
    public function setProperties( $data_array ) {
        
        $this->data = $data_array;
        
    }

    public function putProperties( $data_array ) {

        $this->setData(array_merge($this->getData(), $data_array));
        
    }
    
    public function getProperties( ) {

        return $this->data;
        
    }
    
    public function exclude( $exclude ) {
        
        $class = __CLASS__;
        
        $obj = new $class();
        
        $obj->setData($this->getData());
        
        if( !is_array($exclude) ){
            
            $exclude = [$exclude];
        }
        
        foreach( $exclude as $property ){
        
            if( isset( $obj->data[$property] ) ) {

                unset( $obj->data[$property] );

            }
        }
                 
        return $obj;
    
    }
    
    public function clearProperties( ) {

        $this->data = [];
        
    }
    
    
    public function getMetaInfo(){
        
        if( isset( $this->data['__meta'] ) ) {
            
            return $this->data['__meta'];
            
        }else{
            
            return null;
            
        }
        
    }

}
