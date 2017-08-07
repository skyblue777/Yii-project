<?php
/**
 * SettingInfoForm.php
 *
 * PHP version 5
 *
 * @category  KnowledgeBase
 * @package   Install
 * @author    Phong Quach <phong.quach@webflexica.com>
 * @copyright 2009 Webflexica
 * @license   http://www.webflexica.com/license/ license
 * @version   SVN: $Id$
 * @link      http://knowledgebase.webflexica.com/
 */

/**
 * SettingInfoForm class
 *
 * @category  KnowledgeBase
 * @package   Install
 * @author    Phong Quach <phong.quach@webflexica.com>
 * @copyright 2009 Webflexica
 * @license   http://www.webflexica.com/license/ license
 * @version   Release: @package_version@
 * @link      http://knowledgebase.webflexica.com/
 */
class SettingInfoForm extends CFormModel {
    public $siteName;
    public $adminEmail;

    /**
     * Returns the validation rules for attributes.
     *
     * This method should be overridden to declare validation rules.
     * Each rule is an array with the following structure:
     * <pre>
     * array('attribute list', 'validator name', 'on'=>'scenario name', ...validation parameters...)
     * </pre>
     * where
     * <ul>
     * <li>attribute list: specifies the attributes (separated by commas) to be validated;</li>
     * <li>validator name: specifies the validator to be used. It can be the name of a model class
     *   method, the name of a built-in validator, or a validator class (or its path alias).
     *   A validation method must have the following signature:
     * <pre>
     * // $params refers to validation parameters given in the rule
     * function validatorName($attribute,$params)
     * </pre>
     *   A built-in validator refers to one of the validators declared in {@link CValidator::builtInValidators}.
     *   And a validator class is a class extending {@link CValidator}.</li>
     * <li>on: this specifies the scenarios when the validation rule should be performed.
     *   Separate different scenarios with commas. If this option is not set, the rule
     *   will be applied in any scenario. Please see {@link scenario} for more details about this option.</li>
     * <li>additional parameters are used to initialize the corresponding validator properties.
     *   Please refer to individal validator class API for possible properties.</li>
     * </ul>
     *
     * The following are some examples:
     * <pre>
     * array(
     *     array('username', 'required'),
     *     array('username', 'length', 'min'=>3, 'max'=>12),
     *     array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'register'),
     *     array('password', 'authenticate', 'on'=>'login'),
     * );
     * </pre>
     *
     * Note, in order to inherit rules defined in the parent class, a child class needs to
     * merge the parent rules with child rules using functions like array_merge().
     *
     * @return array validation rules to be applied when {@link validate()} is called.
     * @see scenario
     */
    public function rules() {
        return array(
            array('siteName, adminEmail', 'required'),
            array('adminEmail', 'email'),
        );
    }

    /**
     * Returns the attribute labels.
     * Attribute labels are mainly used in error messages of validation.
     * By default an attribute label is generated using {@link generateAttributeLabel}.
     * This method allows you to explicitly specify attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions like array_merge().
     *
     * @return array attribute labels (name=>label)
     * @see generateAttributeLabel
     */
    public function attributeLabels() {
        return array(
			'siteName'=>'Site Name',
            'adminEmail'=>'Your E-mail',
        );
    }
}