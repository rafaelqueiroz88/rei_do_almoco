<?php

namespace App\Models {

    // Chamada das classes necessárias
    use PDO;
    use PDOException;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Home {

        private static $conn;
        private static $_candidatos = "candidatos";
        private static $_votos      = "votos";

        public static $nome;
        public static $email;
        public static $foto;
        public static $foto_tmp;
        public static $id_rei;
        public static $id_eleitor;
        public static $mensagem_retorno;

        public function __construct( $db ) {
            self::$conn = $db;
        }

        // Cadastra um candidato na tabela rei_almoço. Ativa assim que submeter o formulário da tela principal
        public static function CadastrarCandidato() {
            $hoje   = date( "Y-m-d" );
            $agora  = date( "H:m:s" );
            
            // Verificando se o candidato já se cadastrou
            $email  = self::$email;
            $query  = "SELECT * FROM " . self::$_candidatos . " WHERE email='$email' AND data_entrada='$hoje'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $num    = $stmt->rowCount();
            if( $num > 0 ) :
                self::$mensagem_retorno = "E-mail já cadastrado na data de hoje (" . date( "d/m" ) . ")";
                return false;
            endif;
            
            // Armazenando o novo candidato no banco de dados
            $query          = "INSERT INTO " . self::$_candidatos . "
            (nome, email, foto, voto, data_entrada, hora_entrada)
            VALUES
            (:nome, :email, :foto, 0, :hoje, :agora)";

            $stmt           = self::$conn->prepare( $query );
            self::$nome     = htmlspecialchars( strip_tags( self::$nome ) );
            self::$email    = htmlspecialchars( strip_tags( self::$email ) );

            if ( isset( $_FILES['foto']['name'] ) && $_FILES['foto']['error'] == 0 ) :
                $arquivo_tmp    = $_FILES['foto']['tmp_name'];
                $pic            = $_FILES['foto']['name'];
                $extensao       = pathinfo ( $pic, PATHINFO_EXTENSION );
                $extensao_renew = strtolower ( $extensao );

                if ( strstr('.jpg;.jpeg;.gif;.png', $extensao ) ) :
                    $novo_nome  = uniqid ( time () ) . "." . $extensao_renew;
                    $destino    = './App/Images/' . $novo_nome;

                    if ( @move_uploaded_file ( $arquivo_tmp, $destino ) ) :
                        $stmt->bindParam( ':nome', self::$nome );
                        $stmt->bindParam( ':email', self::$email );
                        $stmt->bindParam( ':foto', $novo_nome );
                        $stmt->bindParam( ':hoje', $hoje );
                        $stmt->bindParam( ':agora', $agora );

                        if( $stmt->execute() ) :
                            // Criando variável de sessão para evitar que um mesmo usuário vote consecutivas vezes por dia
                            $_SESSION["candidato"] = self::$conn->lastInsertId();

                            // Estanciando a classe do PHPMailer para enviar e-mails de notificação.
                            $mail = new PHPMailer( true );

                            try {
                                
                                // Configuração básica da aplicação
                                $mail->SMTPDebug    = 0;
                                $mail->isSMTP();
                                $mail->Host         = 'smtp.gmail.com';
                                $mail->SMTPAuth     = true;
                                $mail->Username     = 'reidoalmoco@gmail.com'; // Altere o e-mail se necessário
                                $mail->Password     = 'reidoalmoco123'; // Inclua a senha do e-mail informado
                                $mail->SMTPSecure   = 'tls';
                                $mail->Port         = 587;

                                // Métodos de exibição
                                $mail->setFrom( 'reidoalmoco@gmail.com', 'Rei do Almoço' );
                                $mail->addAddress( self::$email );

                                // Conteúdo a ser enviado
                                $mail->isHTML( true );
                                $mail->Subject = utf8_encode( 'Bem vindo ao Rei do Almoço' );
                                $mail->Body    = utf8_encode( 'Olá ' . self::$nome. '!<br />Você se cadastrou em Rei do Almoço.' );
                                $mail->AltBody = utf8_encode( 'Olá' . self::$nome. '!<br />Você se cadastrou em Rei do Almoço.' );

                                $mail->send();
                            }
                            catch ( Exception $e ) {
                                self::$mensagem_retorno = "Falha ao enviar notificação para o e-mail fornecido.";
                                return false;
                                // Descomente as duas linhas abaixo se for necessário testar a aplicação
                                // echo 'Message could not be sent.';
                                // echo 'Mailer Error: ' . $mail->ErrorInfo;
                            }

                            self::$mensagem_retorno = "Candidato ao Rei do Almoço cadastrado com sucesso!";
                            return true;
                        else :
                            self::$mensagem_retorno = "Falha ao enviar imagem. Verifique se o seu arquivo de imagem está nos formatos: jpg., jpeg., bitmap, ou .png e tente novamente.";
                            return false;
                        endif;      
                    else :
                        self::$mensagem_retorno = "Falha ao cadastrar candidato. Verifique se todas as informações estão corretas e tente novamente.";
                        return false;
                    endif;
                endif;
            endif;
        }

        // Lista os candidatos ao rei do almoço que se cadastraram hoje.
        public static function ListarCandidatosHoje() {
            $hoje   = date( "Y-m-d" );
            $query  = "SELECT * FROM " . self::$_candidatos . " WHERE data_entrada='$hoje'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $num    = $stmt->rowCount();
            if( $num > 0 ) :
                while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) :
                    $id = "";
                    echo "<tr>";
                    echo '<td rows="2" style="width: 10.00%">';
                    echo '<img src="./App/Images/' . $row["foto"] . '"';
                    echo 'alt="' . $row["nome"] . '" class="foto-rei" />';
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 20.00%;">';
                    echo $row["nome"];
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 70.00%;">';
                    if( self::VerificarVotacao() ) :
                        $porcentagem = self::CalcularPorcentagem( $row["id"] );
                        echo '<div class="progress">';
                        echo '<div class="progress-bar progress-bar-striped bg-info progress-bar-animated"';
                            echo 'role="progressbar"';
                            echo 'style="width: ' . $porcentagem . '%"';
                            echo 'aria-valuenow="50"';
                            echo 'aria-valuemin="0"';
                            echo 'aria-valuemax="100">';
                            if( $porcentagem > 0 ) :
                                echo number_format( $porcentagem, 2, ',', ' ') . "%";
                            endif;
                        echo '</div>';
                        echo '</div>';
                    else :
                        echo '<form action="./" method="post">';
                        echo '<input type="hidden" value="' . $row["id"] . '" name="id_candidato" />';
                        if( isset( $_SESSION["candidato"] ) || !empty( $_SESSION["candidato"] ) ) :
                            $id = $_SESSION["candidato"];
                        elseif( !isset( $_SESSION["candidato"] ) || empty( $_SESSION["candidato"] ) ) :
                            $id = session_id();
                        endif;
                        echo '<input type="hidden" value="' . $id . '" name="id_eleitor" />';
                        echo '<input type="hidden" value="votar" name="acao" />';
                        echo '<button class="btn btn-outline-primary">';
                        echo '<i class="far fa-star"></i> Votar';
                        echo '</button>';
                        echo '</form>';
                    endif;
                    echo "</td>";
                    echo "</tr>";
                    $id = "";
                endwhile;
            endif;
        }

