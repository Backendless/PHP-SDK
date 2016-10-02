<?php
namespace backendless\services;

use backendless\Backendless;
use backendless\services\persistence\BackendlessDataQuery;
use backendless\lib\RequestBuilder;
use backendless\model\BackendlessCollection;
use ReflectionClass;
use ReflectionProperty;
use backendless\exception\BackendlessException;


class Persistence
{
    
    protected $table_name;
    protected $aliases = [
                            'BackendlessUser' => 'Users'
                         ];
    
    protected static $instance;
    
    private function __construct() { }

    public static function getInstance() {
        
        if( !isset( self::$instance ) ) {
            
            self::$instance = new Persistence();
            
        }
        
        return self::$instance;
        
    }
    
    public function of( $table_name ) {
        
        $this->table_name = $table_name;
        
        return $this;
        
    }
    
    public function save( $data ) {
       
        $data_array = $this->convertDataToArray( $data );
        
        $this->cheackTargetTable( $data_array['table'], __METHOD__ ); // if call method of() check target table and  table in data structure
        $this->unsetTableName(); //delete table name if user call mehod of()
        
        if( isset( $data_array['data']["objectId"] ) ) { //update
            
            return BackendlessCollection::prepareSingleItem( 
                                                             RequestBuilder::doRequest( 'data', $data_array['table'] . '/'. $data_array['data']['objectId'], $data_array['data'], 'PUT' ), 
                                                             $data_array["type"] 
                                                            );
                     
        } else {
        
            return BackendlessCollection::prepareSingleItem( 
                                                             RequestBuilder::doRequest( 'data', $data_array['table'], $data_array['data'] ), 
                                                             $data_array["type"] 
                                                           );
            
        }
            
    }
    
    public function update( $data ) {
        
        $data_array = $this->convertDataToArray( $data );
        
        if( !isset( $data_array['data']['objectId'] ) ) {
            
           throw new BackendlessException( 'Missing objectId property value for update' );
           
        }
        
        $this->cheackTargetTable( $data_array['table'], __METHOD__ ); // if call method of() check target table and  table in data structure
        $this->unsetTableName(); //delete table name if user call mehod of()
        
        return BackendlessCollection::prepareSingleItem( 
                                                         RequestBuilder::doRequest( 'data', $data_array['table'] . '/'. $data_array['data']['objectId'], $data_array['data'], 'PUT' ), 
                                                         $data_array["type"] 
                                                       );
    }
    
    public function updateBulk( $data, $condition ) {

        if( !isset( $condition ) || $condition == '' ) {
            
            throw new BackendlessException( 'Missing condition for bulk update' );
            
        }
        
        $table = $this->tryGetTableName();
        
        $this->cheackTargetTable( $table, __METHOD__ ); // if call method of() check target table and  table in data structure
        $this->unsetTableName();
        
        if( $table != null ) {
            
            $data[ 'table-name' ] = $table;
            
        }
                
        $data_array = $this->convertDataToArray( $data );
        
        $this->recursiveDeleteEmptyProps( $data_array['data'] );
        
        if( empty( $data_array['data'] ) || $data_array['data'] == null ) {
            
            throw new BackendlessException("Do not set new value of properties for updating.");
            
        }
        
        $condition = 'where=' . urlencode( $condition );
                  
        return RequestBuilder::doRequest( 'data/bulk', $data_array['table'] . '?' . $condition, $data_array['data'], 'PUT' );
        
    }
    
    public function remove( $data ) {
        
        $object_id = $this->extractObjectId( $data );
        
        if( $object_id == null || $object_id == "" ) {
            
            throw new BackendlessException( "Missing objectId for remove data", $code = null );
            
        }
        
        $this->cheackTargetTable( $this->extractObjectType( $data ), __METHOD__ ); // if call method of() check target table and  table in data structure
        
        return RequestBuilder::doRequest( 'data', $this->extractObjectType( $data ) . '/' . $object_id, [], 'DELETE' );
        
    }
    
    public function removeBulk( $condition ) {
        
        if( !isset( $condition ) || $condition == '' ) {
            
            throw new BackendlessException( "Missing condition for bulk remove" );
            
        }
        
        $condition = 'where=' . urlencode( $condition );
        
        return RequestBuilder::doRequest( 'data/bulk', $this->getTableName() . '?' . $condition, [], 'DELETE' );
        
    }
    
