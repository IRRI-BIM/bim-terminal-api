<?php

/**
 * This is the model class for table "record".
 *
 * The followings are the available columns in table 'record':
 * @property integer $id
 * @property string $variable
 * @property string $value
 * @property string $data_level
 * @property boolean $is_data_type_valid
 * @property boolean $is_data_value_valid
 * @property string $creator
 * @property string $creation_timestamp
 * @property string $modifier
 * @property string $modification_timestamp
 * @property boolean $is_void
 * @property string $remarks
 * @property boolean $is_committed
 * @property integer $transaction_id
 *
 * The followings are the available model relations:
 * @property Transaction $transaction
 */
class RecordBase extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('transaction_id', 'numerical', 'integerOnly'=>true),
			array('variable, creator, modifier', 'length', 'max'=>150),
			array('value', 'length', 'max'=>255),
			array('data_level', 'length', 'max'=>30),
			array('is_data_type_valid, is_data_value_valid, creation_timestamp, modification_timestamp, is_void, remarks, is_committed', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, variable, value, data_level, is_data_type_valid, is_data_value_valid, creator, creation_timestamp, modifier, modification_timestamp, is_void, remarks, is_committed, transaction_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'transaction' => array(self::BELONGS_TO, 'Transaction', 'transaction_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'variable' => 'Variable',
			'value' => 'Value',
			'data_level' => 'Data Level',
			'is_data_type_valid' => 'Is Data Type Valid',
			'is_data_value_valid' => 'Is Data Value Valid',
			'creator' => 'Creator',
			'creation_timestamp' => 'Creation Timestamp',
			'modifier' => 'Modifier',
			'modification_timestamp' => 'Modification Timestamp',
			'is_void' => 'Is Void',
			'remarks' => 'Remarks',
			'is_committed' => 'Is Committed',
			'transaction_id' => 'Transaction',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('variable',$this->variable,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('data_level',$this->data_level,true);
		$criteria->compare('is_data_type_valid',$this->is_data_type_valid);
		$criteria->compare('is_data_value_valid',$this->is_data_value_valid);
		$criteria->compare('creator',$this->creator,true);
		$criteria->compare('creation_timestamp',$this->creation_timestamp,true);
		$criteria->compare('modifier',$this->modifier,true);
		$criteria->compare('modification_timestamp',$this->modification_timestamp,true);
		$criteria->compare('is_void',$this->is_void);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('is_committed',$this->is_committed);
		$criteria->compare('transaction_id',$this->transaction_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RecordBase the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
