<?php

namespace Com\NgAbq\Beta;
require_once("autoload.php");

/**
 * Attendee class  
 * @author Eliot Ostling <it.treugott@gmail.com>
 * @version 1.0.0BETA
 **/

class Attendee implements \JsonSerializable
{
    /*
     * Primary key
     */
    private $attendeeId;

    /*
     * 1 Foreign Keys
     */
    private $attendeeEventId;

    /*
     * 2 Foreign Keys
     */
    private $attendeeProfileId;



    public function __construct(int $newAttendeeId = null, int $newAttendeeEventId, int $newAttendeeProfileId )
    {
        try {
            $this->setAttendeeId($newAttendeeId);
            $this->setAttendeeEventId($newAttendeeEventId);
            $this->setAttendeeProfileId($newAttendeeProfileId);

        } catch(\InvalidArgumentException $invalidArgument) {
            throw(new \InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
        } catch(\RangeException $range) {
            throw(new \RangeException($range->getMessage(), 0, $range));
        } catch(\TypeError $typeError) {
            throw(new \TypeError($typeError->getMessage(), 0, $typeError));
        } catch(\Exception $exception) {
            throw(new \Exception($exception->getMessage(), 0, $exception));
        }
    }
    //Accessor method
    public function getAttendeeId()
    {
        return ($this->attendeeId);
    }

    /**
     * mutator method
     *
     **/
    public function setAttendeeId($newAttendeeId = null)
    {
        if($newAttendeeId === null) {
            $this->attendeeId = null;
            return;
        }

        if($newAttendeeId <= 0) {
            throw(new \RangeException("Ok"));
        }
        $this->attendeeId = $newAttendeeId;
    }

    //Accessor Method
    public function getAttendeeEventId()
    {
        return ($this->attendeeEventId);
    }

    /**
     * mutator method
     *
     **/
    public function setAttendeeEventId($newAttendeeEventId = null)
    {
        if($newAttendeeEventId === null) {
            $this->attendeeEventId = null;
            return;
        }

        if($newAttendeeEventId <= 0) {
            throw(new \RangeException("Ok"));
        }
        $this->attendeeEventId = $newAttendeeEventId;
    }

    //Accessor
    public function getAttendeeProfileId()
    {
        return ($this->attendeeProfileId);
    }

    /**
     * mutator method
     *
     **/
    public function setAttendeeProfileId($newAttendeeProfileId = null)
    {
        if($newAttendeeProfileId === null) {
            $this->attendeeProfileId = null;
            return;
        }

        if($newAttendeeProfileId <= 0) {
            throw(new \RangeException("Ok"));
        }
        $this->AttendeeProfileId = $newAttendeeProfileId;
    }

    //Insert into the DB
    public function insert(\PDO $pdo)
    {

        if($this->attendeeId !== null) {
            throw(new \PDOException("Attendee exists"));
        }
        $query = "INSERT INTO Attendee(attendeeId, attendeeEventId, attendeeProfileId) VALUES(:attendeeId, :attendeeEventId, :attendeeProfileId)";
        $statement = $pdo->prepare($query);
        $parameters = ["attendeeId" => $this->attendeeId, "attendeeEventId" => $this->attendeeEventId, "attendeeProfileId" => $this->attendeeProfileId];
        $statement->execute($parameters);
        $this->attendeeId = intval($pdo->lastInsertId());
    }

    //Delete the DB
    public function delete(\PDO $pdo)
    {
        if($this->attendeeId === null) {
            throw(new \PDOException(""));
        }
        $query = "DELETE FROM Attendee WHERE attendeeId = :attendeeId";
        $statement = $pdo->prepare($query);
        $parameters = ["attendeeId" => $this->attendeeId];
        $statement->execute($parameters);
    }


    public static function getAttendeetByAttendeeEventId(\PDO $pdo, $attendeeEventId)
    {
        if($attendeeEventId <= 0) {
            throw(new \PDOException(""));
        }
        $query = "SELECT attendeeId, attendeeEventId, attendeeProfileId  FROM Attendee WHERE attendeeEventId = :attendeeEventId";
        $statement = $pdo->prepare($query);
        $parameters = array("attendeeEventId" => $attendeeEventId);
        $statement->execute($parameters);
        try {
            $attendeeEventId = null;
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            $row = $statement->fetch();
            if($row !== false) {
                $attendeeEventId = new Attendee($row["attendeeId"], $row["attendeeEventId"], $row["attendeeProfileId"]);
            }
        } catch(\Exception $exception) {
            throw(new \PDOException($exception->getMessage(), 0, $exception));
        }
        return ($attendeeEventId);

    }

























































    public function jsonSerialize() {
        $fields = get_object_vars($this);
        return ($fields);
    }




}