<?php

namespace App\Controllers {

    // Chamada das classes necessárias
    use App\Config\Database;
    use App\Models\Home as Model;
    use PDO;
    use PDOException;

    class Home {

        public function __construct() {

            // Verifica o tipo da requisição foi feita a página
            $verb = $_SERVER["REQUEST_METHOD"];
            if( $verb == "POST" ) :

                new Database;
                $db = Database::GetConnection();
                new Model( $db );

                // Recupera a ação a ser executada pela página, é possível utilizar duas ações diferentes
                $acao = $_POST["acao"];

                if( $acao == "candidatar" ) :
                    // Atribuindo os valores do objeto Model
                    Model::$nome        = $_POST["nome"];
                    Model::$email       = $_POST["email"];
                    Model::$foto        = $_FILES["foto"]["name"];
                    Model::$foto_tmp    = $_FILES["foto"]["tmp_name"];

                    // Preparando para executar a ação desejada e enviar uma mensagem avisando o resultado da operação
                    echo '<div class="container">';
                    if( Model::CadastrarCandidato() ) :
                        echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">';
                        echo Model::$mensagem_retorno;
                        echo '</div>';                        
                    else :
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                        echo Model::$mensagem_retorno;
                        echo '</div>';
                    endif;
                    echo '</div>';
                elseif( $acao == "votar" ) :
                    // Atribuindo os valores do objeto
                    Model::$id_rei      = $_POST["id_candidato"];
                    Model::$id_eleitor  = $_POST["id_eleitor"];

                    // Preparando para executar a ação desejada e enviar uma mensagem avisando o resultado da operação
                    echo '<div class="container">';
                    if( Model::Votar() ) :
                        echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">';
                        echo Model::$mensagem_retorno;
                        echo '</div>';                        
                    else :
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                        echo Model::$mensagem_retorno;
                        echo '</div>';
                    endif;
                    echo '</div>';
                endif;               
            endif;

        }

        // Caso a solicitação tenha sido a página inicial, o controlador buscará executar esta ação
        public static function Index() {

            // Recuperando o horário
            $horario        = date( 'H:i:s', time() );
            $limite_minimo  = date( '10:00:0', time() );
            $limite_maximo  = date( '12:00:0', time() );

            // Teste lógico para saber se o sistema está ou não sendo executado durante o horário permitido
            // Comente este bloco de código se for necessário testar a aplicação
            if( $horario >= $limite_minimo && $horario <= $limite_maximo ) :
                include "./Public/Views/Home/index.php";
            else:
                include "./Public/Views/Home/out_of_time.php";
            endif;

            // A linha abaixo está sendo mantida para testes, pois o sistema só aceita acessos dentro do seu horário padrão
            // Descomente se for necessário fazer testes ou manutenção no sistema
            // include "./Public/Views/Home/index.php";

        }
    }
}