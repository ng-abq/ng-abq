<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
	/**
	 * fields modifiable en masse
	 */
	protected $fillable = ["event_name", "event_description"];

	/**
	 * get the users attending this event
	 */
	public function attendees() {
		return($this->belongsToMany("App\\User", "attendees"));
	}

	/**
	 * get the user that owns this event
	 */
	public function user() {
		return($this->belongsTo("App\\User"));
	}
}