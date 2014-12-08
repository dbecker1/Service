<?php

namespace Maclay\ServiceBundle\Model;

/**
 * StudentHours
 */
class StudentHours
{
    /**
     * @var string 
     */
    private $lastName;
    
    /**
     * @var string 
     */
    private $firstName;
    
    /**
     * @var int 
     */
    private $studentNumber;
    
    /**
     * @var int 
     */
    private $numHours;
    
    /**
     * Get lastName
     * 
     * @return string
     */
    public function getLastName(){
        return $this->lastName;
    }
    
    /**
     * Set lastName
     * 
     * @param string $lastName
     * @return StudentHours
     */
    public function setLastName($lastName){
        $this->lastName = $lastName;
        
        return $this;
    }
    
    /**
     * Get firstName
     * 
     * @return string
     */
    public function getFirstName(){
        return $this->firstName;
    }
    
    /**
     * Set firstName
     * 
     * @param string $firstName
     * @return StudentHours
     */
    public function setFirstName($firstName){
        $this->firstName = $firstName;
        
        return $this;
    }
    
    /**
     * Get studentNumber
     * 
     * @return int
     */
    public function getStudentNumber(){
        return $this->studentNumber;
    }
    
    /**
     * Set studentNumber
     * 
     * @param number $studentNumber
     * @return StudentHours
     */
    public function setStudentNumber($studentNumber){
        $this->studentNumber = $studentNumber;
        
        return $this;
    }
    
    /**
     * Get numHours
     * 
     * @return int
     */
    public function getNumHours(){
        return $this->numHours;
    }
    
    /**
     * Set numHours
     * 
     * @param int $numHours
     * @return StudentHours
     */
    public function setNumHours($numHours){
        $this->numHours = $numHours;
        
        return $this;
    }
}
