<?php
namespace backendless\services\messaging;

class Message {
    
    private $message_id;
    private $headers = [];
    private $data;
    private $publisher_id;
    private $timestamp;


    public function  getMessageId() {
        
        return $this->message_id;
        
    }

    public function getHeaders() {
        
        return $this->headers;
        
    }

    public function getData() {
        
        return $this->data;
        
    }

    public function getPublisherId() {
        
        return $this->publisher_id;
        
    }

    public function getTimestamp() {
        
        return $this->timestamp;
        
    }
    
    public function setMessageId( $message_id ) {
        
        $this->message_id = $message_id;
        return $this;        
    }

    public function setHeaders( $headers ) {
        
        $this->headers = $headers;
        return $this;                
    }

    public function setData( $data ) {
        
        $this->data = $data;
        return $this;                
        
    }

    public function setPublisherId( $publisher_id ) {
        
        $this->publisher_id = $publisher_id;
        return $this;
        
    }

    public function setTimestamp( $timestamp ) {
        
        $this->timestamp = $timestamp;
        return $this;        
        
    }

}