        // Verifica se o candidato atual já votou ou não, se não votou o sistema vai computar o voto
        public static function Votar() {
            $hoje           = date( "Y-m-d" );
            $agora          = date( "H:m:s" );
            $query          = "INSERT INTO " . self::$_votos . "
            (candidato, eleitor, data_entrada, hora_entrada)
            VALUES
            (?, ?, ?, ?)";

            $stmt               = self::$conn->prepare( $query );

            self::$id_rei       = htmlspecialchars( strip_tags( self::$id_rei ) );
            self::$id_eleitor   = htmlspecialchars( strip_tags( self::$id_eleitor ) );
            $stmt->bindParam( 1, self::$id_rei );
            $stmt->bindParam( 2, self::$id_eleitor );
            $stmt->bindParam( 3, $hoje );
            $stmt->bindParam( 4, $agora );

            if( $stmt->execute() ) :
                self::$mensagem_retorno = "Voto computado com sucesso!";
                return true;    
            else :
                self::$mensagem_retorno = "Falha na computação do voto, talvez uma votação já tenha sido executada a partir deste usuário.";
                return false;
            endif;
        }

        // Verifica se um usuário já fez uma votação na data de hoje
        public static function VerificarVotacao() {
            $hoje   = date( "Y-m-d" );
            $agora  = date( "H:m:s" );
            $id     = "";
            if( isset( $_SESSION["candidato"] ) || !empty( $_SESSION["candidato"] ) ) :
                $id = $_SESSION["candidato"];
            elseif( !isset( $_SESSION["candidato"] ) || empty( $_SESSION["candidato"] ) ) :
                $id = session_id();
            endif;
            $query  = "SELECT * FROM "  . self::$_votos . " 
            WHERE eleitor='$id' AND data_entrada='$hoje'";

            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $num    = $stmt->rowCount();

            if( $num > 0 ) :
                return true;
            else :
                return false;
            endif;
        }

        // Retorna a porcentagem de votos que o candidato tem hoje
        public static function CalcularPorcentagem( $id ) {
            $hoje   = date( "Y-m-d" );
            $agora  = date( "H:m:s" );

            $query  = "SELECT * FROM " . self::$_votos . " 
            WHERE data_entrada='$hoje' AND candidato='$id'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $x      = $stmt->rowCount();

            $query  = "SELECT * FROM " . self::$_votos . " 
            WHERE data_entrada='$hoje'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $y      = $stmt->rowCount();

            $porcentagem = ( $x * 100 ) / $y;

            return $porcentagem;
        }

