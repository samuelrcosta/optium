<div class="card">
    <div class="card-header bg-light">
        Cadastrar Nova Área
    </div>

    <div class="card-body">
        <a href="<?php echo BASE_URL; ?>areasCMS" class="btn btn-primary" style="margin-bottom: 20px;"><i class="icon icon-arrow-left-circle"></i>&nbsp;Voltar</a>
        <form method="POST" id="register" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name" class="form-control-label">Nome da Área</label>
                <input class="form-control" name="name" id="name" style="max-width: 400px" data-validation="required" data-validation-error-msg="Digite uma categoria" value="<?php echo(isset($name))?$name:''; ?>"/>
            </div>
            <div class="form-group">
                <label for="description" class="form-control-label">Descrição da Área (Compartilhamento e Google)</label>
                <textarea class="form-control" name="description" id="description" style="max-width: 400px"><?php echo(isset($description))?$description:''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="image" class="form-control-label">Imagem da Área (Compartilhamento)</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-success progresso" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"> Nenhum arquivo enviado
                    </div>
                </div>

                <input type="file" class="form-control" id="image" name="image" data-validation="mime size" data-validation-allowing="jpg, png, jpeg" data-validation-max-size="2M" data-validation-error-msg="Insira um arquivo de imagem válido de até 2Mb"/>
                <div class="image-area" style="width: 100%;max-width: 100%;text-align: center;">
                    (Caso não seja enviado nenhum arquivo será usado a imagem padrão)<br>
                    <a href="<?= BASE_URL ?>assets/images/categories/<?= $pattern_image ?>" target="_blank">Imagem Padrão</a>
                </div>
            </div>
            <?php if(!empty($notice)):?>
                <?php echo $notice ?>
            <?php endif; ?>
            <div class="form-group">
                <input class="btn btn-success" type="submit" value="Salvar" />
            </div>
        </form>
    </div>
</div>
<script src="<?php echo BASE_URL ?>assets/js/resize_image.js"></script>
<script src="<?php echo BASE_URL ?>assets/js/controllers/categoriesImagesCMSController.js"></script>
<script>
    $(document).ready(function(){
        PageController.start();
        $.validate({
            form : '#register',
            modules : 'file'
        });
    });
</script>