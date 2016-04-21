<?php 
namespace backendless\services;

use backendless\lib\RequestBuilder;
use ReflectionClass;
use backendless\model\GeoCategory;
use backendless\model\GeoPoint;
use backendless\services\Persistence;
use backendless\Backendless;
use backendless\exception\BackendlessException;
use backendless\model\BackendlessCollection;


class Geo
{
    
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Geo();
            
        }
        
        return self::$instance;
        
    }
    
    public function addCategory( $category ) {

        $category_name = '';
        
        if( is_object( $category ) ) {
            
            if( is_a( $category, "stdClass" ) ) {
                
                $category_name = $category->name;
                
            } else {
            
                $category_name = $category->getName();
                
            }
            
        } elseif( is_string( $category ) ) {
            
            $category_name = $category;
            
        } elseif( is_array( $category ) ) {
         
            $category_name = $category[ 'name' ];
            
        } 
        
        $result = RequestBuilder::doRequest( 'geo', 'categories/' . $category_name, '', 'PUT' );
        
        if( is_object( $category ) ) {
            
            if( is_a( $category, "stdClass" ) ) {
                
                foreach ( $result as $prop_name => $val) {
                    
                    $category->{$prop_name} = $val;
            
                }
                
            } else {
                
                $props = (new ReflectionClass( $category ))->getProperties();

                foreach ( $props as $prop ) {

                    $prop->setAccessible( true );

                    if( isset( $result[ $prop->getName() ] ) ) {

                        $prop->setValue( $category, $result[ $prop->getName() ] );

                    }
            
                }
                
            }
            
            return $category;
            
        }           

        return $result;
        
    }
    
    public function deleteCategory( $category ) {
        
        $category_name = '';
        
        if( is_object( $category ) ) {
            
            if( is_a( $category, "stdClass") ) {
                
                $category_name = $category->name;
                
            } else {
            
                $category_name = $category->getName();
                
            }
            
        } elseif( is_string( $category ) ) {
            
            $category_name = $category;
            
        } elseif( is_array( $category ) ) {
         
            $category_name = $category['name'];
            
        } 
        
        return RequestBuilder::doRequest( 'geo', 'categories/' . $category_name, '', 'DELETE' )['result'];
        
                
    }
    
    public function getCategories( $return_array = false ) {
        
        $result_collection = RequestBuilder::doRequest( 'geo', 'categories', '', 'GET' );
        
        if( !$return_array ) {
            
            foreach ( $result_collection as $index => $data) {
                
                $result_collection[$index] = new GeoCategory();
                $this->setDataToObject($result_collection[$index], $data);
            }
            
        }
        
        return $result_collection;
        
    }

    public function savePoint( $latitude_or_point, $longitude = null, $categories = null, $metadata = null ) {
        
        
        if( $categories != null ) {
            
            if( !is_array( $categories) ) { throw new BackendlessException( 'Variable $categories must be set as array.' ); }
            
        }
        
        if( $metadata != null ) {
            
            if( !is_array( $metadata ) ) { throw new BackendlessException( 'Variable $metadata must be set as array.' ); }
            
        }
        
        $geopoint_data = [];
        
        if( is_object( $latitude_or_point ) ) {

            if( isset( $latitude_or_point->objectId ) ) {
                
                return $this->updatePoint( $latitude_or_point );
                
            }
            
            $geopoint_data['categories'] = $latitude_or_point->getCategories();
            $geopoint_data['latitude'] = $latitude_or_point->getLatitude();
            $geopoint_data['longitude'] = $latitude_or_point->getLongitude();
            $geopoint_data['metadata'] = $latitude_or_point->getMetadata();
            
        } else {
            
            $geopoint_data['categories'] = $categories;
            $geopoint_data['latitude'] = $latitude_or_point;
            $geopoint_data['longitude'] = $longitude;
            $geopoint_data['metadata'] = $metadata;
            
        }
        
        $this->prepareGeoPointDataBeforeSave( $geopoint_data );
        
        $url_data = [];
        $url_data[] = 'lat=' . $geopoint_data['latitude'];
        $url_data[] = 'lon=' . $geopoint_data['longitude'];
        
        if( count($geopoint_data['categories']) > 0) {
            
            if( is_array($geopoint_data['categories']) ) {
            
                $url_data[] = 'categories=' . implode(',', $geopoint_data['categories']);
                
            }else{
                
                $url_data[] = 'categories=' .  $geopoint_data['categories'];
                
            }
        }
        
        if( isset($geopoint_data["metadata"]) ) {
            
            $url_data[] = 'metadata=' . urlencode( $geopoint_data['metadata'] );
            
        }
        
        $url_data = implode( '&', $url_data );

        $result = RequestBuilder::doRequestWithHeaders( 'geo', 'points?' . $url_data, null, 'PUT', ['application-type' => 'REST', 'Accept:' => '*/*'] );
        
        $geo_point = new GeoPoint();

        $this->setDataToObject( $geo_point, $result["geopoint"] );
        
        return $geo_point;
        
    }
    
    public function updatePoint( $point ) {
        

        if( $point->getObjectId() == null  ) {
            
            throw new BackendlessException( 'GeoPoint object missing objectId property needed for update' );
            
        }
        
        $geopoint_data['categories'] = $point->getCategories();
        $geopoint_data['latitude'] = $point->getLatitude();
        $geopoint_data['longitude'] = $point->getLongitude();
        $geopoint_data['metadata'] = $point->getMetadata();
        
        $this->prepareGeoPointDataBeforeSave( $geopoint_data );
        
        $url_data = [];
        $url_data[] = 'lat=' . $geopoint_data['latitude'];
        $url_data[] = 'lon=' . $geopoint_data['longitude'];
        
        if( count($geopoint_data['categories']) > 0) {
            
            if( is_array($geopoint_data['categories']) ) {
            
                $url_data[] = 'categories=' . implode(',', $geopoint_data['categories']);
                
            }else{
                
                $url_data[] = 'categories=' .  $geopoint_data['categories'];
                
            }
        }
        
        if( isset($geopoint_data['metadata']) ) {
            
            $url_data[] = 'metadata=' . urlencode( $geopoint_data['metadata'] );
            
        }
        
        $url_data = implode( '&', $url_data );
        

        $result = RequestBuilder::doRequestWithHeaders( 'geo', 'points/'. $point->getObjectId() . "?" . $url_data, null, 'PUT', ['application-type' => 'REST', 'Accept:' => '*/*'] );
        
        $geo_point = new GeoPoint();

        $this->setDataToObject($geo_point, $result);
        
        return $geo_point;
        
    }
    
    public function removePoint( $point ) {
        
        $point_id = '';
        
        if( is_object( $point ) ) {
            
            if( $point->getObjectId() != null  ) {
                
                $point_id = $point->getObjectId() ;
                
            } else {
                
                throw new BackendlessException( 'GeoPoint object missing objectId property needed for remove point' );
                
            }
            
        } else{
            
            $point_id = $point;
            
        }
        
        RequestBuilder::doRequest( 'geo', 'points/' . $point_id , '', 'DELETE' ); // API return null if success
        
    }
    
    public function getPoints( $geo_query ) {
        
        return new BackendlessCollection( RequestBuilder::doRequest( 'geo', $geo_query->buildUrl(), '', 'GET')[ 'collection' ] );
        
    }
    
    public function getGeoFencePoints( $geofence_name, $geo_query ){
        
        $url = "points?geoFence=" . $geofence_name;
        
        $url_part = $geo_query->buildCategoryUrl();
        
        if( strlen($url_part) >0 ) {
            
            $url .= "&" . $url_part;
            
        }
        
        return new BackendlessCollection( RequestBuilder::doRequest( 'geo', $url, null, 'GET')[ 'collection' ] );
        
    }
    
    public function runOnEnterAction( $geofence_name, $geopoint = null ) {
        
        return $this->runAction( $action_name = 'onenter', $geofence_name, $geopoint );
    
    }
    
    public function runOnStayAction( $geofence_name, $geopoint = null ) {
        
        return $this->runAction( $action_name = 'onstay', $geofence_name, $geopoint );
    
    }
    
    public function runOnExitAction( $geofence_name, $geopoint = null ) {
        
        return $this->runAction( $action_name = 'onexit', $geofence_name, $geopoint );
    
    }
    
    // internal logic
    
    private function prepareGeoPointDataBeforeSave( &$point_array ) {
        
        if( count( $point_array[ 'metadata' ] ) >= 1 ) {
            
            $persistance = Persistence::getInstance();
            $persistance->prepareGeoPointMetadata( $point_array[ 'metadata' ] );
            
        }
        
        $point_array[ 'metadata' ] = json_encode( $point_array[ 'metadata' ] );
        
    }
    
    private function setDataToObject( &$object, $data ) {
    
        $props = (new ReflectionClass( $object ))->getProperties();

            foreach ( $props as $prop) {

                $prop->setAccessible( true );
            
                if( isset( $data[ $prop->getName() ] ) ) {
                
                    $prop->setValue( $object, $data[ $prop->getName() ] );
                    unset( $data[ $prop->getName() ] );
                
                }
            
            }
            
            foreach ( $data as $key => $val ) {

                $object->{$key} = $val;

            }
        
            
    }
    
    private function runAction( $action_name, $geofence_name, $geopoint = null ) {
    
        $url = "fence/" .$action_name . "?geoFence=" . $geofence_name;
        
        $headers = [];
        $headers['application-id'] = Backendless::getApplicationId();
        $headers['secret-key'] = Backendless::getSecretKey();
        $headers['application-type'] = 'REST';
        
        $geopoint_data = [];
         
        if( $geopoint !== null ) {
            
            if( is_object( $geopoint ) ) {
                
                $geopoint_data['latitude'] = $geopoint->getLatitude();
                $geopoint_data['longitude'] = $geopoint->getLongitude();
                
            } else {
                
                $geopoint_data = $geopoint;
                
            }
            
            $headers['Content-Type'] = 'application/json';
            
        } else {
            
            $geopoint_data = null;
            
        }
        
        return RequestBuilder::doRequestWithHeaders( "geo", $url, $geopoint_data, "POST", $headers )["totalObjects"];
        
    }
    
   // public function loadMetadata( $point ) {
        
        //var_dump($point);
        //$result = RequestBuilder::doRequest( 'geo/points/' . $point->objectId .'/metadata', null, 'GET');
        //var_dump($result);
        //var_dump($point);
    //}
    
}
