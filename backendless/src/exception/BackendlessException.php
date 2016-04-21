<?php
namespace backendless\exception;

use Exception;


class BackendlessException extends Exception
{
    public function __construct ( $message, $code = null, $previous = NULL ) {
        
        parent::__construct( $message, $code, $previous );
        
    }

}
