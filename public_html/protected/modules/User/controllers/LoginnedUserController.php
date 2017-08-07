<?php
class LoginnedUserController extends FrontController
{
    public function actionViewMyAds()
    {
        if (Yii::app()->user->isGuest)
        {
            $this->render('require_login');
        }
        else
        {
            Yii::import('Ads.models.Annonce');
            $criteria=new CDbCriteria;
            $criteria->compare('email',Yii::app()->user->email);
            $criteria->compare('public',1);
            $criteria->order = "create_time DESC";
            $dataProvider = new CActiveDataProvider('Annonce',array(
                'criteria'=>$criteria,
                'pagination'=>array(
                    'pageSize'=>25
                ),
            ));
            
            $this->render('view_my_ads',array('dataProvider'=>$dataProvider));
        }
    }
    
    public function actionViewMyFavoriteAds()
    {
        if (Yii::app()->user->isGuest)
        {
            $this->render('require_login');
        }
        else
        {
            Yii::import('Ads.models.Annonce');
            $criteria=new CDbCriteria;
            $criteria->join = "INNER JOIN ann_favorites ON t.id = ann_favorites.annonce_id";
            $criteria->compare('ann_favorites.user_id',Yii::app()->user->id);
            $criteria->compare('t.public',1);
            $criteria->order = "create_time DESC";
            $dataProvider = new CActiveDataProvider('Annonce',array(
                'criteria'=>$criteria,
                'pagination'=>array(
                    'pageSize'=>25
                ),
            ));
            
            $this->render('view_my_favorite_ads',array('dataProvider'=>$dataProvider));   
        }
    }
    
    public function actionMyProfile()
    {
        if (Yii::app()->user->isGuest)
        {
            $this->render('require_login');
        }
        else
        {
            $user = User::model()->findByPk(Yii::app()->user->id);
            if (is_null($user))
                throw new CHttpException(400,Yii::t('Ads.Ads','Sorry! Your account does not exist'));
            $user->setScenario('edit_profile');
            $updateSuccessfully = FALSE;
            
            if (Yii::app()->request->IsPostRequest)
            {
                $user->setAttributes($_POST['User'],FALSE);
                $user->confirmPassword = trim($_POST['User']['confirmPassword']);
                $user->password = trim($user->password);
                if ($user->validate())
                {
                    $updatedAttrs = array(
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                    );
                    if ($user->password!='')
                        $updatedAttrs['password'] = md5($user->password);
                    User::model()->updateByPk($user->id,$updatedAttrs);
                    $updateSuccessfully = TRUE;
                }    
            }
            else
            {
                $user->password = '';
            }
            
            $this->render('my_profile',array('model'=>$user,'updateSuccessfully'=>$updateSuccessfully));   
        }        
    }   
}