    public function findById( $id, $relations_depth = null ) {
        
        $object_id = null;
        
        if( is_string( $id ) ) {
            
            $object_id = $id;
            
        } else {
            
            $data_array = $this->convertDataToArray( $id );
            
            $this->cheackTargetTable( $data_array['table'] , __METHOD__ );
        
            if( isset( $data_array['data']['objectId'] ) ){
            
                $object_id = $data_array['data']['objectId'];
                
            }
        
        }
        
        if( $object_id == null ) {
            
            throw new BackendlessException("Missing objectId for find object by objectId");
            
        }
        
        $relations_depth = ( $relations_depth === null )? "" : "?relationsDepth={$relations_depth}";
        
        return ( new BackendlessCollection( RequestBuilder::doRequest( 'data', $this->getTableName() .'/'. $object_id . $relations_depth, null, 'GET') ) )->getAsClass();
        
    }
    
    public function findFirst( $relations_depth = null ) {
        
        $relations_depth = ( $relations_depth === null )? "" : "?relationsDepth={$relations_depth}";
      
        return ( new BackendlessCollection( RequestBuilder::doRequest( 'data', $this->getTableName() . '/first' . $relations_depth, null, 'GET') ) )->getAsClass();
                
    }
    
    public function findLast( $relations_depth = null ) {
        
        $relations_depth = ( $relations_depth === null )? "" : "?relationsDepth={$relations_depth}";
        
        return ( new BackendlessCollection( RequestBuilder::doRequest( 'data', $this->getTableName() . '/last' . $relations_depth, null, 'GET') ) )->getAsClass();
        
    }
    
    public function find( $data_query_or_relation_depth = null ) {
        
        if( is_a( $data_query_or_relation_depth, '\backendless\services\persistence\BackendlessDataQuery') ) {
            
            return new BackendlessCollection( RequestBuilder::doRequest( 'data', $this->getTableName() ."?".$data_query_or_relation_depth->buildUrlVars(), null, 'GET') );
                        
        } else {
            
            $relations_depth = $data_query_or_relation_depth;
            
            $relations_depth = ( $relations_depth === null )? "" : "?relationsDepth={$relations_depth}";
            
            return new BackendlessCollection( RequestBuilder::doRequest( 'data', $this->getTableName() . $relations_depth, null, 'GET') );
            
        }
        
    }
    
    public function loadRelations( $data, $relations ) {
        
        if( !isset($relations) || $relations == '') {
            
            throw new BackendlessException("Missing relations  for load relations");
            
        }
        
        if( !is_array( $relations ) ) {
            
            throw new BackendlessException("Relations option must contain array of relations");
            
        }
        
        $object_id = $this->extractObjectId( $data );
        
        if( $object_id == null ) {
            
            throw new BackendlessException('Missing objectId for load relations, data structure must be saved and contained in backendless storage before load relations');
            
        }
        
        $query = new BackendlessDataQuery();
        
        $query->setRelated( $relations )->setWhereClause( "objectId='{$object_id}'" );

        return $this->find( $query );
        
    }
    
    public function describe( $entity_name ) {
        
        return RequestBuilder::doRequest( 'data', $entity_name . "/properties", null, 'GET');
        
    }
    
    
    // internal logic 
    
    protected function getTableName( ) {
        
        if( empty( $this->table_name ) ) {
            
            throw new BackendlessException('It is impossible get entity name, use the method "..->of("entity_name")->..." to set a specific entity.', $code = null   );
        
        }
        
        $table_name = $this->table_name;
        
        $this->unsetTableName();
        
        return $table_name;
                
    }
    
    protected function tryGetTableName( ) {
        
        if( empty( $this->table_name ) ) {
            
            return null;
            
        }

        $table_name = $this->table_name;
        
        $this->unsetTableName();
        
        return $table_name;
                
    }
    
    protected function readTableName( ) {
        
        if( empty( $this->table_name ) ) {
            
            return null;
            
        }
        
        return $this->table_name;
                
    }
    
    protected function unsetTableName(){
        
        unset( $this->table_name );
        
    }
    
    protected function cheackTargetTable( $object_type, $method_name ) {
        
        $declared_table = $this->readTableName();
        
        if( $declared_table != null ) {
            
            if( array_key_exists( $object_type, $this->aliases ) ) {
                
                $object_type = $this->aliases[ $object_type ];
                
            }
            
            if( $declared_table != $object_type ) {
                
                $method_name = explode( "::", $method_name )[1];
                
                throw new BackendlessException( "Argument type error in call method: '{$method_name}()'. Argument data structure have type '$object_type' but "
                                            . "sets as '$declared_table'. Make sure that method ...->of('entity_name') equals argument "
                                            . "type of data structure for method '{$method_name}()'", $code = null );
            
            }
            
        }
        
    }

