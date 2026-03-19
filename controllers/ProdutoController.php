<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use app\models\MeliTokens;

class ProdutoController extends Controller
{
    /**
     * Recebe o meli_id via GET e retorna as informações do produto do ML.
     */
    public function actionDetalhes($meli_id = null)
    {
        $meli_id = $meli_id ?? Yii::$app->request->get('meli_id');

        if (!$meli_id) {
            Yii::$app->session->setFlash('warning', 'Por favor, insira um ID de produto.');
            return $this->redirect(['site/index']);
        }

        // Remove traços e espaços do ID — MLB-123 -> MLB123
        $meli_id = str_replace(['-', ' '], '', strtoupper(trim($meli_id)));

        try {
            $produto = $this->obterDadosProduto($meli_id);
            return $this->render('detalhes', ['produto' => $produto]);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Obtém os dados do produto via API do Mercado Livre.
     * Em caso de token expirado (401), renova automaticamente e tenta novamente.
     *
     * @param  string $meli_id  ID do produto no ML
     * @param  bool   $isRetry  Indica se já é uma segunda tentativa após renovação
     * @return array            Dados relevantes do produto
     * @throws \Exception       Em caso de erro na API
     */
    protected function obterDadosProduto(string $meli_id, bool $isRetry = false): array
    {
        $tokenModel = MeliTokens::find()->one();

        if (!$tokenModel) {
            throw new \Exception('Credenciais não encontradas. <a href="/token/index">Configure os tokens de acesso</a>.');
        }

        $client   = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("https://api.mercadolibre.com/items/{$meli_id}")
            ->addHeaders(['Authorization' => 'Bearer ' . $tokenModel->access_token])
            ->send();

        // Token expirado: renova e tenta novamente uma única vez
        if ($response->getStatusCode() === 401 && !$isRetry) {
            if ($this->renewToken($tokenModel)) {
                return $this->obterDadosProduto($meli_id, true);
            }
            throw new \Exception('Sessão expirada. <a href="/token/index">Renove o token de acesso</a>.');
        }

        if ($response->getStatusCode() === 404) {
            throw new \Exception("Produto '{$meli_id}' não encontrado no Mercado Livre.");
        }

        if (!$response->isOk) {
            $msg = $response->data['message'] ?? 'Erro desconhecido na API.';
            throw new \Exception("Erro ao buscar produto: {$msg}");
        }

        // Retorna apenas os campos exigidos
        $data = $response->data;
        return [
            'id'                 => $data['id']                 ?? '—',
            'title'              => $data['title']              ?? '—',
            'category_id'        => $data['category_id']        ?? '—',
            'price'              => $data['price']              ?? 0,
            'available_quantity' => $data['available_quantity'] ?? 0,
            'thumbnail'          => $data['thumbnail']          ?? '',
            'permalink'          => $data['permalink']          ?? '',
        ];
    }

    /**
     * Renova o access_token usando o refresh_token e persiste ambos no banco.
     * O Mercado Livre gera um novo refresh_token a cada renovação.
     */
    protected function renewToken(MeliTokens $tokenModel): bool
    {
        $params   = Yii::$app->params;
        $client   = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.mercadolibre.com/oauth/token')
            ->addHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
            ->setData([
                'grant_type'    => 'refresh_token',
                'client_id'     => $params['meliClientId'],
                'client_secret' => $params['meliClientSecret'],
                'refresh_token' => $tokenModel->refresh_token,
            ])
            ->send();

        if ($response->isOk) {
            $tokenModel->access_token  = $response->data['access_token'];
            $tokenModel->refresh_token = $response->data['refresh_token'];
            $tokenModel->updated_at    = time();
            return $tokenModel->save();
        }

        return false;
    }
}