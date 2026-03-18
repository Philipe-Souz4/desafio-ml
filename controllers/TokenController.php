<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use app\models\MeliTokens;

class TokenController extends Controller
{
    /**
     * Tela principal de gerenciamento de tokens.
     * Exibe o status atual e as opções disponíveis.
     */
    public function actionIndex()
    {
        $tokenModel = MeliTokens::find()->one();
        return $this->render('index', ['tokenModel' => $tokenModel]);
    }

    /**
     * Salva um par de tokens diretamente no banco via formulário.
     * POST: access_token, refresh_token
     */
    public function actionSalvar()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['token/index']);
        }

        $accessToken  = trim(Yii::$app->request->post('access_token', ''));
        $refreshToken = trim(Yii::$app->request->post('refresh_token', ''));

        if (!$accessToken || !$refreshToken) {
            Yii::$app->session->setFlash('error', 'Preencha os dois campos.');
            return $this->redirect(['token/index']);
        }

        $tokenModel = MeliTokens::find()->one() ?? new MeliTokens();
        $tokenModel->access_token  = $accessToken;
        $tokenModel->refresh_token = $refreshToken;
        $tokenModel->updated_at    = time();

        if ($tokenModel->save()) {
            Yii::$app->session->setFlash('success', 'Tokens salvos com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar: ' . json_encode($tokenModel->errors));
        }

        return $this->redirect(['token/index']);
    }

    /**
     * Inicia o fluxo OAuth — redireciona para a página de autorização do ML.
     */
    public function actionAutorizar()
    {
        $authUrl = 'https://auth.mercadolivre.com.br/authorization'
            . '?response_type=code'
            . '&client_id=' . Yii::$app->params['meliClientId']
            . '&redirect_uri=https://example.com';

        return $this->redirect($authUrl);
    }

    /**
     * Recebe o authorization_code e o troca por tokens frescos.
     * O ML redireciona para https://example.com?code=TG-...
     * Cole o code manualmente: /token/callback?code=SEU_CODE
     */
    public function actionCallback($code = null)
    {
        if (!$code) {
            Yii::$app->session->setFlash('warning', 'Nenhum code informado.');
            return $this->redirect(['token/index']);
        }

        $params   = Yii::$app->params;
        $client   = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.mercadolibre.com/oauth/token')
            ->addHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
            ->setData([
                'grant_type'    => 'authorization_code',
                'client_id'     => $params['meliClientId'],
                'client_secret' => $params['meliClientSecret'],
                'code'          => $code,
                'redirect_uri'  => 'https://example.com',
            ])
            ->send();

        if (!$response->isOk) {
            $msg = $response->data['message'] ?? 'Erro desconhecido.';
            Yii::$app->session->setFlash('error', "Erro ao trocar o code: {$msg}");
            return $this->redirect(['token/index']);
        }

        $tokenModel = MeliTokens::find()->one() ?? new MeliTokens();
        $tokenModel->access_token  = $response->data['access_token'];
        $tokenModel->refresh_token = $response->data['refresh_token'];
        $tokenModel->updated_at    = time();

        if ($tokenModel->save()) {
            Yii::$app->session->setFlash('success', 'Tokens renovados com sucesso via OAuth.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar tokens no banco.');
        }

        return $this->redirect(['token/index']);
    }
}