    protected function convertDataToArray( $data ) {
        
        $table_name = null;
        $data_array = [];
        $type = '';
        
        if( !is_array( $data ) && is_object( $data ) ) {
            
            $table_name = ( new ReflectionClass( $data) )->getShortName();
            
            if( is_a( $data, "stdClass" ) ) {
                
                if( isset( $data->table_name ) ) {
                
                    $table_name = $data->table_name;
                
                } else {

                    throw new BackendlessException('Missing property "table_name" in php stdClass');

                }
                
                $data_array = $this->getRecursiveData( $data );
                $type = 'std_class';
                
            } else {
                
                $data_array = $this->getRecursiveData( $data );
                $type = 'user_class';
                
            }
            
        } else {
            
            if( isset( $data[ 'table-name' ] ) ) {
                
                $table_name = $data[ 'table-name' ];
                
            } elseif( isset( $data[ '___class' ] ) ) {
                
                $table_name = $data[ '___class' ];
                
            } else {
                            
                throw new BackendlessException( 'Missing "table-name" in data multi array' );
                
            }
            
            $data_array = $data;
            $this->prepareArray( $data_array );
     
            $type = 'array';
            
        }
        
        if( isset( $data_array['table-name'] ) ) {
            
            unset( $data_array['table-name'] );
            
        }
        
        return [ 'table' => $table_name, 'data' => $data_array, 'type' => $type ];
        
    }
    
    // convertation object (or object with relations ) to array
    protected function getRecursiveData( $data ) {
        
        $data_array = [];

        $reflection = new ReflectionClass( $data );
        $props = $reflection->getProperties();

        foreach ( $props as $prop ) {

            $prop->setAccessible( true );
            $data_array[ $prop->getName() ] =  $prop->getValue( $data );
            
        }

        // dinamic declared
        $obj_vars = get_object_vars( $data );

        if( isset( $data_array ) && $data_array !== null ){

            $data_array = array_merge( $data_array, $obj_vars );

        } else {

            $data_array = $obj_vars;

        }

        $data_array['___class'] = $reflection->getShortName();

        if( isset( $data_array['table_name'] ) ) { // for std classes

            $data_array['___class'] = $data_array['table_name'];

            unset( $data_array['table_name'] );
        }
        
        if( isset( $this->aliases[ $data_array['___class'] ] ) ){
            
            $data_array['___class'] = $this->aliases[ $data_array['___class'] ];
            
        }
        
        if( $data_array['___class'] == "GeoPoint" ) {
                        
            $this->clearGeoPoint( $data_array );
           
        }

        foreach ( $data_array as $data_key => $data_val ) {

            if( gettype( $data_val ) == "object" ) {

                $data_array[$data_key] = $this->getRecursiveData( $data_val );

            }elseif( is_array( $data_val ) ) { // if relation one to many
                
                foreach ( $data_val as $index => $val  ) {
                    
                    if( gettype( $val ) == "object" ) {
                        
                        $data_array[$data_key][$index] = $this->getRecursiveData( $val );
                        
                    }
                }
                
            }
        }

        return $data_array;        
        
    }
    
    protected function clearGeoPoint( &$point ) {
         
        if( empty( $point['objectId'] ) ) {

            unset( $point['objectId'] );
                                        
        }
            
        if( empty( $point['metadata'] ) ) {

            unset( $point['metadata'] );
                                        
        }
            
    }
    
    protected function prepareArray( &$data ) {
        
        //convert table-name to ___class and convert geopoint to array 
        
        if( is_array( $data ) ) {
        
            if( isset( $data["table-name"] ) ) {

                $data["___class"] = $data["table-name"];
                unset( $data["table-name"] );
                
                if( isset( $this->aliases[ $data['___class'] ] ) ) {
            
                    $data['___class'] = $this->aliases[ $data['___class'] ];
            
                }

                foreach ( $data as $property_name => $property_value  ) {

                    if( is_array( $property_value ) ) {

                        if( isset( $property_value["table-name"] ) ) {

                            $data[ $property_name ][ '___class' ] = $property_value[ 'table-name' ];
                            unset( $data[ $property_name ][ 'table-name'] );
                            
                            if( isset( $this->aliases[ $data[ $property_name ][ '___class' ] ] ) ) {
            
                                $data[ $property_name ][ '___class' ] = $this->aliases[ $data[ $property_name ][ '___class' ] ];
            
                            }

                            $this->prepareArray( $data[ $property_name ] );

                        }

                        if( isset( $property_value[0] ) ) {

                            foreach ( $data[ $property_name ] as $index => $val ){

                                $this->prepareArray( $data[ $property_name ][$index] );

                            }

                        }

                    }
                    // if array have geopoints 
                    $this->prepareGeoPointInArray( $data[$property_name] );

                }

            }
            
        }
        
        $this->prepareGeoPointInArray( $data );
        
    }
    
