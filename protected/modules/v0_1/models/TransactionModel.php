<?php

/**
 * Description of TransactionModel
 *
 * @author Jack Elendil B. Lagare <j.lagare@irri.org>
 */
class TransactionModel {

    /**
     * Appends SQL condition to an array of SQL conditions.
     * 
     * @param array $conditionArray String array containing the SQL condition
     * @param type $conditionString String SQL condition to append to the condition array
     * @return string array containing the modified condition array
     */
    public static function filter($conditionArray, $conditionString) {

        $conditionArray[] = '(' . $conditionString . ')';

        return $conditionArray;
    }

    /**
     * Handles the retrieval of data from the database and provide a response
     * to the GET request.
     * 
     * @param type $data array containing the parameters of the request
     * @return String array containing the formatted response.
     * @author Jack Elendil B. Lagare
     */
    public static function get($data = NULL) {

        $condition = '';
        $limitCondition = '';
        $conditionArray = array();
        $sortCondition = '';

        extract($data);

        //Columns for selection in Sorting
        $tableColumns = Yii::app()->db->schema->getTable('api.api_transaction')->getColumnNames();

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

    /**
     * Handles the creation of a new object and provide a response to the
     * POST request.
     * 
     * @param type $data array containing the parameters of the request
     * @return type String array containing the formatted response.
     * @author Jack Elendil B. Lagare <j.lagare@irri.org>
     */
    public static function create($data = NULL) {

        extract($data);
        $response = array();

        //Check if there is an open transaction for the study
        $criteria = new CDbCriteria();
        $criteria->compare('UPPER(study_name)', strtoupper(trim($studyName)));
        $criteria->compare('status', 'OPEN');

        $transaction = Transaction::model()->findAll($criteria);

        if (count($transaction) > 0) {

            $response['type'] = 'Warning';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = 'An open transaction for ' . $studyName . ' already exists. Send a PUT request instead to update the resource.';
        } else {
            $databaseTransaction = Yii::app()->db->beginTransaction();
            try {
                $transaction = new Transaction();

                $transaction->status = 'OPEN';
                $transaction->start_action_timestamp = new CDbExpression('NOW()');
                $transaction->creator = strtoupper(trim($user));

                if (isset($remarks)) {
                    $transaction->remarks = $remarks;
                }

                $transaction->study_name = $studyName;
                $transaction->save();
                $databaseTransaction->commit();


                $response['type'] = 'Success';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'Transaction for ' . $studyName . ' was successfully created.';
            } catch (Exception $ex) {

                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'There was an error creating the transaction.';
                $response['error_details'] = $ex->getMessage();

                $databaseTransaction->rollback();
            }
        }

        return $response;
    }

    /**
     * Handles the deletion of an object and provide a response to the
     * DELETE request.
     * 
     * @param type $data array containing the parameters of the request
     * @return type String array containing the formatted response.
     * @author Jack Elendil B. Lagare <j.lagare@irri.org>
     */
    public static function delete($data = NULL) {

        extract($data);
        $response = array();

        //Check if there is an open transaction for the study
        $criteria = new CDbCriteria();
        $criteria->compare('UPPER(study_name)', strtoupper(trim($studyName)));
        $criteria->compare('status', 'OPEN');

        $transaction = Transaction::model()->findAll($criteria);

        if (count($transaction) <= 0) {

            $response['type'] = 'Error';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = 'There is no open transaction for ' . $studyName . '.';
        } else {
            $databaseTransaction = Yii::app()->db->beginTransaction();

            try {
                $criteria = new CDbCriteria();
                $criteria->compare('UPPER(study_name)', strtoupper(trim($studyName)));
                $criteria->compare('status', 'OPEN');

                $transaction = Transaction::model()->find($criteria);

                $transaction->delete();

                $response['type'] = 'Success';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'The open transaction for ' . $studyName . ' has been successfully deleted.';

                $databaseTransaction->commit();
            } catch (Exception $ex) {

                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'There was an error deleting the transaction.';
                $response['error_details'] = $ex->getMessage();

                $databaseTransaction->rollback();
            }
        }

        return $response;
    }

    /**
     * Handles the updating of an object and provide a response to the
     * PUT request.
     * 
     * @param type $data array containing the parameters of the request
     * @return type String array containing the formatted response.
     * @author Jack Elendil B. Lagare <j.lagare@irri.org>
     */
    public static function update($data = NULL) {

        extract($data);

        $response = array();
        $flag = 0;

        //Check if there is an open transaction for the study
        $criteria = new CDbCriteria();
        $criteria->compare('UPPER(study_name)', strtoupper(trim($studyName)));
        $criteria->compare('status', 'OPEN');

        $transaction = Transaction::model()->findAll($criteria);

        if (count($transaction) <= 0) {

            $response['type'] = 'Error';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = 'There is no open transaction for ' . $studyName . '.';
        } else {
            $databaseTransaction = Yii::app()->db->beginTransaction();

            try {
                $criteria = new CDbCriteria();
                $criteria->compare('UPPER(study_name)', strtoupper(trim($studyName)));
                $criteria->compare('status', 'OPEN');

                $transaction = Transaction::model()->find($criteria);

                if (!empty($status)) {
                    $transaction->status = $status;
                    $flag = 1;
                }

                if (!empty($remarks)) {
                    $transaction->remarks = $remarks;
                    $flag = 1;
                }

                if (!empty($recordCount)) {
                    $transaction->record_count = $recordCount;
                    $flag = 1;
                }

                if (!empty($invalidRecordCount)) {
                    $transaction->invalid_record_count = $invalidRecordCount;
                    $flag = 1;
                }

                if ($flag == 1) {
                    $transaction->modifier = strtoupper(trim($user));
                    ;

                    $transaction->modification_timestamp = new CDbExpression('NOW()');

                    $transaction->save();

                    $response['type'] = 'Success';
                    $response['timestamp'] = date("Y-m-d H:i");
                    $response['response'] = 'The open transaction for ' . $studyName . ' has been successfully updated.';

                    $databaseTransaction->commit();
                } else {
                    $response['type'] = 'Warning';
                    $response['timestamp'] = date("Y-m-d H:i");
                    $response['response'] = 'The request did not contain any attributes to update. The open transaction for ' . $study_name . ' was not updated.';
                }
            } catch (Exception $ex) {

                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = 'There was an error updating the transaction.';
                $response['error_details'] = $ex->getMessage();

                $databaseTransaction->rollback();
            }
        }

        return $response;
    }

}
