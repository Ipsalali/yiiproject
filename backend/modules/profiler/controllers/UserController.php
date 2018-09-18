<?php

namespace backend\modules\profiler\controllers;


use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use \yii\web\{Controller,BadRequestHttpException};
use yii\base\InvalidParamException;

class UserController extends Controller
{
    

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','profile'],
                        'roles' => ['superadmin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm-email'],
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }





    public function actionIndex(){
        return $this->render('index',[]);
    }



    public function actionProfile()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(Url::toRoute('/site/login'));
        }
        $module = Yii::$app->controller->module;
        
        $model = Yii::$app->user->identity;
        
        $changePasswordFormModel = $module->modelNamespace.'\forms\ChangePasswordForm';
        $changePasswordForm = new $changePasswordFormModel;
        
        //$changeEmailFormModel = $module->modelNamespace.'\forms\ChangeEmailForm';
        //$changeEmailForm = new $changeEmailFormModel;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('profiler', 'CHANGES_WERE_SAVED'));

            return $this->redirect(['/profiler/user/profile']);
        }

        if ($model->Password != '') {
            $changePasswordForm->scenario = 'requiredOldPassword';
        }

        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            $model->setPassword($changePasswordForm->new_password);
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('profiler', 'NEW_PASSWORD_WAS_SAVED'));
                return $this->redirect(['/profiler/user/profile']);
            }
        }

        // $MailerConfirmModel = $module->modelNamespace.'\MailerConfirm';
        // $MailerConfirm = new $MailerConfirmModel;
        // if ($changeEmailForm->load(Yii::$app->request->post()) && $changeEmailForm->validate() && $MailerConfirm->setEmail($changeEmailForm->new_email)) {
        //     Yii::$app->getSession()->setFlash('success', Yii::t('profiler', 'TO_YOURS_EMAILS_WERE_SEND_MESSAGES_WITH_CONFIRMATIONS'));
        //     return $this->redirect(['/profiler/user/profile']);
        // }

        return $this->render($this->module->getCustomView('profile'), [
            'model' => $model,
            'changePasswordForm' => $changePasswordForm,
            //'changeEmailForm' => $changeEmailForm
        ]);
    }


    public function actionConfirmEmail($token){

        $module = Yii::$app->controller->module;
        $MailerConfirmModel = $module->modelNamespace.'\MailerConfirm';
        $MailerConfirm = new $MailerConfirmModel;

        try {
            $res = $MailerConfirm::confirmEmail($token);
            if($res){
                Yii::$app->getSession()->setFlash('success', Yii::t('profiler', 'YOURS_EMAILS_CONFIRMED'));
            }else{
                Yii::$app->getSession()->setFlash('danger', Yii::t('profiler', 'YOURS_EMAILS_NOT_CONFIRMED'));
            }
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        
        return $this->redirect(['/profiler/user/profile']);
    }

}