        // Retorna a porcentagem de votos que o candidato tem hoje
        public static function CalcularPorcentagemSemanal( $id ) {
            $hoje               = date( "Y-m-d" );
            $semana_passada     = date( "Y-m-d", strtotime( "-7 days" ) );
            $agora              = date( "H:m:s" );

            $query  = "SELECT * FROM " . self::$_votos . " 
            WHERE data_entrada='$hoje' 
            AND candidato='$id' 
            AND data_entrada BETWEEN '$semana_passada' AND '$hoje'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $x      = $stmt->rowCount();

            $query  = "SELECT * FROM " . self::$_votos . " 
            WHERE data_entrada='$hoje'";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $y      = $stmt->rowCount();

            $porcentagem = ( $x * 100 ) / $y;

            return $porcentagem;
        }

        // Lista os candidatos com maior número de votos da semana passada
        public static function ListarCandidatosAmadosSemana() {
            $hoje               = date( "Y-m-d", strtotime( "-1 days" ) );
            $semana_passada     = date( "Y-m-d", strtotime( "-7 days" ) );
            $query  = "SELECT COUNT(*) AS votos, c.id, c.nome, c.foto from " . self::$_candidatos . " c
            INNER JOIN " . self::$_votos . " v ON v.candidato=c.id 
            WHERE v.data_entrada BETWEEN '$semana_passada' AND '$hoje'
            GROUP BY c.id";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $num    = $stmt->rowCount();
            if( $num > 0 ) :
                while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) :
                    $id = "";
                    echo "<tr>";
                    echo '<td rows="2" style="width: 10.00%">';
                    echo '<img src="./App/Images/' . $row["foto"] . '"';
                    echo 'alt="' . $row["nome"] . '" class="foto-rei" />';
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 20.00%;">';
                    echo $row["nome"];
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 70.00%;">';
                    if( self::VerificarVotacao() ) :
                        $porcentagem = self::CalcularPorcentagemSemanal( $row["id"] );
                        echo '<div class="progress">';
                        echo '<div class="progress-bar progress-bar-striped bg-success progress-bar-animated"';
                            echo 'role="progressbar"';
                            echo 'style="width: ' . $porcentagem . '%"';
                            echo 'aria-valuenow="50"';
                            echo 'aria-valuemin="0"';
                            echo 'aria-valuemax="100">';
                            if( $porcentagem > 0 ) :
                                echo number_format( $porcentagem, 2, ',', ' ') . "%";
                            endif;
                        echo '</div>';
                        echo '</div>';
                    else :
                        echo '<form action="./" method="post">';
                        echo '<input type="hidden" value="' . $row["id"] . '" name="id_candidato" />';
                        if( isset( $_SESSION["candidato"] ) || !empty( $_SESSION["candidato"] ) ) :
                            $id = $_SESSION["candidato"];
                        elseif( !isset( $_SESSION["candidato"] ) || empty( $_SESSION["candidato"] ) ) :
                            $id = session_id();
                        endif;
                        echo '<input type="hidden" value="' . $id . '" name="id_eleitor" />';
                        echo '<input type="hidden" value="votar" name="acao" />';
                        echo '<button class="btn btn-outline-primary">';
                        echo '<i class="far fa-star"></i> Votar';
                        echo '</button>';
                        echo '</form>';
                    endif;
                    echo "</td>";
                    echo "</tr>";
                    $id = "";
                endwhile;
            endif;
        }

        // Lista os candidatos com menor número de votos durante a semana
        public static function ListarCandidatosMenosAmadosSemana() {
            $hoje               = date( "Y-m-d", strtotime( "-1 days" )  );
            $semana_passada     = date( "Y-m-d", strtotime( "-7 days" ) );
            $query  = "SELECT COUNT(*) AS votos, c.id, c.nome, c.foto from " . self::$_candidatos . " c
            INNER JOIN " . self::$_votos . " v ON v.candidato=c.id
            WHERE v.data_entrada BETWEEN '$semana_passada' AND '$hoje' 
            GROUP BY c.id
            ORDER BY votos ASC";
            $stmt   = self::$conn->prepare( $query );
            $stmt->execute();
            $num    = $stmt->rowCount();
            if( $num > 0 ) :
                while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) :
                    $id = "";
                    echo "<tr>";
                    echo '<td rows="2" style="width: 10.00%">';
                    echo '<img src="./App/Images/' . $row["foto"] . '"';
                    echo 'alt="' . $row["nome"] . '" class="foto-rei" />';
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 20.00%;">';
                    echo $row["nome"];
                    echo "</td>";
                    echo '<td style="vertical-align: middle; width: 70.00%;">';
                    $porcentagem = self::CalcularPorcentagemSemanal( $row["id"] );
                    echo '<div class="progress">';
                    echo '<div class="progress-bar progress-bar-striped bg-danger progress-bar-animated"';
                        echo 'role="progressbar"';
                        echo 'style="width: ' . $porcentagem . '%"';
                        echo 'aria-valuenow="50"';
                        echo 'aria-valuemin="0"';
                        echo 'aria-valuemax="100">';
                        if( $porcentagem > 0 ) :
                            echo number_format( $porcentagem, 2, ',', ' ') . "%";
                        endif;
                    echo '</div>';
                    echo '</div>';
                    echo "</td>";
                    echo "</tr>";
                    $id = "";
                endwhile;
            endif;
        }
    }
}