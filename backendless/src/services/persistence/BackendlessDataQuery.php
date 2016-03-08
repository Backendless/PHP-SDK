<?php
namespace backendless\services\persistence;

class BackendlessDataQuery {
    
    private $props = [];
    private $load_relations = [];
    private $where_clause;
    private $page_size;
    private $sort_by = [];
    private $offset;
    private $depth;
   
    public function  __construct() {
        
    }
    
    public function setProps( $props ) {
        
        if( is_array($props) && !empty($props) && $props !== null  ) {
            
            $this->props = $props;

        }
        
        return $this;
        
    }
    
    public function addProp( $prop ) {
        
        if( ! in_array($prop, $this->props)) {
            
            $this->props[] = $prop;
        }
        
        return $this;
        
    }
    
    public function getProps() {
        
        return $this->props;
        
    }
    
    public function setRelated( $relations ) {
        
        if( is_array($relations) && !empty($relations) && $relations !== null  ) {
            
            $this->load_relations = $relations;
            

        }
        
        return $this;
        
    }
    
    public function addRelated( $relation ) {
        
        if( ! in_array($relation, $this->load_relations)) {
            
            $this->load_relations[] = $relation;
            
        }
        
        return $this;
        
    }
    
    public function getRelations() {
        
        return $this->load_relations;
        
    }
    
    public function setWhereClause( $where_clause ) {
        
        if( !empty($where_clause) && $where_clause !== null && $where_clause != '' ) {
        
            $this->where_clause = $where_clause;
            
        }
        
        return $this;
        
    }
    
    public function getWhereClause( ) {

        return $this->where_clause;

    }
    
    public function setPageSize( $page_size) {
        
        $this->page_size = $page_size;

        return $this;
        
    }    
    
    public function getPageSize() {
        
        $this->page_size;
        
    }
    
    public function setOffset( $offset ) {
        
        $this->offset = $offset;
        
        return $this;
        
    }    
    
    public function getOffset() {
        
        $this->offset;
        
    }
    
    public function setSortBy( $sort_by ) {
        
        if( is_array($sort_by) && !empty($sort_by) && $sort_by !== null  ) {
            
            $this->sort_by = $sort_by;
            
        }
        
        return $this;
    }
    
    public function addSortBy( $sort_by ) {
        
        if( ! in_array($sort_by, $this->sort_by) ) {
            
            $this->sort_by[] = $sort_by;
            
        }
        
        return $this;
        
    }
    
    public function getSortBy() {
        
        return $this->sort_by;
        
    }
    
    public function setDepth( $depth ) {
        
        $this->depth = $depth;
                
        return $this;
        
    }
    
    public function buildUrlVars() {
        
        $var_array = [];
        
        $var_array['props'] = null;
        
        if( isset($this->props) && count($this->props) == 1 && is_array($this->props) ) {
            
            $var_array['props'] =  "props=" . $this->props[0];
            
        }elseif( isset($this->props) && is_array($this->props) && !empty($this->props) ){
            
            $var_array['props'] =  "props=" . implode( ',' , $this->props);
            
        } 
        
        $var_array['load_relations'] = null;
        
        if( isset($this->load_relations) && count($this->load_relations) == 1 && is_array($this->load_relations) ) {
            
            $var_array['load_relations'] =  "loadRelations=" . $this->load_relations[0];
            
        }elseif( isset($this->load_relations) && is_array($this->load_relations) && !empty($this->load_relations) ){
            
            $var_array['load_relations'] =  "loadRelations=" . implode( ',' , $this->load_relations);
            
        }
        
        $var_array['where'] = null;
        
        if( isset($this->where_clause) ) {
            
            $var_array['where'] = "where=" . urlencode( $this->where_clause );
                    
        }        
        
        $var_array['page_size'] = null;
        
        if( isset($this->page_size) ) {
            
            $var_array['page_size'] = "pageSize=" . $this->page_size;
                    
        }
        
        $var_array['sort_by'] = null;
        
        if( isset($this->sort_by) && count($this->sort_by) == 1 && is_array($this->sort_by) ) {
            
            $var_array['sort_by'] = "sortBy=" . urlencode( $this->sort_by[ 0 ] );
            
        }elseif( isset($this->sort_by) && is_array($this->sort_by) && !empty($this->sort_by) ){
            
            $var_array['sort_by'] =  "sortBy=" . urlencode( implode( ',' , $this->sort_by) );
            
        }
        
        $var_array['offset'] = null;
        
        if( isset($this->offset) ) {
            
            $var_array['offset'] = "offset=" . $this->offset;
                    
        }
        
        $var_array['relations_depth'] = null;
        
        if( isset($this->depth) ) {
            
            $var_array['relations_depth'] = "relationsDepth=" . $this->depth;
                    
        }
       
        
        foreach ($var_array as $var=>$val){
            
            if( $val == null) {
                unset($var_array[$var]);
            }
            
        }
        
        
        return implode("&", $var_array);
    }
    
    
}