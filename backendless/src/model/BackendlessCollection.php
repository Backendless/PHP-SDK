<?php
namespace backendless\model;

use backendless\Backendless;
use backendless\lib\RequestBuilder;
use ReflectionClass;
use backendless\exception\BackendlessException;
use stdClass;

class BackendlessCollection {
    
    private $data;

    private $stored_next_page_link;
            

    public function __construct( $data ) {
        
        $this->data = $data;
        
    }
    
    static public function prepareSingleItem( $data_item, $convert_type ) {
     
        $collection_item = new BackendlessCollection( $data_item );
        
            switch ( $convert_type ) {
                
                case "std_class":   return $collection_item->convertToStdClasses();
                case "user_class":  return $collection_item->convertToUserClasses();      
                case "array":       return $collection_item->convertToArray();
                    
            }
        
    }
    
    public function getAsObjects( ) {
        
        return $this->convertToStdClasses();
        
    }
    
    public function getAsArrays() {
        
        return $this->convertToArray();
        
    }
    
    public function getAsClasses() {
        
        return $this->convertToUserClasses();
        
    }  
    
    public function getAsObject( ) {
        
        return $this->convertToStdClasses();
        
    }
    
    public function getAsArray() {
        
        return $this->convertToArray();
        
    }
    
    public function getAsClass() {
        
        return $this->convertToUserClasses();
        
    }  
    
    public function pageSize() { 
        
        if( isset( $this->data["data"] ) ) {
            
            return count($this->data['data']);
            
        } else{
            
            return count( $this->data );
            
        }
        
    }
    
    public function totalObjectsCount() {
        
        if( isset( $this->data["data"] ) ) {
            
            return $this->data['totalObjects'];
            
        } else{
            
            return count( $this->data );
            
        }
        
    } 
    
    public function loadPrevPage() {
        
        if( isset( $this->data["nextPage"] ) || isset( $this->stored_next_page_link ) ) {
            
            $step_value = 0;
            
            if( isset( $this->data["nextPage"] ) ) {
                
                $page_link = $this->data["nextPage"];
                $step_value = 2;
                
            } else {
                
                $page_link = $this->stored_next_page_link;
                $step_value = 1;
                
            }
            
            $matches = [];
            
            preg_match( "/^.*pageSize=(\d+)&offset=(\d+)(.*)$/", $page_link, $matches );
            
            $page_size = $matches[1];
            $offset = $matches[2];
            
            if( $offset > 0) {
                
                $new_offset = $offset - ( $page_size * $step_value );
                
                if( $new_offset < 0 ) {
                    
                    if( isset( $this->data["data"]) ) {
            
                        $this->data = [];
                        return;
            
                    }
                    
                }
                
                $prev_page_link = preg_replace('/^(.*&offset=)(\d+)(.*)$/', '${1}' . $new_offset . '${3}', $page_link );       
                
                $this->data = $this->retrivePageByUrl( $prev_page_link );
                
                if( isset( $this->data["nextPage"] ) ) {
                
                    $this->stored_next_page_link = $this->data["nextPage"]; 
                
                }
                
            }
            
        }elseif( isset( $this->data["data"]) ) {
            
            $this->data = [];
            
        }
       
    }
    
    public function loadNextPage() {
        
        if( isset( $this->data["nextPage"] ) || isset( $this->stored_next_page_link ) ) {
            
            if( isset( $this->data["nextPage"] ) ) {
                
                $page_link = $this->data["nextPage"];
                
            } else {
                
                $page_link = $this->stored_next_page_link;
                
            }
            
            $this->data = $this->retrivePageByUrl( $page_link );
            
            if( isset( $this->data["nextPage"] ) ) {
                
                $this->stored_next_page_link = $this->data["nextPage"]; 
            }
            
        }elseif( isset ($this->data["data"] ) ) {
            
            $this->data = [];
            
        }
        
    }
    
    public function loadPage( $page_size, $offset ) {
        
        if( $offset < 0) {
            
            throw new BackendlessException( 'Argument $offset cannot be less than 0.' );
        
        }
        
        if( $page_size < 0) {
            
            throw new BackendlessException( 'Argument $page_size cannot be less than 0.' );
        
        }
        
        if( isset( $this->data["nextPage"] ) || isset( $this->stored_next_page_link ) ) {

            if( isset( $this->data["nextPage"] ) ) {

                $page_link = $this->data["nextPage"];

            } else {

                $page_link = $this->stored_next_page_link;

            }

            $page_link = preg_replace('/^(.*pageSize=)(\d+)(&offset=)(\d+)(.*)$/', '${1}' . $page_size . '${3}'. $offset . '${5}', $page_link );       

            $this->data = $this->retrivePageByUrl( $page_link );
            
            if( isset( $this->data["nextPage"] ) ) {
                
                $this->stored_next_page_link = $this->data["nextPage"]; 
            }


        }elseif( isset( $this->data["data"] ) ) {

            $this->data = [];

        }
        
    }
    
    
    // internal logic
    
