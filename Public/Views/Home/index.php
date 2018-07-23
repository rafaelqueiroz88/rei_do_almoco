<?php

use App\Config\Database;
use App\Models\Home;

new Database;
$db     = Database::GetConnection();
$rei    = new Home( $db );

?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
            <div class="cadastro-rei-almoco">
                <center>
                    <h2>
                        Candidatar-se
                    </h2>
                </center>
                <br />
                <form action="./" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="candidatar" />
                    <div class="form-group row">
                        <label for="nome" class="col-sm-3 col-form-label">
                            Nome<strong style="color: red;">*</strong>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da majestade" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">
                            E-mail<strong style="color: red;">*</strong>
                        </label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" placeholder="e-mail@exemplo.com" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="foto" class="col-sm-3 col-form-label">
                            Foto<strong style="color: red;">*</strong>
                        </label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="foto" name="foto" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="submit" class="form-control" value="Enviar" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
            <div class="painel-candidatos">
                <div class="table-responsive">
                    <table class="table table-striped table-hover nome-rei">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Candidatos a Rei do Almoço <?php echo date( "d/m" ); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rei::ListarCandidatosHoje(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="painel-candidatos">
                <div class="table-responsive">
                    <table class="table table-striped table-hover nome-rei">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Reis mais amados da semana
                                    <?php 
                                    echo "(" . date( "d/m", strtotime( "-7 days" ) ) . " à ";
                                    echo date( "d/m", strtotime( "-1 days" ) ) . ")";
                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rei::ListarCandidatosAmadosSemana(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="painel-candidatos">
                <div class="table-responsive">
                    <table class="table table-striped table-hover nome-rei">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Reis menos amados da semana
                                    <?php 
                                    echo "(" . date( "d/m", strtotime( "-7 days" ) ) . " à ";
                                    echo date( "d/m", strtotime( "-1 days" ) ) . ")";
                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rei::ListarCandidatosMenosAmadosSemana(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>