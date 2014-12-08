<?php

namespace Maclay\ServiceBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * ClubBatchRecord
 */
class ClubBatchRecord
{
    public function __construct()
    {
        $this->studentHours = new ArrayCollection();
    }
    
    /**
     * @var \DateTime 
     */
    private $dateFrom;
    
    /**
     * @var \DateTime 
     */
    private $dateTo;
    
    /**
     * @var string 
     */
    private $organization;
    
    /**
     * @var string 
     */
    private $supervisor;
    
    /**
     * @var string 
     */
    private $activity;
    
    /**
     * @var string 
     */
    private $notes;
    
    /**
     * @var array 
     */
    private $studentHours;
    
    /**
     * Get dateFrom
     * 
     * @return \DateTime
     */
    public function getDateFrom(){
        return $this->dateFrom;
    }
    
    /**
     * Set dateFrom
     * 
     * @param \DateFrom $date
     * @return ClubBatchRecord
     */
    public function setDateFrom($date){
        $this->dateFrom = $date;
        
        return $this;
    }
    
    /**
     * Get dateTo
     * 
     * @return \DateTime
     */
    public function getDateTo(){
        return $this->dateTo;
    }
    
    /**
     * Set dateTo
     * 
     * @param \DateFrom $date
     * @return ClubBatchRecord
     */
    public function setDateTo($date){
        $this->dateTo = $date;
        
        return $this;
    }
    
    /**
     * Get organization
     * 
     * @return string
     */
    public function getOrganization(){
        return $this->organization;
    }
    
    /**
     * Set organization
     * 
     * @param string $organization
     * @return ClubBatchRecord
     */
    public function setOrganization($organization){
        $this->organization = $organization;
        
        return $this;
    }
    
    /**
     * Get supervisor
     * 
     * @return string
     */
    public function getSupervisor(){
        return $this->supervisor;
    }
    
    /**
     * Set supervisor
     * 
     * @param string $supervisor
     * @return ClubBatchRecord
     */
    public function setSupervisor($supervisor){
        $this->supervisor = $supervisor;
        
        return $this;
    }
    
    /**
     * Get activity
     * 
     * @return string
     */
    public function getActivity(){
        return $this->activity;
    }
    
    /**
     * Set activity
     * 
     * @param string $activity
     * @return ClubBatchRecord
     */
    public function setActivity($activity){
        $this->activity = $activity;
        
        return $this;
    }
    
    /**
     * Get notes
     * 
     * @return string
     */
    public function getNotes(){
        return $this->notes;
    }
    
    /**
     * Set notes
     * 
     * @param string $notes
     * @return ClubBatchRecord
     */
    public function setNotes($notes){
        $this->notes = $notes;
        
        return $this;
    }
    
    /**
     * Add studentHours
     * 
     * @param StudentHours $model
     * @return ClubBatchRecord
     */
    public function addStudentHours(\Maclay\ServiceBundle\Model\StudentHours $model){
        $this->studentHours[] = $model;
        
        return $this;
    }
    
    /**
     * Remove studentHours
     * 
     * @param StudentHours $model
     */
    public function removeStudentHours(\Maclay\ServiceBundle\Model\StudentHours $model){
        $this->studentHours->removeElement($model);
    }
    
    /**
     * Get studentHours
     * 
     * @return array
     */
    public function getStudentHours(){
        return $this->studentHours;
    }
}
