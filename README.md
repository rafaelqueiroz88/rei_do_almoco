# rei_do_almoco
<b>Aplicação de teste: rei_do_almoco.</b>

Este sistema é parte de uma prova.

Rei do Almoço
O rei do almoço é nosso produto que proclamará o rei mais amado ou odiado pelos seus súditos.

<b>Descrição do projeto</b>

O projeto precisa armazenar candidatos para votação.
Esta votação ocorre diariamente, sendo assim os usuários podem candidatar-se mais de uma vez, porém
somente pode haver uma candidatura de um mesmo usuário por dia.

Para prevenir que um usuário de se candidatar mais vezes, o e-mail será utilizado como informação
única de cada usuário, portanto não pode existir duplicidade de um e-mail no dia em questão.

A aplicação deve funcionar somente durante o horário de almoço (previamente configurado para ocorrer entre 10:00 e 12:00 tendo como tolerância 12:01).

<b>Configurações do projeto</b>

Requerimentos: MySql, PHP 7.*, Composer e uma conexão com a internet (visto que somente com conexão com a internet para enviar e-mails de notificação).

<b>Configurando o Mysql</b>

Execute os seguintes comandos para criar o banco e as tabelas necessárias para a aplicação.

CREATE SCHEMA `rei_almoco` ;

CREATE TABLE `rei_almoco`.`candidatos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `foto` TEXT NOT NULL,
  `voto` INT NOT NULL,
  `data_entrada` DATE NOT NULL,
  `hora_entrada` TIME NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `rei_almoco`.`votos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `candidato` INT NOT NULL,
  `eleitor` INT NOT NULL,
  `data_entrada` DATE NOT NULL,
  `hora_entrada` TIME NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `rei_almoco`.`configuration` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `voto_proprio` INT NULL,
  `voto_multiplo` INT NULL,
  `permitir_anonimo` INT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `rei_almoco`.`configuration` (`voto_proprio`, `voto_multiplo`, `permitir_anonimo`) VALUES ('0', '0', '0');

<hr />

<b>Configurando a conexão do MySql</b>

Acesse Config/database.php

Altere as linhas: 10, 11, 12 e 13 conforme as especificações da sua conexão com o banco de dados.

private static $host        = "localhost";
private static $db_name     = "rei_almoco";
private static $username    = "root";
private static $password    = "";

<b>Configurando o servidor de e-mail para disparar emails de notificação</b>

Acesse App/Models/Home.php

Altere as linhas: 82, 83, 84, 85, 86, 87, 88 e 89

$mail->SMTPDebug    = 0;
$mail->isSMTP();
$mail->Host         = 'smtp.gmail.com';
$mail->SMTPAuth     = true;
$mail->Username     = 'reidoalmoco@gmail.com'; // Altere o e-mail se necessário
$mail->Password     = 'reidoalmoco123'; // Inclua a senha do e-mail informado
$mail->SMTPSecure   = 'tls';
$mail->Port         = 587;

Também é necessário preencher algumas variáveis do PHPMailer para atualizar o conteúdo a ser enviado.
O conteúdo pode ser atualizado nas linhas: 92, 93, 94, 95, 96, 97, 98 e 99.

$mail->setFrom( 'reidoalmoco@gmail.com', 'Rei do Almoço' );
$mail->addAddress( self::$email );

// Conteúdo a ser enviado
$mail->isHTML( true );
$mail->Subject = utf8_encode( 'Bem vindo ao Rei do Almoço' );
$mail->Body    = utf8_encode( 'Olá ' . self::$nome. '! Você se cadastrou em Rei do Almoço.' );
$mail->AltBody = utf8_encode( 'Olá' . self::$nome. '! Você se cadastrou em Rei do Almoço.' );

<b>Habilitando o modo de teste da aplicação</b>

Acesse App/Controllers/Home.php

Comente as linhas: 77, 78, 79, 80 e 81

if( $horario >= $limite_minimo && $horario <= $limite_maximo ) :
    include "./Public/Views/Home/index.php";
else:
    include "./Public/Views/Home/out_of_time.php";
endif;

Descomente aa linha: 85

// include "./Public/Views/Home/index.php";

Isso fará com que o sistema se torne acessível a qualquer horário para testes e manutenções.

<b>Configurando o horário de almoço</b>

Acesse App/Controllers/Home.php

Altere as linhas 72 e 73 conforme as suas necessidades.

$limite_minimo  = date( '10:00:0', time() );
$limite_maximo  = date( '12:00:0', time() );

<b>Outras configurações e informações do sistema</b>

Para fazer uso do disparador de e-mail foi necessário criar uma conta de e-mail.
Foi criado um endereço de e-mail em https://gmail.com
E-mail: reidoalmoco@gmail.com
Senha: reidoalmoco123

O sistema utiliza o Bootstrap 4 e Jquery 3.2.1.
Para o envio de e-mails utiliza-se a biblioteca do PHPMailer

Informações em: https://github.com/PHPMailer/PHPMailer (Acesso em: 22/07/2018)

Para fazer uso deste recurso foi necessário também utilizar o Composer
Informações em: https://getcomposer.org/ (Acesso em: 22/07/2018)

Outras informações: rafael.qdc88@gmail.com ou rafael.castro6@fatec.sp.gov.br