    protected function convertToStdClasses() {

        if( isset( $this->data['data'] ) ) {
            
            $collection = [];
            
             foreach ( $this->data['data'] as $index => $collection_item ) {

                $collection[ $index ] = $this->convertToStdClassesItem( $collection_item );
                 
             }
             
             return $collection;
            
        } else {
            
            return $this->convertToStdClassesItem( $this->data );
        }
        
    }
    
    protected function convertToUserClasses() {

        if( isset( $this->data['data']) ) {
            
             $collection = [];
            
             foreach ( $this->data['data'] as $index => $collection_item ) {

                $collection[ $index ] = $this->convertToUserClassesItem( $collection_item );
                 
             }
             
             return $collection;
            
        } else {
            
            return $this->convertToUserClassesItem( $this->data );
        }
        
    }
    
    protected function convertToArray() {
        
        if( isset( $this->data['data']) ) {
            
             $collection = [];
            
             foreach ( $this->data['data'] as $index => $collection_item ) {

                $collection[ $index ] = $collection_item;  
                $this->normalizeArray( $collection[ $index ] );
                 
             }
             
             return $collection;
            
        } else {
            
            $item = $this->data;
            $this->normalizeArray( $item );
            
            return $item;
        }
        
    }
    
    protected function convertToStdClassesItem( $data ) {
        
        if( isset( $data["___class"] ) ) {

            if( $data["___class"] === "GeoPoint" ) {   // if "___class" == GEOPINT need create geopoint

                return $this->fillGeoPoint( $data, "std_class" );

            }
            
            $obj = new stdClass();
            $skip_step = false;
            
        } else {
            
            // если не задан класс для маппинга модели она остается мултимассивом + рекурсивно проверяются его ключи.
            if( is_array( $data ) ) {

                foreach ( $data as $prop_name => $prop_val ) {

                    $data[$prop_name] = $this->convertToStdClassesItem( $prop_val );
                }
            }
            
            return $data; 
            
        }
        
        foreach ( $data as $property_name => $property_value  ) {

            if( is_array( $property_value ) ) {
                
                if( isset( $property_value["___class"] ) ) {
                    
                    $obj->{$property_name} = $this->convertToStdClassesItem( $property_value );
                    continue;
                }
                
                if( isset( $property_value[0] ) ) {  // check is relation one to many
                    
                    foreach ( $property_value as $prop_index => $prop_val) {
                        
                        if( isset( $prop_val["___class"] ) ) {
                            
                            $obj->{$property_name}[$prop_index] = $this->convertToStdClassesItem( $prop_val );
                            $skip_step = true;
                            
                        } else {
                            
                            $obj->{$property_name}[$prop_index] = $prop_val;
                            
                        }
                        
                    }
                    
                    if( $skip_step ) { $skip_step = false; continue;}
                    
                }
                
                $obj->{$property_name} = $property_value;
                continue;
            }
            
            if( $property_name != "___class" ) {
                
                $obj->{$property_name} = $property_value;
                
            } else {
                
              $obj->table_name = $property_value;
                
            }
        }
        
        return $obj;
        
    }
    
    //  create geopoint set data to class create and set related objects. 
    protected function fillGeoPoint( $data, $mode ) {

        $geo_point = new GeoPoint();
        
        $props = ( new ReflectionClass( $geo_point ) )->getProperties();

        foreach ( $props as $prop ) {

            $prop->setAccessible( true );
            
            if( isset( $data[ $prop->getName() ] ) ) {
                
                $prop->setValue( $geo_point, $data[ $prop->getName() ] );
                    
                if( $prop->getName() == "metadata" ) {

                        $metadata = $data[ $prop->getName() ];
                        
                        switch ( $mode ) {
                
                            case "std_class": foreach ( $metadata as $index => $meta_val ) {
                                
                                                $metadata[ $index ] = $this->convertToStdClassesItem( $meta_val );
                                                
                                                
                                              } unset( $data["___class"] ); break;

                            case "array":     foreach ( $metadata as $index => $meta_val ) {
                                
                                                $metadata[ $index ] = $this->normalizeArray( $meta_val );
                                                
                                              } break;
                                              
                            case "user_class": foreach ( $metadata as $index => $meta_val ) {
                                
                                                    $metadata[ $index ] = $this->convertToUserClassesItem( $meta_val );
                                                
                                                } break;
                    
                       }
                       
                       $prop->setValue( $geo_point, $metadata );
                    
                }
                
            }
            
            unset( $data[ $prop->getName() ] );
        }
        
        foreach ( $data as $key => $val) {

            $geo_point->{$key} = $val;

        }
        
        return $geo_point;
    }
    
