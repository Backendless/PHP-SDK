<?php
namespace backendless\services\messaging;

class PublishOptions {
    
    private $publisher_id;
    private $headers = [];
    private $subtopic;
    private $channel_name;
    
    public static $MESSAGE_TAG = "message";
    public static $IOS_ALERT_TAG = "ios-alert";
    public static $IOS_BADGE_TAG = "ios-badge";
    public static $IOS_SOUND_TAG = "ios-sound";
    public static $ANDROID_TICKER_TEXT_TAG = "android-ticker-text";
    public static $ANDROID_CONTENT_TITLE_TAG = "android-content-title";
    public static $ANDROID_CONTENT_TEXT_TAG = "android-content-text";
    public static $ANDROID_ACTION_TAG = "android-action";
    public static $WP_TYPE_TAG = "wp-type";
    public static $WP_TITLE_TAG = "wp-title";
    public static $WP_TOAST_SUBTITLE_TAG = "wp-subtitle";
    public static $WP_TOAST_PARAMETER_TAG = "wp-parameter";
    public static $WP_TILE_BACKGROUND_IMAGE = "wp-backgroundImage";
    public static $WP_TILE_COUNT = "wp-count";
    public static $WP_TILE_BACK_TITLE = "wp-backTitle";
    public static $WP_TILE_BACK_BACKGROUND_IMAGE = "wp-backImage";
    public static $WP_TILE_BACK_CONTENT = "wp-backContent";
    public static $WP_RAW_DATA = "wp-raw";
    
   
    public function  __construct() {
        
    }
    
    
    public function setPublisherId( $publisher_id ) {
        
        $this->publisher_id = $publisher_id;
        return $this;        
        
    }
    
    public function getPublisherId() {
        
        return $this->publisher_id;
        
    }
    
    public function setSubtopic( $subtopic) {
        
        $this->subtopic = $subtopic;
        return $this;
        
    }
    
    public function getSubtopic() {
        
        return $this->subtopic;
        
    }
    
    public function setHeaders( $headers_array ) {
        
        $this->headers = $headers_array;
        
        return $this;
        
    }
    
    public function getHeaders() {
        
        return $this->headers;
        
    }
    
    public function putHeader( $name, $value ) {
        
        $this->headers[$name] = $value;
        
    }
    
    public function setChannel( $channel ) {
        
        $this->channel_name = $channel;
        return $this;
        
    }
    
    public function getChannel() {
        
        if( isset( $this->channel_name)) {
            
            return $this->channel_name;
            
        }else{
            
            return null;
            
        }
        
    }

    
}