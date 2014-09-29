<?php

/**
 * This is the model class for table "dataset".
 *
 * The followings are the available columns in table 'dataset':
 * @property integer $id
 * @property integer $transaction_id
 * @property string $input_file
 * @property string $creator
 * @property string $creation_timestamp
 * @property string $modifier
 * @property string $modification_timestamp
 * @property boolean $is_void
 * @property string $remarks
 * @property integer $committed_records_count
 * @property string $status
 *
 * The followings are the available model relations:
 * @property Transaction $transaction
 */
class DatasetBase extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dataset';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('transaction_id, committed_records_count', 'numerical', 'integerOnly'=>true),
			array('creator, modifier', 'length', 'max'=>150),
			array('status', 'length', 'max'=>30),
			array('input_file, creation_timestamp, modification_timestamp, is_void, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, transaction_id, input_file, creator, creation_timestamp, modifier, modification_timestamp, is_void, remarks, committed_records_count, status', 'safe', 'on'=>'search'),
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
			'transaction_id' => 'Transaction',
			'input_file' => 'Input File',
			'creator' => 'Creator',
			'creation_timestamp' => 'Creation Timestamp',
			'modifier' => 'Modifier',
			'modification_timestamp' => 'Modification Timestamp',
			'is_void' => 'Is Void',
			'remarks' => 'Remarks',
			'committed_records_count' => 'Committed Records Count',
			'status' => 'Status',
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
		$criteria->compare('transaction_id',$this->transaction_id);
		$criteria->compare('input_file',$this->input_file,true);
		$criteria->compare('creator',$this->creator,true);
		$criteria->compare('creation_timestamp',$this->creation_timestamp,true);
		$criteria->compare('modifier',$this->modifier,true);
		$criteria->compare('modification_timestamp',$this->modification_timestamp,true);
		$criteria->compare('is_void',$this->is_void);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('committed_records_count',$this->committed_records_count);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DatasetBase the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
