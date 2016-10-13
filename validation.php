<?php
/**
* Developer : Siddharajsinh Zala.
* Contact: zalasid.mca@gmail.com / 834 76 76 729
* Description : Here I have developed a custom validation for WS request param or WS rules.
*/

class Validation
{

  // Uncomment line for a use of custom lable in error message.
  public $customMsg = ["first_name"=>"First Name","email"=>"Email","contact"=>"Contact Number"];
  // and please uncomment $msg variable for custom message and comment olg $msg


  public $conn;
  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Create a connection with database.
  */
  function __construct(){

      $host = "localhost";
      $username = "root";
      $password = "ln";
      $database = "mydb";

      $this->conn = mysqli_connect($host,$username,$password,$database);
      if(!$this->conn){
        echo "Database Connection Error";
      }
  }
  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : This is main method. That execute both array request_data and rules
  */
  public function checkValidation($request_data,$rules){

    // Main loop that traverse a request_data values.
    foreach ($request_data as $key => $value) {
      // check for which field rules is define.
      if(array_key_exists($key,$rules)){
        // if one filed have multiple validation then explode validation methods.
        $validationRule = explode("|",$rules[$key]);
        // call that all validation method.
        foreach ($validationRule as $methodName) {

          $dbValidation = explode(":",$methodName);

          if(sizeof($dbValidation)>=2){
            // mendetory fields
            // print_r($dbValidation);
            $methodName = $dbValidation[0];
            $table = $dbValidation[1];
            $fieldName = $key;
            // optional fields
            $uniqueFieldName =  (isset($dbValidation[2]) && !empty($dbValidation[2])?$dbValidation[2]:"");
            $id = (isset($dbValidation[3]) && !empty($dbValidation[3])?$dbValidation[3]:"");
            $returnData = $this->$methodName($value,$fieldName,$table,$uniqueFieldName,$id);
          }else {
            // make a validation rule name as a function name.
            $returnData = $this->$methodName($value,$key);
          }
          //if error so it return json error message.
          if($returnData!=$value){
            // return a error message.
            return $returnData;
          }
        }
      }
    }
    // if all rules are setisfying conditions then return true.
    return true;
  }
  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Check a given email is valid or not.
  */
  public function email($value,$fieldName){
    if(filter_var($value,FILTER_VALIDATE_EMAIL) === false){
      // to send error message we are calling a Formatter class's method for display alert and error with their message.
      $msg = "Invalide $fieldName address";
      // $msg = "Invalide ".$this->customMsg[$fieldName]." address";
      return $this->errorAlertMessage($msg);
    }else {
      return $value;
    }
  }
  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Check given param is empty or not. Work for a required value
  */
  public function required($value,$fieldName){
    if(trim($value)==""){

      // Uncomment line for a use of custom lable in error message.
      // $msg = "Please enter ".$this->customMsg[$fieldName];
      $msg = "Please enter ".str_replace("_"," ",$fieldName);

      return $this->errorAlertMessage($msg);
    }else {
      return $value;
    }
  }
  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Here preg match for a contact number. You can modify as per your requirement.
  */
  public function contactNumber($value,$fieldName){
    if(!preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $value))
    {
      $msg = "Invalid ".str_replace("_"," ",$fieldName);
      // $msg = "Invalid ".$this->customMsg[$fieldName];
      return $this->errorAlertMessage($msg);
    }else {
      return $value;
    }
  }

  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : That is used to restrict a duplicate value into database.
  */
  // That check a data value must be unique at the time of insert data into database.
  public function unique($value,$fieldName,$table,$uniqueFieldName="",$id=""){
    // That condtion mostly append at the time of update data.
    $extraCondition = "";
    if(!empty($uniqueFieldName) && !empty($id)){
      $extraCondition =  " and ".$uniqueFieldName."!='".$id."'";
    }
    $query = "SELECT id from ".$table." WHERE ".$fieldName."='".$value."'".$extraCondition."";
    $result = $this->conn->query($query);
    if($result->num_rows>0){
      $msg = str_replace("_"," ",$fieldName)." is already taken";
      // $msg = $this->customMsg[$fieldName]." is already taken";
      return $this->errorAlertMessage($msg);
    }else {
      return $value;
    }
  }

  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Check a given input for the parameter is an integer or not.
  */
  public function integer($value,$fieldName){
    if(!preg_match('/^[0-9]*$/',$value))
    {
      $msg = str_replace("_"," ",$fieldName)." must be number";
      // $msg = $this->customMsg[$fieldName]." must be number";
      return $this->errorAlertMessage($msg);
    }else {
      return $value;
    }
  }

  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : Check requested parameters and expected parameters are same or not.
  */
  public function isValidRequest($requiredRequestFields,$request_data){
    $result = array_diff_key($requiredRequestFields,$request_data);
    if(sizeof($result)!=0){
      $msg = implode(" & ",array_keys($result))." parameters are missing in JSON request";
      return $this->errorAlertMessage($msg);
    }else {
      return true;
    }
  }

  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : If any success message you want to send as response that will formated here.
  */
  public function successDataFormat($request_data){
    $data['data'] = $request_data;
    return json_encode($data);
  }

  /**
  * Developer : Siddharajsinh Zala.
  * Contact: zalasid.mca@gmail.com / 834 76 76 729
  * Description : If any error message you want to send as response that will formated here.
  */
  public function errorAlertMessage($msg){
    $msg = array("error"=>array("message"=>$msg));
    return json_encode($msg);
  }
}
?>
