<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\City;
use yii\web\Cookie;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $cookies = Yii::$app->request->cookies;

        if (($cookie = $cookies->get(Yii::$app->geo->cookieName)) === null) {

            $ip = Yii::$app->request->userIP;
            //$ip = '176.52.6.40'; // Казань
            //$ip = '178.219.186.12'; // Москва

            $city = Yii::$app->geo->getCity($ip);
        } else {
            $city = Yii::$app->geo->getCity();

        }

        if (!empty($city)) {
            $url = 'http://' . str_replace(' ', '-', $city['city_en']) . '.' . Yii::$app->getRequest()->serverName;
            return $this->redirect($url);
        }

        return $this->render('index');
    }

    public function actionSubdomain()
    {
        $city = Yii::$app->geo->getCity();
        $this->view->params['city'] = $city['name'];

        return $this->render('index');
    }

    /*
     * найти все города по названию
     */
    public function actionFindcity()
    {
        $data = Yii::$app->request->post();

        if (isset($data['city_name'])) {
            $cities = City::findCity($data['city_name']);
            if (!empty($cities)) {
                return json_encode($cities);
            }
        }
    }

    /*
    * установка города
    */
    public function actionSetcity()
    {
        $data = Yii::$app->request->post();

        if (isset($data['id'])) {

            $city = City::findOne($data['id']);
            if (!empty($city)) {
                $cookies = Yii::$app->response->cookies;
                $cookies->remove(Yii::$app->geo->cookieName);


                $cookie = new Cookie([
                    'name' => Yii::$app->geo->cookieName,
                    'value' => $data['id'],
                    'expire' => time() + 3600 * 24 * Yii::$app->geo->cookieExpire,
                    'domain' => '.servergoods.ru' // <<<=== HERE
                ]);

                Yii::$app->response->cookies->add($cookie);

            }
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
