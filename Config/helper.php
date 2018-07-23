<?php

    class Helper {

        public function __construct() {

            date_default_timezone_set( "America/Sao_Paulo" );
            require 'App/Functions/mailer/vendor/autoload.php';
            include "Config/database.php";

            include "App/Controllers/Home.php";
            include "App/Controllers/Admin.php";

            include "App/Models/Home.php";
            include "App/Models/Admin.php";

        }

        public static function Main() {
            // include "Config/routes.php";
            include "Config/route.php";
            include "Config/route_map.php";
        }

        public static function TimeChecker() {
            self::Main();
        }
    }