<?php
use yii\helpers\Html;

$this->title = 'Busca Mercado Livre';

$exemplos = [
    'MLB1381222244',
    'MLB-1689836021',
    'MLB-1570636742',
];
?>

<div class="site-index container py-5">

    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Busca MLB</h1>
        <p class="text-muted">Busque informações de produtos no ML</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow p-3">
                <div class="input-group">
                    <?= Html::textInput('meli_id', '', [
                        'class'       => 'form-control form-control-lg',
                        'placeholder' => 'Ex: MLB4540404727',
                        'id'          => 'meli_id_input',
                    ]) ?>
                    <button class="btn btn-primary px-4" onclick="buscarProduto()">Buscar</button>
                </div>
            </div>

            <!-- IDs de exemplo clicáveis -->
            <div class="mt-3 text-center">
                <small class="text-muted me-2">Exemplos:</small>
                <?php foreach ($exemplos as $id): ?>
                    <a href="/produto/<?= $id ?>"
                       class="badge bg-secondary text-decoration-none me-1 p-2"
                       style="font-size: 13px;">
                        <?= $id ?>
                    </a>
                <?php endforeach ?>
            </div>

        </div>
    </div>

</div>

<script>
function buscarProduto() {
    var id = document.getElementById('meli_id_input').value.trim();
    if (!id) return;
    var idLimpo = id.replace(/[-\s]/g, '').toUpperCase();
    window.location.href = '/produto/' + idLimpo;
}

document.getElementById('meli_id_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') buscarProduto();
});
</script>