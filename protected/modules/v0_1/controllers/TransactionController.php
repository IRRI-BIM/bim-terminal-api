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
            
        } 
        
        else if ($request == 'POST') {

            $data = array();
            $error_array = array();
            $post_vars = array();
            
            parse_str(file_get_contents('php://input'),$post_vars);
            
            // Check required parameters
            if (!empty($post_vars['user'])) {
                $data['user'] = trim($post_vars['user']);
            } else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($post_vars['study_name'])) {
                $data['study_name'] = $post_vars['study_name'];
            }
            else{
                array_push($error_array, "Required field (study_name) is missing.");
            }
            
            // Check optional parameters
            if (!empty($post_vars['remarks'])) {
                $data['remarks'] = trim($post_vars['remarks']);
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
        
        else if($request == 'DELETE'){
            
            $data = array();
            $error_array = array();
            $delete_vars = array();
            
            parse_str(file_get_contents('php://input'),$delete_vars);
            
            // Check required parameters
            if (!empty($delete_vars['user'])) {
                $data['user'] = trim($delete_vars['user']);
            } 
            else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($delete_vars['study_name'])) {
                $data['study_name'] = $delete_vars['study_name'];
            }
            else{
                array_push($error_array, "Required field (study_name) is missing.");
            }
            
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::delete($data);
            }
            
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
            
        }
        
        else if($request == 'PUT'){
            
            $data = array();
            $error_array = array();
            $delete_vars = array();
            
            parse_str(file_get_contents('php://input'),$delete_vars);
            
            // Check required parameters
            if (!empty($delete_vars['user'])) {
                $data['user'] = trim($delete_vars['user']);
            } 
            else {
                array_push($error_array, "Required field (user) is missing.");
            }

            if (!empty($delete_vars['study_name'])) {
                $data['study_name'] = $delete_vars['study_name'];
            }
            else{
                array_push($error_array, "Required field (study_name) is missing.");
            }
            
            // Check optional parameters
            
            
            if(count($error_array) > 0){
                $response['type'] = 'Error';
                $response['timestamp'] = date("Y-m-d H:i");
                $response['response'] = $error_array;
            }
            else{
                $response = TransactionModel::update($data);
            }
            
            $this->_sendResponse(200, CJSON::encode($response), 'text/json');
        }
    }

}
