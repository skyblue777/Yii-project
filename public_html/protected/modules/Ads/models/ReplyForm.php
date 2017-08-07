<?php
class ReplyForm extends CFormModel
{
    public $senderEmail;
	public $senderName;
	public $content;
    public $verifyCode;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('senderEmail, content', 'required'),
			array('senderEmail', 'email'),
            array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
		    'senderEmail' => Language::t(Yii::app()->language,'Frontend.Ads.Reply','Email address'),
            'senderName' => Language::t(Yii::app()->language,'Backend.Common.Common','Name'),
            'content' => Language::t(Yii::app()->language,'Backend.User.Common','Message')    
		);
	}
}