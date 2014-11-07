<?php

namespace Maclay\ServiceBundle\Model;

/**
 * StudentHours
 */
class StudentHours
{
    private $lastName;
    private $firstName;
    private $studentNumber;
    private $numHours;
    
    public function getLastName(){
        return $this->lastName;
    }
    
    public function setLastName($lastName){
        $this->lastName = $lastName;
        
        return $this;
    }
    
    public function getFirstName(){
        return $this->firstName;
    }
    
    public function setFirstName($firstName){
        $this->firstName = $firstName;
        
        return $this;
    }
    
    public function getStudentNumber(){
        return $this->studentNumber;
    }
    
    public function setStudentNumber($studentNumber){
        $this->studentNumber = $studentNumber;
        
        return $this;
    }
    
    public function getNumHours(){
        return $this->numHours;
    }
    
    public function setNumHours($numHours){
        $this->numHours = $numHours;
        
        return $this;
    }
}
