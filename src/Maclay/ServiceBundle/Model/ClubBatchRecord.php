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
    
    private $dateFrom;
    private $dateTo;
    private $organization;
    private $supervisor;
    private $activity;
    private $notes;
    private $studentHours;
    
    public function getDateFrom(){
        return $this->dateFrom;
    }
    
    public function setDateFrom($date){
        $this->dateFrom = $date;
        
        return $this;
    }
    
    public function getDateTo(){
        return $this->dateTo;
    }
    
    public function setDateTo($date){
        $this->dateTo = $date;
        
        return $this;
    }
    
    public function getOrganization(){
        return $this->organization;
    }
    
    public function setOrganization($organization){
        $this->organization = $organization;
        
        return $this;
    }
    
    public function getSupervisor(){
        return $this->supervisor;
    }
    
    public function setSupervisor($supervisor){
        $this->supervisor = $supervisor;
        
        return $this;
    }
    
    public function getActivity(){
        return $this->activity;
    }
    
    public function setActivity($activity){
        $this->activity = $activity;
        
        return $this;
    }
    
    public function getNotes(){
        return $this->notes;
    }
    
    public function setNotes($notes){
        $this->notes = $notes;
        
        return $this;
    }
    
    public function addStudentHours(\Maclay\ServiceBundle\Model\StudentHours $model){
        $this->studentHours[] = $model;
        
        return $this;
    }
    
    public function removeStudentHours(\Maclay\ServiceBundle\Model\StudentHours $model){
        $this->studentHours->removeElement($model);
    }
    
    public function getStudentHours(){
        return $this->studentHours;
    }
}
