<?php

	class ClassAutoloader {
        public function __construct() {
            spl_autoload_register(array($this, 'loader'));
        }
        private function loader($className) {
            //echo 'Trying to load ', $className, ' via ', __METHOD__, "()\n<br />";
            
            if(strstr($className,'Smarty'))
            {
            	// include Smarty Templates Class
            	require_once($GLOBALS['document_root'] . '/include/libraries/smarty/Smarty.class.php');
            	
            }elseif(strstr($className,'Facebook'))
            {
            	// include Facebook Class
            	require_once($GLOBALS['document_root'] . '/include/libraries/facebook-php-sdk/src/base_facebook.php');
            	require_once($GLOBALS['document_root'] . '/include/libraries/facebook-php-sdk/src/facebook.php');
            	
            }else
            {
           		require_once('class.' . $className . '.php' );
            }
        }
    }
?>