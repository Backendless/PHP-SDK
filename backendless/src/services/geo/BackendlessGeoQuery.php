<?php
namespace backendless\services\geo;

use backendless\exception\BackendlessException;

class BackendlessGeoQuery {

    private $possible_units = [ 'METERS', 'MILES', 'YARDS', 'KILOMETERS', 'FEET' ];
    
    private $categories = [];
    private $where_clause;
    private $metadata = [];
    private $page_size;
    private $offset;
    private $include_metadata;
    
    private $latitude; 
    private $longitude;
    private $radius;
    private $units;
    
    private $nw_latitude;
    private $nw_longitude;
    private $se_latitude;
    private $se_longitude;
    
    private $search_type = 'category';
    
    // clastering
    private $west_longitude; 
    private $east_longitude; 
    private $map_width; 
    private $cluster_grid_size;
    private $dpp;
    

    public function  __construct() {
        
    }
    
    public function setIncludeMeta( $val ) {
        
        $this->include_metadata = $val;
        
    }
    
    public function addCategory( $category ) {
        
        if( !in_array($category, $this->categories) ) {
    
            $this->categories[] = $category;
            
        }
        
    }
    
    public function setCategories( $categories ) {
        
        $this->categories = $categories;
        
    }
    
    public function setMetadata( $metadata ) {
        
        if( is_array($metadata) ) {
            
            $this->metadata = $metadata;
            
        } else {
            
            throw new BackendlessException( "Metadata must be set as array." );
        
        }
        
    }
    
    public function setWhereClause( $where_clause ) {
        
        $this->where_clause = $where_clause;
        
    }
    
    public function setPageSize( $page_size ) {
        
        $this->page_size = $page_size;
        
    }
    
    public function setOffset( $offset ){
        
        $this->offset = $offset;
        
    }
    
    public function setLatitude( $latitude ) {
        
        $this->latitude = $latitude;
        $this->search_type = 'radius';
        
    }
    
    public function setLongitude( $longitude ) {
        
        $this->longitude = $longitude;
        $this->search_type = 'radius';
        
    }
    
    public function setRadius( $radius ) {
        
        $this->radius = $radius;
        $this->search_type = 'radius';
        
    }
    
    public function setUnits( $units ){
        
        $this->units = $units;
        $this->search_type = 'radius';
        
    }
    
    public function setSearchRectangle( $nw_latitude, $nw_longitude, $se_latitude, $se_longitude ) {
        
        $this->nw_latitude = $nw_latitude;
        $this->nw_longitude = $nw_longitude;
        $this->se_latitude = $se_latitude;
        $this->se_longitude = $se_longitude;
        
        $this->search_type = 'rectangle';
        
    }
    
    public function buildUrl() {
        
        $url = '';
        
        switch ( $this->search_type ) {
            
            case 'category': $url = "points?" . $this->buildCategoryUrl();                                  break;
            
            case 'radius'  : 
                             $url_part = $this->buildCategoryUrl();
                
                             $url = "points?";
                             $url .= ( $url_part !== null ) ? $url_part . '&' : '';

                             $url .= $this->buildRadiusUrl();                                               break;
                         
            case 'rectangle':
                             $url_part = $this->buildCategoryUrl();
                
                             $url = "rect?";
                             $url .= ( $url_part !== null ) ? $url_part . '&' : '';
                             $url .= $this->buildRectangleUrl();                                            break;
                         
            case 'clustering':
                             $url_part = $this->buildCategoryUrl();
                
                             $url = "points?";
                             $url .= ( $url_part !== null ) ? $url_part . '&' : '';
                             $url .= $this->buildClusterUrl();                                            break;
        }
        
        return $url;
        
    }
    
