<?php

/**
 * This is the model class for table "transaction".
 *
 * The followings are the available columns in table 'transaction':
 * @property integer $id
 * @property string $status
 * @property string $start_action_timestamp
 * @property string $end_action_timestamp
 * @property string $creator
 * @property string $modifier
 * @property string $modification_timestamp
 * @property boolean $is_void
 * @property string $remarks
 * @property integer $record_count
 * @property integer $invalid_record_count
 * @property string $study_name
 *
 * The followings are the available model relations:
 * @property Dataset[] $datasets
 * @property Record[] $records
 */
class TransactionBase extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'transaction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('record_count, invalid_record_count', 'numerical', 'integerOnly'=>true),
			array('status', 'length', 'max'=>30),
			array('creator, modifier, study_name', 'length', 'max'=>150),
			array('start_action_timestamp, end_action_timestamp, modification_timestamp, is_void, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, start_action_timestamp, end_action_timestamp, creator, modifier, modification_timestamp, is_void, remarks, record_count, invalid_record_count, study_name', 'safe', 'on'=>'search'),
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
			'datasets' => array(self::HAS_MANY, 'Dataset', 'transaction_id'),
			'records' => array(self::HAS_MANY, 'Record', 'transaction_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'start_action_timestamp' => 'Start Action Timestamp',
			'end_action_timestamp' => 'End Action Timestamp',
			'creator' => 'Creator',
			'modifier' => 'Modifier',
			'modification_timestamp' => 'Modification Timestamp',
			'is_void' => 'Is Void',
			'remarks' => 'Remarks',
			'record_count' => 'Record Count',
			'invalid_record_count' => 'Invalid Record Count',
			'study_name' => 'Study Name',
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
		$criteria->compare('status',$this->status,true);
		$criteria->compare('start_action_timestamp',$this->start_action_timestamp,true);
		$criteria->compare('end_action_timestamp',$this->end_action_timestamp,true);
		$criteria->compare('creator',$this->creator,true);
		$criteria->compare('modifier',$this->modifier,true);
		$criteria->compare('modification_timestamp',$this->modification_timestamp,true);
		$criteria->compare('is_void',$this->is_void);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('record_count',$this->record_count);
		$criteria->compare('invalid_record_count',$this->invalid_record_count);
		$criteria->compare('study_name',$this->study_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TransactionBase the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