    protected function recursiveDeleteEmptyProps( &$data ) {
        
        if( is_array( $data ) ) {
        
            if( isset( $data["___class"] ) ) {

                foreach ( $data as $property_name => $property_value  ) {
                    
                    if( empty( $data[$property_name] ) ) {
                        
                        unset( $data[$property_name] );
                        
                    }

                    if( is_array( $property_value ) ) {

                        if( isset( $property_value["___class"] ) ) {

                            $this->recursiveDeleteEmptyProps( $data[$property_name] );

                        }

                        if( isset( $property_value[0] ) ) {

                            foreach ( $data[ $property_name ] as $index => $val ){

                                $this->recursiveDeleteEmptyProps( $data[ $property_name ][$index] );

                            }

                        }

                    }

                }

            }
            
        }
        
    }
    
    protected function prepareGeoPointInArray( &$property ) {
                
        if( gettype( $property ) == "object" ) {

            if( ( new ReflectionClass( $property ) )->getShortName() == "GeoPoint" ) {                    

                $geo_point_array = [];
                $metadata = [];

                $props = ( new ReflectionClass( $property ) )->getProperties();

                foreach ( $props as $prop ) {

                    $prop->setAccessible( true );

                    if( $prop->getName() == "objectId" ) {
                        
                        if( $prop->getValue( $property )  == "" ) {
                            
                            continue;
                            
                        }
                    }
                    
                    $geo_point_array[ $prop->getName() ] =  $prop->getValue( $property );

                    if( $prop->getName() == "metadata" ) {

                            $metadata = $prop->getValue( $property );

                            foreach ( $metadata as $index => $meta_val ) {

                                $this->prepareArray( $metadata[$index] );

                            }
                            
                        $geo_point_array["metadata"] = $metadata;
                        $geo_point_array["___class"] = "GeoPoint";
                    
                        if( empty($geo_point_array["metadata"]) ) {
                        
                            unset( $geo_point_array["metadata"] );
                            
                        }
                    }

                }

                $property = $geo_point_array;

            }

        }        
        
    }
    
    protected function extractObjectId( $object ) {
        
        if( !is_array( $object ) && is_object( $object ) ) {
            
            if( is_a( $object, "stdClass" ) ) {
                
                if( isset( $object->objectId ) ) {
                    
                    return $object->objectId;
                    
                } else {
                    
                    return null;
                    
                }
                
            }else{
                
                $object_id = null;
                
                $reflection = new ReflectionClass( $object );
                $props = $reflection->getProperties();

                foreach ( $props as $prop ) {

                    $prop->setAccessible( true );
                    
                    if( $prop->getName() == "objectId" ) {
                    
                        $object_id =  $prop->getValue( $object );
                        
                    }
            
                }
                
                if( $object_id != null ) {
                    
                    return $object_id;
                    
                }

                // dinamic declared
                return $this->extractObjectId( get_object_vars( $object ) );
                
            }

            
        } elseif( is_array( $object ) ) {
             
            if( isset( $object["objectId"] ) ) {
                
                return $object["objectId"];
                
            } else {
                
                return null;
                
            }
            
        }
        
        return null;
        
    }
    
    protected function extractObjectType( $object ) {
        
        $table_name = null;
        
        if( !is_array( $object ) && is_object( $object ) ) {
            
            $table_name = ( new ReflectionClass( $object ) )->getShortName();
            
            if( is_a( $object, "stdClass" ) ) {
                
                if( isset( $object->table_name ) ) {
                
                    $table_name = $object->table_name;
                
                }
                
            } 
            
        } else {
            
            if( isset( $data['table-name'] ) ) {
                
                $table_name = $data['table-name'];
                
            } 
            
        }
        
        if( isset( $this->aliases[ $table_name ] ) ) {
            
            $table_name = $this->aliases[ $table_name ];
            
        }
        
        return $table_name;
        
    }
    
    // method call inside geo service
    public function prepareGeoPointMetadata( &$metadata_array ) {
         
        foreach ( $metadata_array as $meta_name => $meta_val ) {

            if( is_object( $meta_val ) ) {

                $metadata_array[ $meta_name ] = $this->getRecursiveData( $meta_val );

            }

        }
            
     }
     
}
