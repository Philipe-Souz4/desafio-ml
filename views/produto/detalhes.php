<?php
use yii\helpers\Html;

$this->title = Html::encode($produto['title']);
$this->params['breadcrumbs'][] = ['label' => 'Busca', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-5">

    <div class="mb-4">
        <?= Html::a('&larr; Nova busca', ['site/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="row g-0">

            <!-- Imagem do produto -->
            <div class="col-md-4 p-4 bg-white text-center border-end d-flex align-items-center justify-content-center">
                <?php if (!empty($produto['thumbnail'])): ?>
                    <img src="<?= Html::encode(str_replace('-I.jpg', '-O.jpg', $produto['thumbnail'])) ?>"
                         class="img-fluid rounded"
                         style="max-height: 300px; object-fit: contain;"
                         alt="<?= Html::encode($produto['title']) ?>">
                <?php else: ?>
                    <span class="text-muted">Sem imagem</span>
                <?php endif ?>
            </div>

            <!-- Dados do produto -->
            <div class="col-md-8 bg-light">
                <div class="card-body p-4 p-md-5">

                    <h1 class="h3 fw-bold mb-1">
                        <?= Html::encode($produto['title']) ?>
                    </h1>

                    <p class="text-muted mb-4" style="font-size: 13px;">
                        ID: <code><?= Html::encode($produto['id']) ?></code>
                        &nbsp;&middot;&nbsp;
                        Categoria: <code><?= Html::encode($produto['category_id']) ?></code>
                    </p>

                    <h2 class="fw-bold text-success mb-4" style="font-size: 2rem;">
                        <?= Yii::$app->formatter->asCurrency($produto['price'], 'R$') ?>
                    </h2>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-info text-dark p-2 fs-6">
                            Estoque disponível: <?= (int) $produto['available_quantity'] ?>
                        </span>
                    </div>

                    <?php if (!empty($produto['permalink'])): ?>
                        <a href="<?= Html::encode($produto['permalink']) ?>"
                           target="_blank"
                           class="btn btn-warning btn-lg fw-bold px-4">
                            Ver no Mercado Livre &nearr;
                        </a>
                    <?php endif ?>

                </div>
            </div>

        </div>
    </div>

</div>