<?php
namespace backendless\services\messaging;

class SubscriptionOptions {
    
    private $subscriber_id;
    private $subtopic;
    private $selector;


    public function getSubscriberId() {
        
        return $this->subscriber_id;
        
    }

    public function setSubscriberId( $subscriber_id ) {
        
        $this->subscriber_id = $subscriber_id;
        return $this;
        
    }

    public function getSubtopic() {
        
        return $this->subtopic;
        
    }
    

    public function setSubtopic( $subtopic ) {
        
        $this->subtopic = $subtopic;
        return $this;
        
    }

    public function getSelector() {
        
        return $this->selector;
    }

    public function setSelector( $selector ) {
        
        $this->selector = $selector;
        return $this;
        
    }
    
}