<?php

/**
 * Handles the web service calls to the transaction resource in the data terminal
 * API.
 *
 * @author Jack Elendil B. Lagare <j.lagare@irri.org>
 */
class TransactionController extends ApiController {

    public function actionIndex() {

        //determine request type of the call
        $request = Yii::app()->request->getRequestType();
        $response = array();

        if ($request == 'GET') {

            $data = array();

            if (!empty($_GET['id'])) {
                $data['id'] = trim($_GET['id']);
            }

            //retrieve data from model class
            $result = TransactionModel::get($data);

            $response['type'] = 'Success';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = $result;

            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
            
        } else if ($request == 'POST') {

            $data = array();
            $error_array = array();

            // Check required parameters
            if (!empty($_POST['user'])) {
                $data['user'] = trim($_POST['user']);
            } else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($_POST['study_name'])) {
                $data['study_name'] = $_POST['study_name'];
            }
            else{
                array_push($error_array, "Required field (study_name) is missing.");
            }
            
            // Check optional parameters
            if (!empty($_POST['remarks'])) {
                $data['remarks'] = trim($_POST['remarks']);
            }
            
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::create($data);
            }
            
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
        }
    }

}
