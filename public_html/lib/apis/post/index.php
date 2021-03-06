<?php

/**
 * Post API
 *
 * @author Skyler Rexroad <skyler.rexroad@gmail.com>
 *
 * @version 1.0.0
 **/

use Com\NgAbq\Beta;

require_once dirname(__DIR__, 2) . "/classes/autoload.php";
require_once dirname(__DIR__, 3) . "/lib/xsrf.php";
require_once("/etc/apache2/encrypted-config/encrypted-config.php");

// Verify the session and start it if it's not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	$pdo = connectToEncryptedMySQL("/etc/apache2/encrypted-config/ng-abq-dev.ini");

	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

	if (($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("", 405));
	}

	if ($method === "GET") {
		setXsrfCookie();

		if (empty($id) === false) {
			$post = Beta\Post::getPostByPostId($pdo, $id);
			if ($post !== null) {
				$reply->data = $post;
			}
		} else {
			$posts = Beta\Post::getAllPosts($pdo)->toArray();
			if (sizeof($posts) > 0) {
				$reply->data = $posts;
			}
		}
	} else if ($method === "PUT" || $method === "POST") {
		verifyXsrf();

		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		if (empty($requestObject->postProfileUserName)) {
			throw new \InvalidArgumentException("Post profile username must exist.", 405);
		}
		if (empty($requestObject->postSubmission)) {
			throw new \InvalidArgumentException("Post content must exist.", 405);
		}
//		if (empty($requestObject->postTime)) {
//			throw new \InvalidArgumentException("Post timestamp must exist.", 405);
//		}

		// BEGIN PUT AND POST
		if ($method === "PUT") {
			$post = Beta\Post::getPostByPostId($pdo, $id);
			if ($post === null) {
				throw new \RuntimeException("Post does not exist.", 404);
			}

			$post->setPostProfileUserName($requestObject->postProfileUserName);
			$post->setPostSubmission($requestObject->postSubmission);
//			$post->setPostTime($requestObject->postTime);

			$post->update($pdo);

			$reply->message = "Post updated successfully.";
		} else if ($method === "POST") {
			$post = new Beta\Post(null, $requestObject->postProfileUserName, $requestObject->postSubmission, $requestObject->postTime);
			$post->insert($pdo);

			$reply->message = "Post successfully posted.";
		}
	} else if ($method === "DELETE") {
		verifyXsrf();

		$post = Beta\Post::getPostByPostId($pdo, $id);
		if($post === null) {
			throw(new RuntimeException("Post does not exist.", 404));
		}

		$post->delete($pdo);

		$reply->message = "Post successfully deleted.";
	} else {
		throw new \RuntimeException("Method not allowed.");
	}
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

echo json_encode($reply);
