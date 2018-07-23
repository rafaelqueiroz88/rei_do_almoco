<?php

use App\Controllers\Home;
use App\Controllers\Admin;

class HomeRoute {

    public function __construct()  {
        new Home();
        Home::Index();
    }

}

class AdminRoute {

    public function __construct()  {

        include "./App/Controllers/Admin.php";
        $ctrl = new AdminController();
        $ctrl->Index();

    }

}