    protected function convertToUserClassesItem( $data ) {
        
        $dont_set_class_for_mapping = false;
        
        if( isset( $data["___class"] ) ) {
            
            if( $data["___class"] === "GeoPoint" ) {   // if "___class" == GEOPINT need create geopoint

                return $this->fillGeoPoint( $data, "user_class" );

            }
            
            $class_name = Backendless::getModelByClass( $data["___class"] );

            if( $class_name != null ) {

                $obj = $this->getObjectByClass( $class_name );

            } else {

                $dont_set_class_for_mapping = true;
            }
            
        }else{
            
            $dont_set_class_for_mapping = true;
        }
        
        if( $dont_set_class_for_mapping === true) {
            // если не задан класс для маппинга модели она остается мултимассивом + рекурсивно проверяются его ключи.
            if( is_array( $data ) ) {

                foreach ( $data as $prop_name => $prop_val ) {

                    $data[$prop_name] = $this->convertToUserClassesItem( $prop_val );
                }
            }
            
            return $data; 
            
        }
            
        $props = (new ReflectionClass( $obj ) )->getProperties();

        foreach ( $props as $prop) {

            $prop->setAccessible(true);
            
            if( isset( $data[ $prop->getName() ] ) ) {
                
                if( !is_array( $data[ $prop->getName() ] ) ) {
                    
                    $prop->setValue( $obj, $data[ $prop->getName() ] );
                    
                }else{
                    
                    $prop->setValue( $obj, $this->prepareUserClassRelation( $data[ $prop->getName() ] ) );
                    
                }
                
            }
            
            unset( $data[ $prop->getName() ] );
        }
        
        // set undeclared in model properties
        $this->setUndeclaredProperties( $data, $obj );
        
        
        return $obj;
        
    }
    
    protected function setUndeclaredProperties( &$data, &$obj ) {
        
        foreach ( $data as $name => $val ) {

            if( !is_array( $data[ $name ] ) ) {
                    
                $obj->{$name} = $val;
                    
            } else {
                    
                $obj->{$name} = $this->prepareUserClassRelation( $data[ $name ] );
                    
            }

        }
        
    }
        
    protected function prepareUserClassRelation( $class_property ) {
        
        
        if( isset( $class_property["___class"] ) ) {
            
            if( $class_property["___class"] === "GeoPoint" ) {   // if "___class" == GEOPINT need create geopoint

                return $this->fillGeoPoint( $class_property, "user_class" );

            }
            
            return $this->convertToUserClassesItem( $class_property );
            
        }
        
        if( isset( $class_property[0] ) ) {
            
            foreach ( $class_property as $propery_index => $prop_value ) {
                
                if( isset( $prop_value["___class"] ) ) {
                    
                    if( $prop_value["___class"] === "GeoPoint" ) {   // if "___class" == GEOPINT need create geopoint

                        $class_property[$propery_index] =  $this->fillGeoPoint( $prop_value, "user_class" );

                    } else {
                    
                        $class_property[$propery_index] = $this->convertToUserClassesItem( $prop_value );
                        
                    }
                    
                }
                
            }
            
            return $class_property;
            
        }
        
    }
    
    protected function normalizeArray( &$data ) {
        

        if ( is_array( $data ) ) {
        
            if( isset( $data["___class"] ) ) {

                $data["table-name"] = $data["___class"];
                unset( $data["___class"] );
                
                if( $data["table-name"] == "GeoPoint" ) {
                            
                    $this->normalizeGeoPointInArray( $data );
                            
                }
                        

            }

            foreach ( $data as $property_name => $property_value  ) {

                if( is_array( $property_value ) ) {

                    if( isset($property_value["___class"]) ) {

                        $data[$property_name]["table-name"] = $property_value["___class"];
                        unset($data[$property_name]["___class"]);

                        $this->normalizeArray( $data[$property_name] );
                        
                        if( $data[$property_name]["table-name"] == "GeoPoint" ) {
                            
                            $this->normalizeGeoPointInArray( $data[$property_name] );
                            
                        }
                        

                    }
                    
                    if( isset( $property_value[0] ) || isset( $property_value["metadata"] ) ) {

                        foreach ( $data[$property_name] as $index=>$val ) {

                            if( is_array($data[$property_name][$index]) ) {
                                
                                $this->normalizeArray( $data[$property_name][$index] );
                                
                            }
                            
                        }

                    }


                }

            }
            
        }
        
    }
    
    protected function normalizeGeoPointInArray( &$point ) {

        if( isset( $point["metadata"] ) ) {
            
            if( is_array( $point["metadata"] ) ) {
        
                foreach ( $point["metadata"] as $index => $meta_val ) {

                    if( is_array( $point["metadata"][$index] ) ) {

                        $this->normalizeArray( $point["metadata"][$index] );
                        
                    }
                }
            }

        }
        
    }
    
    protected function getObjectByClass( $class_name ) {
        
        return new $class_name();
        
    }
    
    public function retrivePageByUrl( $url ) {

        return  RequestBuilder::doRequestByUrl( $url, null, 'GET' );
        
    }
    
}
