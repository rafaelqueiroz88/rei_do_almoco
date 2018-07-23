<?php

namespace Config\Routes {

    class Route {
        
        private static $_uri = array();
        private static $_method = array();

        public function add( $uri, $method = null ) {
            
            self::$_uri[] = "/" . trim( $uri, "/" );
            if( $method != null ) :
                self::$_method[] = $method;
            endif;

        }

        public function submit() {

            $uriGetParam = isset( $_GET["uri"] ) ? "/" . $_GET["uri"] : "/";
            foreach( self::$_uri as $key => $value ) :
                if( preg_match( "#^$value$#", $uriGetParam ) ) :
                    if( is_string( self::$_method[$key] ) ) :

                        include "routes.php";                        
                        $useMethod = self::$_method[$key];
                        new $useMethod();
                        
                    endif;
                endif;
            endforeach;

        }
    }
}