## PHP Rest API Custom validation rules Helper Class.

# Import database
 Import a dummy database. Use mydb.sql file for that.
 
# How to use
 I have create one class file that is validation.php
 You can use that class for a validate your request parameters.
 Simply put a validate rule array and request array into method and that will check your validations for your request.
 
```
// convert to array
$request_data = json_decode($json_request,true);

// create a rule array
$rules = array(
        "first_name"=>"required",
        "last_name"=>"required",
        "age"=>"required|integer",
        "email"=>"email|unique:user_details",
        "contact"=>"required|contactNumber",
        "gender"=>"required"
);
// check a validation using a validation class method.
$isValid = $this->checkValidation($request_data,$rules);
```
