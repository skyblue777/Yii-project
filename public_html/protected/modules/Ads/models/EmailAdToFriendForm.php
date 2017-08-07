<?php
class EmailAdToFriendForm extends CFormModel
{
    public $senderEmail;
	public $receiverEmail;
	public $content;
    public $verifyCode;
    
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('senderEmail, receiverEmail', 'required'),
			array('senderEmail, receiverEmail', 'email'),
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
		    'senderEmail' => 'Your email address',
            'receiverEmail' => "Your friend's email",
            'content' => 'Message'    
		);
	}
}