    public function buildCategoryUrl() {
        
        $url_vars = [];
        
        if( isset($this->categories) && count($this->categories) == 1 ) {
            
            $url_vars['categories'] =  "categories=" . $this->categories[0];
            
        }elseif( isset($this->categories) && is_array($this->categories) && !empty($this->categories) ){
            
            $url_vars['categories'] =  "categories=" . implode( ',' , $this->categories );
            
        }
        
        if( isset($this->where_clause) ) {
            
            $url_vars['where'] = "where=" . urlencode( $this->where_clause );
            
        }
        
        if( isset($this->offset) ) {
            
            $url_vars['offset'] = "offset=" . $this->offset;
                    
        }
        
        if( isset($this->page_size) ) {
            
            $url_vars['page_size'] = "pagesize=" . $this->page_size;
                    
        }
        
        if( count($this->metadata) > 0 ) {
            
            $url_vars['metadata'] =  "metadata=" . urlencode(json_encode($this->metadata)) ;
            
        }
        
        if( isset($this->include_metadata) ) {
            
            if( $this->include_metadata == true ) {
                $url_vars['includemetadata'] = "includemetadata=true";
            }
            
        }
        
        if( count($url_vars) > 0 ) {
            
            return implode("&", $url_vars);
            
        }
        
        return null;
        
    }
    
    private function buildRadiusUrl() {
        
        $url_vars = [];
        
        if( isset( $this->latitude) ) {
            
            $url_vars[] = 'lat=' . $this->latitude;
            
        } else {
            
            throw new BackendlessException('Missing latitude value for radius search');
        
        }
        
        if( isset( $this->longitude) ) {
            
            $url_vars[] = 'lon=' . $this->longitude;
            
        } else {
            
            throw new BackendlessException('Missing longitude value for radius search');
        
        }
        
        if( isset( $this->radius) ) {
            
            $url_vars[] = 'r=' . $this->radius;
            
        } else {
            
            throw new BackendlessException('Missing radius value for radius search');
        
        }
        
        
        
        if( isset( $this->units) ) {
            
            if( in_array($this->units, $this->possible_units ) ) {
            
                $url_vars[] = 'units=' . $this->units;
                
            } else {
                
                throw new BackendlessException('Wrong units value for radius search');
                
            }
            
            
        } else {
            
            throw new BackendlessException('Missing units value for radius search');
        
        }
            
        return implode("&", $url_vars);
            
        
    }
    
    private function buildRectangleUrl() {
        
        $url_vars = [];
        
        if( isset( $this->nw_latitude) ) {
            
            $url_vars[] = 'nwlat=' . $this->nw_latitude;
            
        } else {
            
            throw new BackendlessException('Don\'t have set nw-latitude value for rectangle search');
            
        }
        
        if( isset( $this->nw_longitude) ) {
            
            $url_vars[] = 'nwlon=' . $this->nw_longitude;
            
        } else {
            
            throw new BackendlessException('Don\'t have set nw-longitude value for rectangle search');
            
        }
        
        
        if( isset( $this->se_latitude) ) {
            
            $url_vars[] = 'selat=' . $this->se_latitude;
            
        } else {
            
            throw new BackendlessException('Don\'t have set se-latitude value for rectangle search');
            
        }        
        
        if( isset( $this->se_longitude) ) {
            
            $url_vars[] = 'selon=' . $this->se_longitude;
            
        } else {
            
            throw new BackendlessException('Don\'t have set se-longitude value for rectangle search');
            
        }        
        
        
        return implode("&", $url_vars);
        
    }    
    
    private function buildClusterUrl() {
        
        $url_vars = [];
        
        if( isset( $this->cluster_grid_size) ) {
            
            $url_vars[] = 'clusterGridSize=' . $this->cluster_grid_size;
            
        } else {
            
            throw new BackendlessException('Don\'t have set clusterGridSize value for cluster  search');
            
        }
        
        if( isset( $this->dpp) ) {
            
            $url_vars[] = 'dpp=' . $this->dpp;
            
        } else {
            
            throw new BackendlessException('Can\'t calculate dpp value for cluster search');
            
        }
        
        return implode("&", $url_vars);
        
    }    
    
    public function setClusteringParams( $west_longitude, $east_longitude, $map_width, $cluster_grid_size = 100 ) {
        
         $this->search_type = 'clustering';
         
         $this->west_longitude = $west_longitude;
         $this->east_longitude = $east_longitude;
         $this->map_width = $map_width;
         $this->cluster_grid_size = $cluster_grid_size;
         
         if ( ($this->east_longitude - $this->west_longitude) < 0 ) {
 
            $this->dpp = ((($this->east_longitude - $this->west_longitude) + 360) / $this->map_width );
 
         }else{
 
            $this->dpp = (($this->east_longitude - $this->west_longitude) / $this->map_width);
        } 
    }
    
}  