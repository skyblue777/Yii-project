<?php
/**
* Rights base controller class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.6
*/
class RightsBaseController extends CController
{
	/**
	* @var string the default layout for the controller view. Defaults to '//layouts/column1',
	* meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	*/
	public $layout='//layouts/column1';
	/**
	* @var array context menu items. This property will be assigned to {@link CMenu::items}.
	*/
	public $menu=array();
	/**
	* @var array the breadcrumbs of the current page. The value of this property will
	* be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	* for more details on how to specify this property.
	*/
	public $breadcrumbs=array();

	/**
	* The filter method for 'rights' access filter.
	* This filter is a wrapper of {@link CAccessControlFilter}.
	* @param CFilterChain the filter chain that the filter is on.
	*/
	public function filterRights($filterChain)
	{
		$filter = new RightsFilter;
		$filter->allowedActions = $this->allowedActions();
		$filter->filter($filterChain);
	}

	/**
	* @return string the actions that are always allowed separated by commas.
	*/
	public function allowedActions()
	{
		return '';
	}

	/**
	* Denies the access of the user.
	* This method is invoked when access check fails.
	* @param IWebUser the current user
	*/
	public function accessDenied($user)
	{
		if( $user->getIsGuest()===true )
			$user->loginRequired();
		else
			throw new CHttpException(403, Yii::t('AuthModule.core', 'You are not authorized to perform this action.'));
	}
}
