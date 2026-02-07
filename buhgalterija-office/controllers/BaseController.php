<?php

namespace app\controllers;

use Yii;
use \yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function renderPage($data, $view = 'page')
    {
        $html = $this->renderPartial($view, $data);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'data' => $html,
        ];
        if (isset($data['debug'])) {
            $response->data['debug'] = $data['debug'];
        }
        return $response;
    }

    protected function renderLogout($data = [])
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'logout',
            'code' => 403,
            'message' => 'Invalid or expired token.',
            'data' => $data,
        ];
        return $response;
    }

    protected function getN8nWebhookBaseUrl()
    {
        // return 'https://n8n.acround.com/webhook/';
        return getenv('N8N_WEBHOOK_BASE_URL');
    }

    protected function makeN8nWebhookCall($path, $data)
    {
        $url = $this->getN8nWebhookBaseUrl() . $path;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $response = curl_exec($ch);
        if ($response) {
            $response = json_decode($response, true);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $finalResponse = [
                'status' => $code == 200 ? 'success' : 'error',
                'code' => $code,
            ];
            if (isset($response['message'])) {
                $finalResponse['message'] = $response['message'];
            }
            if (isset($response['hint'])) {
                $finalResponse['hint'] = $response['hint'];
            }
            $finalResponse['data'] = $response;
            $finalResponse['debug'] = [
                'url' => $url,
                'data' => $data,
                'curl_info' => curl_getinfo($ch),
            ];
            return $finalResponse;
        } else {
            return [
                'status' => 'error',
                'message' => curl_error($ch),
                'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                'debug' => [
                    'url' => $url,
                    'data' => $data,
                ],
            ];
        }
    }
}
