<?php
namespace backendless\model;

use backendless\exception\BackendlessException;

class Data {
   
    public function __construct() { }
    
    public function __set( $key, $value ) {
        
        $this->{ $key } = $value;
       
    }
    
    public function __get( $key ) {
        
        if( isset( $this->{ $key } ) ) {
            
        return $this->{ $key };
            
        }
        
        return null;
        
    }
    
    public function putProperties( $properties ) {
        
        if( is_array( $properties) ) {
            
            foreach ( $properties as $key=>$value ) {
                
                $this->{ $key } = $value;
                        
            }
            
        } else {
            
            throw new BackendlessException( '"putProperties" method argument "$properties" must be array' );
            
        }
        
    }

    public function setProperties( $properties ) {
        
        $props = ( new ReflectionClass( $this ) )->getProperties();

        foreach ( $props as $prop ) {

            $prop->setAccessible( true );
            unset( $this->{ $prop->getName() });
            
        }
        
        $object_vars = get_object_vars( $this );
        
        foreach ( $object_vars as $name => $val ) {

            unset( $this->{ $name });
            
        }
                
        if( is_array( $properties) ) {
            
            foreach ( $properties as $key => $value ) {
                
                $this->{ $key } = $value;
                        
            }
            
        } else {
            
            throw new BackendlessException( '"setProperties" method argument "$properties" must be array' );
            
        }
        
    }
    
    public function setProperty( $key, $value) {
        
        $this->{ $key } = $value;
        
    }
    
    public function getProperty( $key ) {
        
        if( isset( $this->{ $key } ) ) {
            
            return $this->{ $key };
            
        }
        
        return null;
        
    }
    
    public function getProperties() {
        
        $data_array = [];
        
        $props = ( new ReflectionClass( $this ) )->getProperties();

        foreach ( $props as $prop ) {

            $prop->setAccessible( true );
            $data_array[] = $prop->getValue();
            
        }
        
        $object_vars = get_object_vars( $this );
        
        return array_merge( $data_array, $object_vars );
        
    }
    
    public function __call( $name, $arguments ) {

         $action_name = substr( $name, 0, 3 );
         $property = substr( $name, 3 );

         $property = lcfirst( $property );
         
         switch ( $action_name ) {

             case 'set': $this->setProperty( $property, $arguments[ 1 ] ); break;

             case 'get': return $this->getProperty( $property );

             default :   $action_name = substr( $name, 0, 5 );
                         $property = substr( $name, 5 );

                         $property = lcfirst( $property );

                         if( $action_name === 'unset' ) {

                             if( isset( $this->{ $property } ) ) {

                                unset( $this->{ $property } );

                             }

                         } else {

                             throw  new BackendlessException( "Called undefined function $name." );

                         }

         }

     }

}
