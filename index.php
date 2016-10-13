<?php
include "validation.php";

class Testing extends Validation
{
  function __construct()
  {
      Parent::__construct();
  }
  public function postRequest($request_data)
  {
    // Here is validation for request parameters are missing or not.
    // Convert json request to array by using json_decode function with true option.
    $request_data = json_decode($request_data,true);
    // Create on array with blank value which are must you want in request parameters.
    $requiredRequestFields = array("first_name"=>"","last_name"=>"","age"=>"","email"=>"","contact"=>"","gender"=>"");
    // Called a method of Validation Class. That will check a passed json request have all parameters are available or missing any one or more.
    $isValid = $this->isValidRequest($requiredRequestFields,$request_data);
    // Check is any parameters is missing. If yes then go to else other wise move ahead.
    if($isValid=="1"){ // Incomming request have all parameters
      //  Now here is rules of validations.

      /*
      * unique value - e.g : If you want to check a email address is already exist in db.
      * Then you can use this rule - unique:your_table_name
      * e.g : "email":"unique:user_detail"
      * That will check a passed email address is already exist in dabase or not.
      */
      $rules = array(
        "first_name"=>"required",
        "last_name"=>"required",
        "age"=>"required|integer",
        "email"=>"email|unique:user_details",
        "contact"=>"required|contactNumber",
        "gender"=>"required"
      );
      $isValid = $this->checkValidation($request_data,$rules);
      if($isValid=="1"){
        echo "Your request array perfect.<br><pre>";
        print_r($request_data);
      }else { //
        echo $isValid;
      }
    }else { // Return error message
      echo $isValid;
    }
  }
}
$objTest = new Testing;

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"24",
"email":"xyz@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';

/* Testing request

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"",
"email":"xyz@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"Twenty Five",
"email":"xyz@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"25",
"email":"zalasid.mca@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';

$request_data = '{
"age":"25",
"email":"xyz.mca@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"24",
"email":"xyz@gmail.com",
"contact":"9876543210",
"gender":"male"
}';

$request_data = '{
"first_name":"Sid",
"last_name":"Jhala",
"age":"24",
"email":"x@y,z@gmail.com",
"contact":"987-654-3210",
"gender":"male"
}';
*/


// Paste testing request_data variable here. :D

$objTest->postRequest($request_data);

?>
