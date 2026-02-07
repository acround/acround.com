<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Cookie;
use yii\db\ActiveRecord;
use \app\models\Accountant;
use yii\web\Controller;
use app\services\AuthService;

class AuthController extends Controller
{

    public function actionLogin()
    {
        $request = \Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $accountant = Accountant::findOne(['email' => $username, 'password' => AuthService::encodePassword($password)]);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        if ($accountant) {
            if (!$accountant->token || (strtotime($accountant->updated_at) + Accountant::TOKEN_EXPIRATION_INTERVAL < time())) {
                $accountant->token = $token = $accountant->generateAccessToken();
            } else {
                $token = $accountant->token;
            }
            $cookies = $response->cookies;
            $cookies->add(new Cookie([
                'name' => 'token',
                'value' => $token,
                'expire' => time() + 3600, // Срок действия: 30 дней
                // 'domain' => '.example.com',      // Доступно для поддоменов (необязательно)
                // 'secure' => true,                // Только по HTTPS
                'httpOnly' => true,              // Недоступно через JavaScript (защита от XSS)
            ]));
            $accountant->updated_at = date('Y-m-d H:i:s');
            $accountant->save();
            $session = Yii::$app->getSession();
            $session->set($token, $token);
            $response->data =
                [
                    'status' => 'success',
                    'token' => $token,
                    'user' => $accountant,
                    'code' => 200,
                ];
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => AuthService::ACCESS_TOKEN_NAME,
                'value' => $token,
                'httpOnly' => true,
            ]));
            return $response;
        } else {
            $response->data = [
                'status' => 'error',
                'message' => 'Invalid credentials',
                'code' => 401,
                'email' => $username,
                'hash' => AuthService::encodePassword($password),
                'user' => $accountant,
            ];
        }
        return $response;
    }

    public function actionCheck()
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = null;
        if ($token !== null) {
            $accountant = \app\models\Accountant::findIdentityByAccessToken(['token' => $token]);
            // $session = Yii::$app->getSession();
            // $sToken = $session->get($token, null);
        }
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'token' => $token,
            'accountant' => $accountant,
            // 'session' => $sToken,
            // 'session_valid' => $token == $sToken,
        ];
        return $response;
    }

    public function actionToken()
    {
        $request = \Yii::$app->request;
        $username = $request->post('username');
        $accountant = Accountant::findOne(['email' => $username,]);
        $token = $accountant->token;
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'token' => $token,
        ];
        return $response;
    }

    public static function logout($token = null)
    {
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        if ($accountant && ($accountant instanceof ActiveRecord)) {
            $accountant->token = '';
            $accountant->updated_at = date('Y-m-d H:i:s');
            $accountant->save();
            $session = Yii::$app->getSession();
            $session->remove($token);
            $response->data = [
                'status' => 'success',
                'message' =>
                'Logged out successfully',
                'code' => 200,
            ];
            Yii::$app->response->cookies->remove(AuthService::ACCESS_TOKEN_NAME);
        } else {
            // $response->data = ['status' => 'error', 'message' => 'Invalid token', 'code' => 401];
            $response->data = [
                'status' => 'success',
                'message' => 'Logged out successfully',
                'code' => 401,
            ];
        }
        return $response;
    }

    public function actionLogout()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        return self::logout($token);
    }
}
