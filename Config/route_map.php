<?php

use Config\Routes\Route;

new Route();

$routes = new Route();

$routes->add( "/", "HomeRoute" );
$routes->add( "/admin", "AdminRoute" );
$routes->add( "/sobre", "Sobre" );

$routes->submit();