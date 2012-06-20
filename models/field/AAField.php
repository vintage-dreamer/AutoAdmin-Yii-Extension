<?php
/**
 * Parent class that data type format.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
abstract class AAField
{
	/**
	 *
	 * @var string The name of the SQL table the field belongs to.
	 */
	public $tableName;
	/**
	 *
	 * @var string The type of the field. Also the name of a class that must implenets "AAIField" interface.
	 * Here are the predefined types:
	 * - Boolean
	 * - Date
	 * - Datetime (subtype of date)
	 * - Enum
	 * - File
	 * - Image (subtype of file)
	 * - Num
	 * - Password
	 * - String
	 * - Text
	 * - TinyText
	 * See the specification in the classes of these predefined types.
	 */
	public $type;
	/**
	 *
	 * @var string The name of the corresponding SQL field.
	 */
	public $name;
	/**
	 *
	 * @var string Label of the field
	 */
	public $label;
	/**
	 *
	 * @var mixed Value
	 */
	public $value;
	/**
	 * 
	 * @var string Description of the field for users.
	 */
	public $description;
	/**
	 *
	 * @var mixed Default value
	 */
	public $defaultValue;
	/**
	 *
	 * @var bool Whether readonly paramater
	 */
	public $isReadonly = false;
	/**
	 *
	 * @var bool Whether value must be shown in list mode
	 */
	public $showInList = false;
	/**
	 *
	 * @var bool Whether can be ordered be in list mode
	 */
	public $allowOrder = false;
	/**
	 *
	 * @var bool Whether can be searched by in list mode
	 */
	public $allowSearch = false;
	/**
	 *
	 * @var bool Whether allows null
	 */
	public $allowNull = false;
	/**
	 *
	 * @var array Custom options defined by user or internally in child classes.
	 */
	public $options = array();
	/**
	 *
	 * @var string|int The value is defined by external parameters
	 */
	public $bind;
	/**
	 *
	 * @var bool Whether the field was changed for commiting in DB. 
	 */
	public $isChanged = false;

	public function testOptions()
	{
		if(!$this->allowNull && is_null($this->defaultValue==null))
			return false;
		return true;
	}

	public function completeOptions()
	{
	}

	public function printValue()
	{
		return $this->value;
	}

	public function formInputName()
	{
		return AutoAdmin::INPUT_PREFIX."[{$this->name}]";
	}

	public function printFormNullCB()
	{
		echo CHtml::checkBox(AutoAdmin::INPUT_PREFIX."[AAnullf][{$this->name}]", !is_null($this->value), array('class'=>'nullf'));
	}

	public function formInput(&$controller, $tagOptions=array())
	{
		ob_start();
		$inputName = $this->formInputName();

		$inputID = "i_{$inputName}";
		echo CHtml::label($this->label, $inputID);
		echo CHtml::tag('br');
		if($this->allowNull)
			$this->printFormNullCB();
		$tagOptions['id'] = $inputID;
		echo CHtml::textField($inputName, AAHelperForm::prepareTextForForm(((string)$this->value ? $this->value : $this->defaultValue)), $tagOptions);

		return ob_get_clean();
	}

	public function loadFromForm($formData)
	{
		if(isset($formData[$this->name]))
			$this->value = $formData[$this->name];
		else
		{
			if($this->allowNull)
				$this->value = null;
			else
				throw new AAException(Yii::t('AutoAdmin.errors', 'The field "{field}" cannot be NULL but it can be passed by the form', array('{field}'=>$this->name)));
		}
	}

	public function loadFromSql($queryValue)
	{
		if(isset($queryValue[$this->name]))
			$this->value = $queryValue[$this->name];
	}

	public function valueForSql()
	{
		if(is_null($this->value))
		{
			if(!is_null($this->defaultValue))
				return $this->defaultValue;
			elseif($this->allowNull)
				return new CDbExpression('NULL');
			else
				throw new AAException(Yii::t('AutoAdmin.errors', 'The field "{field}" cannot be NULL but it can be passed by the form', array('{field}'=>$this->name)));
		}
		else
			return $this->value;
	}

	/**
	 * public function modifySqlQuery();
	 */
}