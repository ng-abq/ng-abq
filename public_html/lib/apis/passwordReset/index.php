<?php
use Com\NgAbq\Beta;

require_once dirname(__DIR__, 2) . "/classes/autoload.php";
require_once dirname(__DIR__, 2) . "/lib/xsrf.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


/**
 * api for the PasswordReset class
 *
 * @author Marlan Ball, parts of this code have been modified from code by Derek Mauldin <derek.e.mauldin from @see https://bootcamp-coders.cnm.edu/class-materials/php/writing-restful-apis/
 **/

// verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	// grab the mySQL connection
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/cartridge.ini");

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

	//make sure the id is valid for methods that require it
	if(($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("id cannot be empty or negative", 405));
	}


	// handle GET request - all passwordResets are returned.
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();

		//get all passwordResets and update reply
		$passwordResets = ng-abq\PasswordReset::getAllPasswordResets($pdo);
		if($passwordResets !== null) {
			$reply->data = $passwordResets;
		}
	}
	else if($method === "POST") {

		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//make sure passwordReset content is available
		if(empty($requestObject->passwordResetUrl) === true) {
			throw(new \InvalidArgumentException ("no content for passwordReset.", 405));
		}

		//perform the actual post
		if($method === "POST") {

			// create new passwordReset and insert into the database
			$passwordReset = new ng-abq\PasswordReset(null, $requestObject->passwordResetProfileId, $requestObject->passwordResetProfileUserName, $requestObject->passwordResetUrl, $requestObject->passwordResetDate);
			$passwordReset->insert($pdo);

			// update reply
			$reply->message = "PasswordReset created ok";
		}
	} else if($method === "DELETE") {
		verifyXsrf();

		// retrieve the PasswordReset to be deleted
		$passwordReset = ng-abq\PasswordReset::getPasswordResetByPasswordResetId($pdo, $id);
		if($passwordReset === null) {
			throw(new RuntimeException("PasswordReset does not exist", 404));
		}

		// delete passwordReset
		$passwordReset->delete($pdo);

		// update reply
		$reply->message = "PasswordReset deleted OK";
	} else {
		throw (new InvalidArgumentException("Invalid HTTP method request"));
	}
}

	// update reply with exception information
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
	$reply->trace = $exception->getTraceAsString();
} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}

header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

// encode and return reply to front end caller
echo json_encode($reply);