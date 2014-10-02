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
        
        //Handle GET requests
        if ($request == 'GET') {

            $data = array();

            if(!empty($_GET['id'])) {
                $data['id'] = trim($_GET['id']);
            }
            
            if (!empty($_GET['limit'])) {
                $data['limit'] = trim($_GET['limit']);
            }

            if (!empty($_GET['offset'])) {
                $data['offset'] = trim($_GET['offset']);
            }

            if (!empty($_GET['sort'])) {
                $data['sort'] = trim($_GET['sort']);
            }

            if(!empty($_GET['fields'])){
                $data['fields'] = trim($_GET['fields']);
            }
            
            //retrieve data from model class
            $result = TransactionModel::get($data);
            
            //prepare response
            $response['type'] = 'Success';
            $response['timestamp'] = date("Y-m-d H:i");
            $response['response'] = $result;
            
            //send response
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
            
        } 
        
        //Handle POST requests
        else if ($request == 'POST') {

            $data = array();
            $error_array = array();
            $post_vars = array();
            
            //parse request variables and store in $post_vars
            parse_str(file_get_contents('php://input'),$post_vars);
            
            // Check required parameters
            if (!empty($post_vars['user'])) {
                $data['user'] = trim($post_vars['user']);
            } else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($post_vars['studyName'])) {
                $data['studyName'] = $post_vars['studyName'];
            }
            else{
                array_push($error_array, "Required field (studyName) is missing.");
            }
            
            // Check optional parameters
            if (!empty($post_vars['remarks'])) {
                $data['remarks'] = trim($post_vars['remarks']);
            }
            
            //prepare response
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::create($data);
            }
            
            //send response
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
        }
        
        //Handle DELETE requests
        else if($request == 'DELETE'){
            
            $data = array();
            $error_array = array();
            $delete_vars = array();
            
            //parse request variables and store in $delete_vars
            parse_str(file_get_contents('php://input'),$delete_vars);
            
            // Check required parameters
            if (!empty($delete_vars['user'])) {
                $data['user'] = trim($delete_vars['user']);
            } 
            else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($delete_vars['studyName'])) {
                $data['studyName'] = $delete_vars['studyName'];
            }
            else{
                array_push($error_array, "Required field (studyName) is missing.");
            }
            
            //prepare response
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::delete($data);
            }
            
            //send response
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
            
        }
        
        //Handle PUT requests
        else if($request == 'PUT'){
            
            $data = array();
            $error_array = array();
            $put_vars = array();
            
            //parse request variables and store in $put_vars
            parse_str(file_get_contents('php://input'),$put_vars);
            
            // Check required parameters
            if (!empty($put_vars['user'])) {
                $data['user'] = trim($put_vars['user']);
            } 
            else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($put_vars['studyName'])) {
                $data['studyName'] = $put_vars['studyName'];
            }
            else{
                array_push($error_array, "Required field (studyName) is missing.");
            }
            
            // Check optional parameters
            if(!empty($put_vars['status'])){
                
                if(strtoupper($put_vars['status']) == 'OPEN' || strtoupper($put_vars['status']) == 'COMMITTED'){
                    $data['status'] = strtoupper($put_vars['status']);
                }
                else{
                    array_push($error_array,"The value for (status) can only be OPEN or COMMITTED.");
                }
                
            }
            
            if(!empty($put_vars['remarks'])){
                $data['remarks'] = $put_vars['remarks'];
            }
            
            if(!empty($put_vars['recordCount'])){
                
                //validate
                if(is_numeric($put_vars['recordCount'])){
                    $data['recordCount'] = $put_vars['recordCount'];
                }
                else{
                    array_push($error_array,"The value for (recordCount) must be an integer.");
                }
            }
            
            if(!empty($put_vars['invalidRecordCount'])){
                
                //validate
                if(is_numeric($put_vars['invalidRecordCount'])){
                    $data['invalidRecordCount'] = $put_vars['invalidRecordCount'];
                }
                else{
                    array_push($error_array,"The value for (invalidRecordCount) must be an integer.");
                }
            }
            
            //prepare response
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::update($data);
            }
            
            //send response
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
        }
    }

}
