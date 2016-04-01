<?php
namespace backendless\services;

use backendless\lib\RequestBuilder;
use backendless\model\BackendlessUser;
use backendless\exception\BackendlessException;

class UserService
{
    
    protected static $instance;
    protected $current_user;
    
    
    private function __construct() {
    
    }

    public static function getInstance() {
        
        if( !isset(self::$instance)) {
            
            self::$instance = new UserService();
            
        }
        
        return self::$instance;
        
    }
    
    public function retrieveUserProperties() {
        
        return RequestBuilder::doRequest( 'users', 'userclassprops', null, 'GET');
        
    }
    
    public function register( $user ) {
        
        self::checkUserToBeProper($user);
        
        $user->putProperties( RequestBuilder::doRequest( 'users', 'register', $user->getProperties() ) );
        
        return $user;
        
    }
    
    public function login( $login_identity, $password ) {
        
        if( !isset($login_identity) || !isset($password) ) {
            
            throw new BackendlessException("Not set login identity or password.");
            
        }
        
        $user = new BackendlessUser();
        $user->setLogin( $login_identity );
        $user->setPassword( $password );

        $user->setProperties( RequestBuilder::doRequest( 'users', 'login', $user->getProperties() ) );
        
        $this->setCurrentUser( $user );
        
        return $user;
        
    }
    
    public function update( $user ) {
        
        
        self::checkUserToBeProperForUpdate( $user );
        
        if( ( $user->getUserId() == null && $user->getUserId() == '') || ( $user->getUserToken() == null && $user->getUserToken() == '') ) {
        
            throw new BackendlessException("User not logged in or wrong user id.");
            
        } else {
       
            $user->putProperties( RequestBuilder::doRequest( 'users', $user->getUserId(), $user->getProperties(), 'PUT' ) );
        
            return $user;
        }
        
    }
    
    public function logout( ) {
        
        RequestBuilder::doRequest('users', 'logout', null, 'GET');
        
        $this->unsetCurrentUser();
        
    }
    
    public function restorePassword( $identity ) {
        
        if( !isset( $identity ) || $identity == null ) {
            
            throw new BackendlessException( 'Identity cannot be null' );
            
        } else {
            
            RequestBuilder::doRequest( 'users', 'restorepassword/' . urlencode( $identity ), null, 'GET' );
            
        }
        
    }
    
    public function resendEmailConfirmation( $email_address ) {
        
        RequestBuilder::doRequest( 'users', 'resendconfirmation?email=' . $email_address , null, 'POST' );
        
    }
    
    public function getCurrentUser() {
        
        return $this->CurrentUser();
        
    }
    
    public function CurrentUser() {
        
        if( isset( $this->current_user ) ) {
            
            return $this->current_user;
            
        }
        
        return null;
        
    }
    
    public function setCurrentUser( $user ) {
        
        if ( is_a( $user, '\backendless\model\BackendlessUser' ) ) {
            
            $this->current_user = $user;
            
        } else {
            
            throw new BackendlessException('Method argument var $user must be BackendlessUser class instance');
        
        }
        
    }
    
    protected function unsetCurrentUser() {
        
        unset( $this->current_user );
        
    }
 
    
    protected static function checkUserToBeProper( $user ) {
        
        self::checkUserToBeProperForUpdate($user);
        
        if( $user->getPassword() == null || $user->getPassword() == '' ) {
            
            throw new BackendlessException("User password cannot be null or empty.");
            
        }
        
    }
    
    protected static function checkUserToBeProperForUpdate( $user ) {
        
        if( $user == null ) {
            
            throw new BackendlessException("User cannot be null or empty.");
            
        }
        
    }
    
    public function getUserToken() {
        
        if( isset( $this->current_user ) ) {
            
            return $this->current_user->getUserToken();
            
        }
        
        return null;
        
    }
    
}
