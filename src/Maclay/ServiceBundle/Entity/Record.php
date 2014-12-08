<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Record
 */
class Record
{
    /**
     * @var User
     */
    protected $student;
    
    /**
     * @var file
     */
    public $attachment;
    
    /**
     * @var string
     */
    private $attachmentFileName;
    
    /**
     * @var int $enteredByClub The club that entered the record..
     */
    private $enteredByClub;
    
    /**
     * @var boolean
     */
    private $emailIsSent;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $currentGrade;

    /**
     * @var \DateTime
     */
    private $dateFrom;

    /**
     * @var \DateTime
     */
    private $dateTo;

    /**
     * @var integer
     */
    private $numHours;

    /**
     * @var string
     */
    private $activity;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     */
    private $organization;

    /**
     * @var string
     */
    private $supervisor;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     */
    private $approvalDate;

    /**
     * @var string
     */
    private $approverComments;

    /**
     * @var integer
     */
    private $approvalStatus;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set currentGrade
     *
     * @param integer $currentGrade
     * @return Record
     */
    public function setCurrentGrade($currentGrade)
    {
        $this->currentGrade = $currentGrade;

        return $this;
    }

    /**
     * Get currentGrade
     *
     * @return integer 
     */
    public function getCurrentGrade()
    {
        return $this->currentGrade;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return Record
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     * @return Record
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime 
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set numHours
     *
     * @param integer $numHours
     * @return Record
     */
    public function setNumHours($numHours)
    {
        $this->numHours = $numHours;

        return $this;
    }

    /**
     * Get numHours
     *
     * @return integer 
     */
    public function getNumHours()
    {
        return $this->numHours;
    }

    /**
     * Set activity
     *
     * @param string $activity
     * @return Record
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return string 
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Record
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set organization
     *
     * @param string $organization
     * @return Record
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set supervisor
     *
     * @param string $supervisor
     * @return Record
     */
    public function setSupervisor($supervisor)
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get supervisor
     *
     * @return string 
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Record
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set approvalDate
     *
     * @param \DateTime $approvalDate
     * @return Record
     */
    public function setApprovalDate($approvalDate)
    {
        $this->approvalDate = $approvalDate;

        return $this;
    }

    /**
     * Get approvalDate
     *
     * @return \DateTime 
     */
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * Set approverComments
     *
     * @param string $approverComments
     * @return Record
     */
    public function setApproverComments($approverComments)
    {
        $this->approverComments = $approverComments;

        return $this;
    }

    /**
     * Get approverComments
     *
     * @return string 
     */
    public function getApproverComments()
    {
        return $this->approverComments;
    }

    /**
     * Set approvalStatus
     *
     * @param boolean $approvalStatus
     * @return Record
     */
    public function setApprovalStatus($approvalStatus)
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    /**
     * Get approvalStatus
     *
     * @return boolean 
     */
    public function getApprovalStatus()
    {
        return $this->approvalStatus;
    }
    
    public function setStudent($student)
    {
        $this->student = $student;
        
        return $this;
    }
    
    /**
     * Get student
     *
     * @return User 
     */
    public function getStudent()
    {
        return $this->student;
    }
    
    /**
     * Set attachmentFileName
     *
     * @param string $attachmentFileName
     * @return Record
     */
    public function setAttachmentFileName($attachmentFileName)
    {
        $this->attachmentFileName = $attachmentFileName;
        
        return $this;
    }
    
    /**
     * Get attachementFileName
     *
     * @return string 
     */
    public function getAttachmentFileName()
    {
        return $this->attachmentFileName;
    }
    
    public function setEnteredByClub($club)
    {
        $this->enteredByClub = $club;
        
        return $this;
    }
    
    /**
     * Get enteredByClub
     *
     * @return Club 
     */
    public function getEnteredByClub()
    {
        return $this->enteredByClub;
    }
    
    /**
     * Set emailIsSent
     *
     * @param string $emailIsSent
     * @return Record
     */
    public function setEmailIsSent($emailIsSent){
        $this->emailIsSent = $emailIsSent;
        
        return $this;
    }
    
    /**
     * Get emailIsSent
     *
     * @return boolean 
     */
    public function getEmailIsSent(){
        return $this->emailIsSent;
    }
}
