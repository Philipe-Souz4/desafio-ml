<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Busca Mercado Livre';

$exemplos = [
    'MLB4540404727',
    'MLB6478021916',
    'MLB6478017024',
];
?>

<div class="site-index container py-5">

    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Meli Search</h1>
        <p class="text-muted">Busque informações detalhadas de produtos</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow p-3">
                <?= Html::beginForm(['produto/detalhes'], 'get', ['class' => 'input-group']) ?>
                    <?= Html::textInput('meli_id', '', [
                        'class'       => 'form-control form-control-lg',
                        'placeholder' => 'Ex: MLB59773991',
                        'id'          => 'meli_id_input',
                        'required'    => true,
                    ]) ?>
                    <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary px-4']) ?>
                <?= Html::endForm() ?>
            </div>

            <!-- IDs de exemplo clicáveis -->
            <div class="mt-3 text-center">
                <small class="text-muted me-2">Exemplos:</small>
                <?php foreach ($exemplos as $id): ?>
                    <a href="<?= Url::to(['produto/detalhes', 'meli_id' => $id]) ?>"
                       class="badge bg-secondary text-decoration-none me-1 p-2"
                       style="font-size: 13px;">
                        <?= $id ?>
                    </a>
                <?php endforeach ?>
            </div>

        </div>
    </div>

</div>