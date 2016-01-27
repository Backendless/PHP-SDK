<?php
namespace backendless\services;

use backendless\lib\RequestBuilder;
use backendless\Backendless;
use backendless\services\messaging\Message;


class Messaging
{
    protected static $instance;
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new Messaging();
            
        }
        
        return self::$instance;
        
    }
    
    public function publish( $msg, $publish_options = null, $delivery_options = null ) {
        
        $channel = "default";
        
        if( isset($publish_options) ) {
           
            $ch = $publish_options->getChannel();
            
            if( $ch !== null ) {
                
                $channel = $ch;
                
            }
            
        }
               
        $msg_data = ["message" => $msg];
        
        $url_part  ="/messaging/" . $channel;
        
        $this->prepareMsgData($msg_data, $publish_options, $delivery_options);
        
        
        return  RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . $url_part, $msg_data, 'POST' );
        
    }
    
    public function cancel( $msg_id ) {
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/messaging/" . $msg_id, '', 'DELETE' );

    }
    
    public function subscribe( $channel = null, $subscription_options = null ) {
        
        $ch = "default";
        
        if( $channel !== null ) {
          
                $ch = $channel;
                
        }
        
        $data = [];
        
        if( $subscription_options !== null ){
            
            $this->prepareSubscribeData($data, $subscription_options);
        }
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/messaging/" . $ch . "/subscribe", $data, 'POST' )['subscriptionId'];
        
    }
    
    public function retrieveMessages( $channel_name, $subscription_id ) {
        
        $msg_array =  RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/messaging/" . $channel_name . "/" . $subscription_id, '', 'GET' );

        foreach ($msg_array["messages"] as $index=>$msg_data) {
            
            $msg = new Message();
            
            $msg->setData($msg_data["data"]);
            $msg->setHeaders($msg_data["headers"]);
            $msg->setMessageId($msg_data["messageId"]);
            $msg->setPublisherId($msg_data["publisherId"]);        
            $msg->setTimestamp($msg_data["publishedAt"]/1000);
            
            $msg_array["messages"][$index] = $msg;            
        }
        
        
        return $msg_array;
        
    }
    
    private function prepareSubscribeData( &$data, $subscription_options ) {
        
        if( $subscription_options->getSubscriberId() !== null ) {

            $data["subscriberId"] = $subscription_options->getSubscriberId();
        }

        if( $subscription_options->getSubtopic() !== null ) {

            $data["subtopic"] = $subscription_options->getSubtopic();
        }            

        if( $subscription_options->getSelector() !== null ) {

            $data["selector"] = $subscription_options->getSelector();

        }                 
        
    }
    
    private function prepareMsgData( &$msg_data, $publish_options = null, $delivery_options = null) {
        
        
        if( $publish_options !== null ) {
            
            if( $publish_options->getPublisherId() !== null ) {
            
                $msg_data["publisherId"] = $publish_options->getPublisherId();
            }
            
            if( count($publish_options->getHeaders()) > 0 ) {
            
                $msg_data["headers"] = $publish_options->getHeaders();
            }
            
            if( $publish_options->getSubtopic() !== null ) {
            
                $msg_data["subtopic"] = $publish_options->getSubtopic();
                
            }
            
        }
        
        if( $delivery_options !== null ) {
            
            if( $delivery_options->getPushBroadcast() !== null ) {
            
                $msg_data["pushBroadcast"] = $delivery_options->getPushBroadcast();
            }
            
            if( $delivery_options->getPushPolicy() !== null ) {
            
                $msg_data["pushPolicy"] = $delivery_options->getPushPolicy();
            }
            
            if( count($delivery_options->getPushSinglecast()) > 0 ) {
            
                $msg_data["pushSinglecast"] = $delivery_options->getPushSinglecast();
            }
            
            
            if( $delivery_options->getPublishAt() !== null ) {
            
                $msg_data["publishAt"] = ( $delivery_options->getPublishAt()*1000 );
            }
            
            if( $delivery_options->getRepeatEvery() !== null ) {
            
                $msg_data["repeatEvery"] = $delivery_options->getRepeatEvery();
            }            
            
            if( $delivery_options->getRepeatExpiresAt() !== null ) {
            
                $msg_data["repeatExpiresAt"] = ( $delivery_options->getRepeatExpiresAt()*1000 );
                
            }                 
        }   
            
    
    }
    
    public function sendTextEmail($subject, $body, $to, $attachments = null) {
        
        return $this->sendEmail($subject, $body, $to, $attachments, $html = false);
        
    }
    
    public function sendHTMLEmail($subject, $body, $to, $attachments = null) {
        
        return $this->sendEmail($subject, $body, $to, $attachments, $html = true);
        
    }
    
    public function sendEmail( $subject, $body, $to, $attachments = null, $html ) {

        if( !is_array($to) ) {
            
            $to = [$to];
            
        }
        
        $data = [
            "subject" => $subject,
            "to" => $to
        ];
        
        if( $html ){
            
            $data["bodyparts"] = ["htmlmessage" => $body ]; 
            
        } else {
            
            $data["bodyparts"] = [ "textmessage" => $body ];
            
        }
        
        if( $attachments !== null ) {
            
            $data["attachment"] = $attachments;
            
        }
        
        return RequestBuilder::doRequestByUrl( Backendless::getUrl() . "/" . Backendless::getVersion() . "/messaging/email", $data, 'POST' );
    
    }
    
}