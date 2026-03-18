<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Gerenciar Tokens';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-5" style="max-width: 720px;">

    <h1 class="h3 fw-bold mb-1">Gerenciar Tokens</h1>
    <p class="text-muted mb-4">Gerencie as credenciais de acesso à API do Mercado Livre.</p>

    <?php if ($tokenModel): ?>
    <!-- Status atual -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-bold">
            ✓ Token configurado
        </div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr>
                    <th style="width:160px">Access token</th>
                    <td><code><?= Html::encode(substr($tokenModel->access_token, 0, 40)) ?>...</code></td>
                </tr>
                <tr>
                    <th>Refresh token</th>
                    <td><code><?= Html::encode(substr($tokenModel->refresh_token, 0, 40)) ?>...</code></td>
                </tr>
                <tr>
                    <th>Atualizado em</th>
                    <td><?= date('d/m/Y H:i:s', $tokenModel->updated_at) ?></td>
                </tr>
                <tr>
                    <th>Expira em</th>
                    <td>
                        <?php
                            $expira = $tokenModel->updated_at + 21600;
                            $restam = $expira - time();
                        ?>
                        <?php if ($restam > 0): ?>
                            <span class="text-success">
                                <?= gmdate('H\h i\m', $restam) ?> restantes
                            </span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Expirado — renove abaixo</span>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        Nenhum token configurado. Use uma das opções abaixo para começar.
    </div>
    <?php endif ?>

    <div class="row g-4">

        <!-- Opção 1: Colar tokens manualmente -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-1">Inserir tokens manualmente</h5>
                    <p class="text-muted small mb-3">Cole tokens gerados externamente (Postman, curl, etc.).</p>

                    <?= Html::beginForm(['token/salvar'], 'post') ?>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Access token</label>
                            <?= Html::textarea('access_token', '', [
                                'class' => 'form-control form-control-sm font-monospace',
                                'rows'  => 3,
                                'placeholder' => 'APP_USR-...',
                                'required' => true,
                            ]) ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Refresh token</label>
                            <?= Html::textInput('refresh_token', '', [
                                'class'       => 'form-control form-control-sm font-monospace',
                                'placeholder' => 'TG-...',
                                'required'    => true,
                            ]) ?>
                        </div>
                        <?= Html::submitButton('Salvar tokens', ['class' => 'btn btn-primary w-100']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>

        <!-- Opção 2: Fluxo OAuth -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold mb-1">Renovar via OAuth</h5>
                    <p class="text-muted small mb-3">
                        Use quando o refresh token expirar. Autorize o app no ML
                        e cole o <code>code</code> retornado na URL de redirect.
                    </p>

                    <div class="mb-3">
                        <a href="<?= Url::to(['token/autorizar']) ?>"
                           target="_blank"
                           class="btn btn-outline-warning w-100 mb-2">
                            1. Autorizar no Mercado Livre &nearr;
                        </a>
                        <p class="text-muted small">
                            Após autorizar, copie o <code>code=TG-...</code> da URL de redirect
                            (<code>https://example.com?code=...</code>).
                        </p>
                    </div>

                    <?= Html::beginForm(['token/callback'], 'get') ?>
                        <div class="input-group">
                            <?= Html::textInput('code', '', [
                                'class'       => 'form-control form-control-sm font-monospace',
                                'placeholder' => 'Cole o code aqui: TG-...',
                                'required'    => true,
                            ]) ?>
                            <?= Html::submitButton('2. Trocar', ['class' => 'btn btn-warning btn-sm']) ?>
                        </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4 text-center">
        <?= Html::a('← Voltar para a busca', ['site/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

</div>