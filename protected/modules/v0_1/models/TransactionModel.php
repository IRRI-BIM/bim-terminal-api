<?php

/**
 * Description of TransactionModel
 *
 * @author Jack Elendil B. Lagare <j.lagare@irri.org>
 */
class TransactionModel {

    public static function filter($conditionArray, $conditionString) {

        $conditionArray[] = '(' . $conditionString . ')';

        return $conditionArray;
    }

    public static function get($data = NULL) {

        $condition = '';
        $limitCondition = '';
        $conditionArray = array();
        $sortCondition = '';

        extract($data);

        /*
         * Filtering of data 
         */
        if (isset($id)) {

            if (is_numeric($id)) {
                $strCond = ' s.id = ' . trim($id);
            }

            if (!empty($strCond) and $strCond != ' ') {
                $conditionArray = TransactionModel::filter($conditionArray, $strCond);
            }
        }

        if (!empty($conditionArray)) {
            $condition = ' AND ( ' . implode(" AND", $conditionArray) . ')';
        }

        /**
         * Handling of offsets and limits
         */
        if (!isset($offset) && isset($limit)) { // no offset but limit is provided
            $offset = 0;
        } else if (isset($offset) && !isset($limit)) {  //offset is provided but no limit
            $offset = null;
        } else if (!isset($offset) && !isset($limit)) { // no offset and no limit, this is the default
            $offset = null;
        }

        if (isset($limit)) {
            $limitCondition = "LIMIT " . $limit . " OFFSET " . $offset;
        } else {
            $limit = null;
        }

        /**
         * Handling of sorting
         */
        if (isset($sort)) {

            $sortStr = '';
            $sortCols = explode(',', $sort);
            for ($i = 0; $i < count($sortCols); $i++) {
                if ($i == (count($sortCols) - 1)) {
                    $sortStr .= $sortCols[$i];
                } else {
                    $sortStr .= $sortCols[$i] . ", ";
                }
            }

            $sortCondition = "ORDER BY " . $sortStr;
        }

        if (isset($fields)) {

            $selectStr = '';
            $selectCols = explode(',', $fields);
            for ($i = 0; $i < count($selectCols); $i++) {

                $selectedCol = trim($selectCols[$i]);
                if (!in_array($selectedCol, $tableColumns)) {
                    continue;
                }

                $appendedStr = 's.' . $selectedCol;

                if ($selectStr == '') {
                    $selectStr = $appendedStr;
                } else {
                    $selectStr = $selectStr . ',' . $appendedStr;
                }
            }

            if ($selectStr == '') {
                $selectStr = 's.*';
            }

            $selectStr = rtrim($selectStr, ',');
        } else {
            $selectStr = 's.*';
        }

        $sql = <<<EOD
            SELECT
                {$selectStr}
            FROM
                transaction s
            WHERE
                s.is_void = FALSE
                {$condition}  
                {$sortCondition}
                {$limitCondition}
EOD;

        $countSql = <<<EOD
            SELECT
                COUNT(1)
            FROM
                transaction s
           
            WHERE
                s.is_void = FALSE
                {$condition}
EOD;

        $results = Yii::app()->db->createCommand($sql)->queryAll();
        $count = Yii::app()->db->createCommand($countSql)->queryAll();



        $rows = array();
        foreach ($results as $key) {
            if (is_null($key['modifier'])) {
                $key["modifier"] = null;
            }

            array_push($rows, $key);
        }

        if (count($rows) < 1) {
            $response = 'No records found.';
        } else {
            $response = array(
                "totalRows" => intval($count[0]["count"]),
                "limit" => intval($limit),
                "offset" => intval($offset),
                "rows" => $rows,
                    //"columns" => $columns,
            );
        }

        return $response;
    }

    public static function create($data = NULL) {

        extract($data);
        $response = array();
        
        //Check if there is an open transaction for the study
        $criteria = new CDbCriteria();
        $criteria->compare('UPPER(study_name)',strtoupper(trim($study_name)));
        $criteria->compare('status','OPEN');
        
        $transaction = Transaction::model()->findAll($criteria);
        
        if(count($transaction) > 0){
            
            $response['type'] = 'Warning';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = 'An open transaction for '.$study_name.' already exists. Send a PUT request instead to update the resource.';
        }
        else{
            $databaseTransaction = Yii::app()->db->beginTransaction();
            try{
                $transaction = new Transaction();
            
                $transaction->status = 'OPEN';
                $transaction->start_action_timestamp = new CDbExpression('NOW()');
                $transaction->creator = strtoupper(trim($user));

                if(isset($remarks)){
                    $transaction->remarks = $remarks;
                }

                $transaction->study_name = $study_name;
                $transaction->save();
                $databaseTransaction->commit();
                
                
                $response['type'] = 'Success';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'Transaction for '.$study_name.' was successfully created.';
            } 
            catch (Exception $ex) {
                
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'There was an error creating the transaction.';
                $response['error_details'] = $ex->getMessage();
                
                $databaseTransaction->rollback();
            }
         
        }
        
        return $response;
    }
    
    public static function delete($data = NULL){
        
        extract($data);
        $response = array();
        
        //Check if there is an open transaction for the study
        $criteria = new CDbCriteria();
        $criteria->compare('UPPER(study_name)',strtoupper(trim($study_name)));
        $criteria->compare('status','OPEN');
        
        $transaction = Transaction::model()->findAll($criteria);
        
        if(count($transaction) <= 0){
            
            $response['type'] = 'Error';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = 'There is no open transaction for '.$study_name.'.';
        }
        
        else{
            $databaseTransaction = Yii::app()->db->beginTransaction();
            
            try{
                $criteria = new CDbCriteria();
                $criteria->compare('UPPER(study_name)',strtoupper(trim($study_name)));
                $criteria->compare('status','OPEN');

                $transaction = Transaction::model()->find($criteria);
                
                $transaction->delete();
                
                $response['type'] = 'Success';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'The open transaction for '.$study_name.' has been successfully deleted.';
                
                $databaseTransaction->commit();
            } 
            catch (Exception $ex) {
                
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'There was an error deleting the transaction.';
                $response['error_details'] = $ex->getMessage();
                
                $databaseTransaction->rollback();
            }
        }
        
        return $response;
    }

}
