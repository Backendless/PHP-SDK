<?php
namespace backendless\lib;

class Log {
    
    protected static $app_log_file = "sdk.log";
    protected static $app_log_dir = "log";
    
    protected static $log_path;
    
    protected static $colors = [ 'blue' => '0;34', 'yellow' => '1;33', 'red' => '0;31' ];
    
    public static function init( ) {
        
        self::$log_path =  dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . self::$app_log_dir;
        
        if( !file_exists( self::$log_path ) ) {
            
            mkdir( self::$log_path );
            
        }
        
    }

    public static function write( $msg, $target = 'all', $colored = true ) {
        
        self::doWrite( $msg, $target, "\r", $colored, 'none');
                          
    }
    
    public static function writeInfo( $msg, $target = 'all', $colored = true ) {
        
        self::doWrite( $msg, $target, "[INFO]", $colored, 'blue');
                          
    }
    
    public static function writeWarn( $msg, $target = 'all', $colored = true ) {
        
        self::doWrite( $msg, $target, "[WARN]", $colored, 'yellow');
                          
    }
    
    public static function writeError( $msg, $target = 'all', $colored = true ) {
        
        self::doWrite( $msg, $target, "[ERROR]", $colored, 'red');
                          
    }
    
    public static function writeTrace( $msg, $target = 'file', $colored = false ) {
        
        self::doWrite( $msg, $target, "[ERROR]", $colored, 'red');
                          
    }
    
    protected static function doWrite( $msg, $target, $msg_prefix, $colored, $color ) {
        
        if( $colored ) {
            
            $msg_colored_prefix = self::addColor($msg_prefix, $color );
            
        }else{
            
            $msg_colored_prefix = $msg_prefix;
            
        }
        
        $space = '';
        
        if( strlen($msg_prefix) > 1 ) {
            
            $space = ' ';
            
        }
        
        $msg_colored = $msg_colored_prefix . $space . $msg;
        $msg = $msg_prefix . $space . $msg;
        
        if( $target == 'all') {
            
            self::writeToConsole($msg_colored);
            self::writeToFile($msg);
            
        }elseif( $target == 'console') {
            
            self::writeToConsole($msg_colored);
            
        }elseif($target == 'file') {
            
            self::writeToFile($msg);
            
        }
    }
    
    protected static function addColor( $string, $color ) {
        
        $colored_string = $string;
        
        if( isset(self::$colors[$color]) ) {
            
            $color_code = self::$colors[$color];
            $colored_string = "\033[" . $color_code . "m" . $string . "\033[0m";
        }
        
        return $colored_string;
        
    }
    
    protected static function writeToConsole( $msg ) {
        
        echo $msg . "\n";
        
    }

    protected static function writeToFile( $msg ) {
        
        $log_file_path = self::$log_path . DS . self::$app_log_file;
        
        if( !file_exists($log_file_path) ) {
            
            file_put_contents($log_file_path, '');
            
        }
        
        file_put_contents( $log_file_path, date("Y-m-d H:i:s" ,time()) ." " . $msg . "\n", FILE_APPEND );
        
    }
    
}


