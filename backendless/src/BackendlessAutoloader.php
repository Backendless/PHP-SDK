<?php
namespace backendless;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

class BackendlessAutoloader
{

    static protected $_instance;
    protected $prefixes = array();
    
    
    private function __construct() {
        
    }

    static public function getInstance() {
        
        if (!self::$_instance) {
            self::$_instance = new BackendlessAutoloader();
        }
        return self::$_instance;
        
    }
    
    static public function register() {
        
        spl_autoload_register(array(self::getInstance(), 'loadClass'));
    }
    
    static public function addNamespace($prefix, $base_dir, $prepend = false) {
        
        $prefix = trim($prefix, '\\') . '\\';

        $base_dir = rtrim($base_dir, '/') . DS;
        $base_dir = rtrim($base_dir, DS) . '/';

        if (isset(self::$_instance->prefixes[$prefix]) === false) {
            self::$_instance->prefixes[$prefix] = array();
        }

        if ($prepend) {
            array_unshift(self::$_instance->prefixes[$prefix], $base_dir);
        } else {
            array_push(self::$_instance->prefixes[$prefix], $base_dir);
        }
    }

    public function loadClass($class) {
            
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {

            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);

            if ( $this->loadFile($prefix, $relative_class) ) {
                return true;
            }

            $prefix = rtrim($prefix, '\\');   
        }

        return false;
    }

    protected function loadFile($prefix, $relative_class) {
            
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        foreach ($this->prefixes[$prefix] as $base_dir) {

            $file = $base_dir . str_replace('\\', DS, $relative_class) . '.php';
            
            if ($this->requireFile($file)) {
                return true;
            }
        }

        return false;
    }

    protected function requireFile($file) {
        
        if (file_exists($file)) {
            require $file;
            return true;
        }
        
        return false;
        
    }

}
