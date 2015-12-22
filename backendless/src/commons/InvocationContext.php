<?php
namespace backendless\commons;


class InvocationContext {
    
  public $app_id;
  public $user_id;
  public $user_roles;
  public $device_type;
  public $configuration_items;

  public function __construct( $data_array = null ) {
      
      if( $data_array != null ) {
          
          $this->app_id = $data_array[ 'appId' ];
          $this->user_id = $data_array[ 'userId' ];
          $this->user_roles = $data_array[ 'userRoles' ];
          $this->device_type = $data_array[ 'deviceType' ];
          $this->configuration_items = $data_array[ 'configurationItems' ];
  
      }
    
  }
    
}