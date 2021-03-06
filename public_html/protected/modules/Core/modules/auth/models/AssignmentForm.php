<?php
/**
* Auth item assignment form class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9
*/
class AssignmentForm extends CFormModel
{
	public $authItem;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('authItem', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'authItem' => Language::t(Yii::app()->language,'Backend.System.Assignment','Auth Item'),
		);
	}
}
