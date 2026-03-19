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
            throw new \Exception('Credenciais não encontradas. Acesse /token para configurar.');
        }

        $client     = new Client();
        $response   = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("https://api.mercadolibre.com/items/{$meli_id}")
            ->addHeaders(['Authorization' => 'Bearer ' . $tokenModel->access_token])
            ->send();

        $statusCode = (int) $response->getStatusCode();

        // Token expirado: renova e tenta novamente uma única vez
        if ($statusCode === 401 && !$isRetry) {
            Yii::info("Token expirado para {$meli_id}. Tentando renovar...", __METHOD__);

            if ($this->renewToken($tokenModel)) {
                Yii::info("Token renovado. Repetindo requisição para {$meli_id}...", __METHOD__);
                return $this->obterDadosProduto($meli_id, true);
            }

            throw new \Exception('Token expirado e não foi possível renovar automaticamente. Acesse /token para renovar.');
        }

        // Retry também retornou 401 — token recém renovado não funcionou
        if ($statusCode === 401 && $isRetry) {
            throw new \Exception('Falha após renovação do token. Acesse /token para renovar manualmente.');
        }

        if ($statusCode === 404) {
            throw new \Exception("Produto '{$meli_id}' não encontrado no Mercado Livre.");
        }

        if (!$response->isOk) {
            $msg = $response->data['message'] ?? 'Erro desconhecido na API.';
            throw new \Exception("Erro {$statusCode} ao buscar produto: {$msg}");
        }

        // Retorna apenas os campos exigidos pelo desafio
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
                'client_id'     => (string) $params['meliClientId'],
                'client_secret' => $params['meliClientSecret'],
                'refresh_token' => $tokenModel->refresh_token,
            ])
            ->send();

        if ($response->isOk) {
            $tokenModel->access_token  = $response->data['access_token'];
            $tokenModel->refresh_token = $response->data['refresh_token'];
            $tokenModel->updated_at    = time();

            $saved = $tokenModel->save();

            if (!$saved) {
                Yii::error('Falha ao salvar token renovado: ' . json_encode($tokenModel->errors), __METHOD__);
            } else {
                Yii::info('Token salvo no banco com sucesso.', __METHOD__);
            }

            return $saved;
        }

        Yii::error(
            'Falha ao renovar token. Status: ' . $response->getStatusCode() .
            ' | Resposta: ' . json_encode($response->data),
            __METHOD__
        );

        return false;
    }
}