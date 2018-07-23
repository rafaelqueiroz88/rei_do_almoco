<?php

session_start();

include "Config/helper.php";
new Helper();

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Rei do Almoço</title>

        <link rel="stylesheet" 
            href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" 
            integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" 
            crossorigin="anonymous">
        <link rel="stylesheet" href="Public/Assets/Css/bootstrap.css">
        <link rel="stylesheet" href="Public/Assets/Css/style.css">
    </head>
    <body>
        
        <nav></nav>

        <main>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="jumbotron">
                            <h2>Rei do Almoço</h2>
                        </div>
                    </div>
                </div>
            </div>
            <?php Helper::TimeChecker(); ?>
        </main>

        <footer></footer>
        <script src="Public/Assets/Js/jquery.min.js"></script>
        <script src="Public/Assets/Js/bootstrap.min.js"></script>
        <script src="Public/Assets/Js/application.js"></script>
    </body>
</html>