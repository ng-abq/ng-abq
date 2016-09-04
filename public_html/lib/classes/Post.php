<?php

namespace Com\NgAbq\Beta;
require_once("autoload.php");

/**
 * PHP representation of a post.
 *
 * Contains information about a forum post.
 *
 * @author Skyler Rexroad <skyler.rexroad@gmail.com> and Marlan Ball <wyndows@earthlink.net>
 *
 * @version 1.0.0
 **/
class Post implements \JsonSerializable {

	/* STATE VARIABLES */

	/**
	 * Primary key of post
	 * @var int $postId
	 **/
	private $postId;

	/**
	 * Post profile user name
	 * @var string $postProfileUserName
	 **/
	private $postProfileUserName;

	/**
	 * Post submission
	 * @var string $postSubmission
	 **/
	private $postSubmission;

	/**
	 * Post time
	 * @var \DateTime $postTime
	 **/
	private $postTime;

	//construct
	public function __construct(int $newPostId = null, string $newPostProfileUserName, string $newPostSubmission, string $newPostTime = null) {
		try {
			$this->setPostId($newPostId);
			$this->setPostProfileUserName($newPostProfileUserName);
			$this->setPostSubmission($newPostSubmission);
			$this->setPostTime($newPostTime);
		} catch(\InvalidArgumentException $invalidArgument) {
			//rethrow the exception to the caller
			throw(new \InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(\RangeException $range) {
			//rethrow the exception to the caller
			throw(new \RangeException($range->getMessage(), 0, $range));
		} catch(\TypeError $typeError) {
			//rethrow the exception to the caller
			throw(new \TypeError($typeError->getMessage(), 0, $typeError));
		} catch(\Exception $exception) {
			//rethrow the exception to the caller
			throw(new \Exception($exception->getMessage(), 0, $exception));
		}
	}

	// accessors and mutators
	/**
	 * accessor method for post id
	 *
	 * @return int value of post id
	 */
	public function getPostId() {
		return ($this->postId);
	}

	/**
	 * mutator method for post id
	 *
	 * @param int|null $newPostId new value of post id
	 * @throws \RangeException if $newPostId is not positive
	 * @throws \TypeError if $newPostId is not an integer
	 */
	public function setPostId(int $newPostId = null) {
		// base case: if the post id is null, this is a new post without a mySQL assigned id
		if($newPostId === null) {
			$this->postId = null;
			return;
		}

		// verify the post id is positive
		if($newPostId <= 0) {
			throw(new \RangeException("post id is not positive"));
		}

		// store the post id
		$this->postId = $newPostId;

	}

	/**
	 * accessor method for post profile username
	 *
	 * @return string value of post profile username
	 */
	public function getPostProfileUserName() {
		return ($this->postProfileUserName);
	}

	/**
	 * mutator method for post profile username
	 *
	 * @param string $newPostProfileUserName new value of post profile username
	 * @throws \InvalidArgumentException if $newPostProfileUserName is not a string or insecure
	 * @throws \RangeException if $newPostProfileUserName is > 25 characters
	 * @throws \TypeError if $newPostProfileUserName is not a string
	 */
	public function setPostProfileUserName(string $newPostProfileUserName) {
		// verify the post profile username is secure
		$newPostProfileUserName = trim($newPostProfileUserName);
		$newPostProfileUserName = filter_var($newPostProfileUserName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newPostProfileUserName) === true) {
			throw(new \InvalidArgumentException("post profile username is empty or insecure"));
		}

		// verify the post profile username will fit in the database
		if(strlen($newPostProfileUserName) > 25) {
			throw(new \RangeException("post profile username is too long"));
		}

		// store the product description
		$this->postProfileUserName = $newPostProfileUserName;
	}

	/**
	 * accessor method for post submission
	 *
	 * @return string value of post submission
	 */
	public function getPostSubmission() {
		return ($this->postSubmission);
	}

	/**
	 * mutator method for post submission
	 *
	 * @param string $newPostSubmission new value of post submission
	 * @throws \InvalidArgumentException if $newPostSubmission is not a string or insecure
	 * @throws \RangeException if $newPostSubmission is > 25 characters
	 * @throws \TypeError if $newPostSubmission is not a string
	 */
	public function setPostSubmission(string $newPostSubmission) {
		// verify the post submission is secure
		$newPostSubmission = trim($newPostSubmission);
		$newPostSubmission = filter_var($newPostSubmission, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newPostSubmission) === true) {
			throw(new \InvalidArgumentException("post submission is empty or insecure"));
		}

		// verify the post submission will fit in the database
		if(strlen($newPostSubmission) > 150) {
			throw(new \RangeException("post submission is too long"));
		}

		// store the product description
		$this->postSubmission = $newPostSubmission;
	}

	/**
	 * accessor method for post time
	 *
	 * @return \DateTime value of post time
	 **/
	public function getPostTime() {
		return ($this->postTime);
	}

	/**
	 * mutator method for post time
	 *
	 * @param \DateTime|string|null $newPostTime purchase time as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newPostTime is not a valid object or string
	 * @throws \RangeException if $newPostTime is  a time that does not exist
	 **/
	public function setPostTime($newPostTime = null) {
		// base case: if the time is null, use the current date and time
		if($newPostTime === null) {
			$this->postTime = new \DateTime();
			return;
		}

		// store the post time
		try {
			$newPostTime = $this->validateDate($newPostTime);
		} catch(\InvalidArgumentException $invalidArgument) {
			throw(new \InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(\RangeException $range) {
			throw(new \RangeException($range->getMessage(), 0, $range));
		}
		$this->postTime = $newPostTime;
	}


	//TODO
	public function jsonSerialize() {
		// TODO
	}
}
