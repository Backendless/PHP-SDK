<?php
namespace backendless\services\messaging;

class DeliveryOptions {
    

    private $push_broadcast;        
    private $push_policy;           
    private $push_singlecast = [];  
    private $publish_at;            
    private $repeat_every;          
    private $repeat_expires_at;     
    
    public function  __construct() {
        
    }
    
    public function setPushBroadcast( $val ) {
        
        $this->push_broadcast = $val;
        return $this;
        
    }
    
    public function getPushBroadcast() {
        
        return $this->push_broadcast;
        
    }
    
    public function setPushPolicy( $val ) {
        
        $this->push_policy = $val;
        return $this;        
        
    }
    
    public function getPushPolicy() {
        
        return $this->push_policy;
        
    }
    
    public function setPushSinglecast( $val ) {
        
        $this->push_singlecast = $val;
        return $this;        
        
    }
    
    public function getPushSinglecast() {
        
        return $this->push_singlecast;
        
    }    
    
    public function addPushSinglecast( $val ) {
        
        $this->push_singlecast[ ] = $val;
        return $this;
        
    }
    
    public function setPublishAt( $val ) {
        
        $this->publish_at = $val;
        return $this;
        
    }
    
    public function getPublishAt() {
        
        return $this->publish_at;
        
    }    
    
    public function setRepeatEvery( $val ) {
        
        $this->repeat_every = $val;
        return $this;
        
    }
    
    public function getRepeatEvery(){
        
        return $this->repeat_every;
        
    }    
    
    public function setRepeatExpiresAt( $val ) {
        
        $this->repeat_expires_at = $val;
        return $this;
        
    }
    
    public function getRepeatExpiresAt() {
        
        return $this->repeat_expires_at;
        
    }    
        
}
