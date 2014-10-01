<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->clubs = new ArrayCollection();
        $this->records = new ArrayCollection();
        $this->sponsorForClubs = new ArrayCollection();
    }
    
    public function __toString(){
        return $this->firstName . " " . $this->lastName;
    }
    
    protected $studentinfo;
    protected $parents;
    protected $children;
    protected $clubowner;
    protected $clubs;
    protected $records;
    protected $approvedHours;
    protected $sponsorForClubs;
    protected $isInvited;
    protected $forgotPasswordCode;
    /**
     * @var integer
     */
    protected $id;

    

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $middleName;
    
    private $tempPass;


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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string 
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set studentinfo
     *
     * @param \Maclay\ServiceBundle\Entity\StudentInfo $studentinfo
     * @return User
     */
    public function setStudentinfo(\Maclay\ServiceBundle\Entity\StudentInfo $studentinfo = null)
    {
        $this->studentinfo = $studentinfo;

        return $this;
    }

    /**
     * Get studentinfo
     *
     * @return \Maclay\ServiceBundle\Entity\StudentInfo 
     */
    public function getStudentinfo()
    {
        return $this->studentinfo;
    }

    /**
     * Add clubs
     *
     * @param \Maclay\ServiceBundle\Entity\Club $clubs
     * @return User
     */
    public function addClub(\Maclay\ServiceBundle\Entity\Club $clubs)
    {
        $this->clubs[] = $clubs;

        return $this;
    }

    /**
     * Remove clubs
     *
     * @param \Maclay\ServiceBundle\Entity\Club $clubs
     */
    public function removeClub(\Maclay\ServiceBundle\Entity\Club $clubs)
    {
        $this->clubs->removeElement($clubs);
    }

    /**
     * Get clubs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClubs()
    {
        return $this->clubs;
    }
    
    public function getRecords()
    {
        return $this->records;
    }
    
    public function getApprovedHours(){
        return $this->approvedHours;
    }
    
    public function setApprovedHours($approvedHours){
        $this->approvedHours = $approvedHours;
        
        return $approvedHours;
    }
    
    public function getTempPass()
    {
        return $this->tempPass;
    }
    
    public function setTempPass($tempPass)
    {
        $this->tempPass = $tempPass;
        
        return $tempPass;
    }

    /**
     * Add records
     *
     * @param \Maclay\ServiceBundle\Entity\Record $records
     * @return User
     */
    public function addRecord(\Maclay\ServiceBundle\Entity\Record $records)
    {
        $this->records[] = $records;

        return $this;
    }

    /**
     * Remove records
     *
     * @param \Maclay\ServiceBundle\Entity\Record $records
     */
    public function removeRecord(\Maclay\ServiceBundle\Entity\Record $records)
    {
        $this->records->removeElement($records);
    }

    /**
     * Add sponsorForClubs
     *
     * @param \Maclay\ServiceBundle\Entity\Club $sponsorForClubs
     * @return User
     */
    public function addSponsorForClub(\Maclay\ServiceBundle\Entity\Club $sponsorForClubs)
    {
        $this->sponsorForClubs[] = $sponsorForClubs;

        return $this;
    }

    /**
     * Remove sponsorForClubs
     *
     * @param \Maclay\ServiceBundle\Entity\Club $sponsorForClubs
     */
    public function removeSponsorForClub(\Maclay\ServiceBundle\Entity\Club $sponsorForClubs)
    {
        $this->sponsorForClubs->removeElement($sponsorForClubs);
    }

    /**
     * Get sponsorForClubs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSponsorForClubs()
    {
        return $this->sponsorForClubs;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */


    /**
     * Set isInvited
     *
     * @param boolean $isInvited
     * @return User
     */
    public function setIsInvited($isInvited)
    {
        $this->isInvited = $isInvited;

        return $this;
    }

    /**
     * Get isInvited
     *
     * @return boolean 
     */
    public function getIsInvited()
    {
        return $this->isInvited;
    }

   

    /**
     * Add parents
     *
     * @param \Maclay\ServiceBundle\Entity\User $parents
     * @return User
     */
    public function addParent(\Maclay\ServiceBundle\Entity\User $parents)
    {
        $this->parents[] = $parents;

        return $this;
    }

    /**
     * Remove parents
     *
     * @param \Maclay\ServiceBundle\Entity\User $parents
     */
    public function removeParent(\Maclay\ServiceBundle\Entity\User $parents)
    {
        $this->parents->removeElement($parents);
    }

    /**
     * Get parents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Add children
     *
     * @param \Maclay\ServiceBundle\Entity\User $children
     * @return User
     */
    public function addChild(\Maclay\ServiceBundle\Entity\User $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Maclay\ServiceBundle\Entity\User $children
     */
    public function removeChild(\Maclay\ServiceBundle\Entity\User $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set forgotPasswordCode
     *
     * @param string $forgotPasswordCode
     * @return User
     */
    public function setForgotPasswordCode($forgotPasswordCode)
    {
        $this->forgotPasswordCode = $forgotPasswordCode;

        return $this;
    }

    /**
     * Get forgotPasswordCode
     *
     * @return string 
     */
    public function getForgotPasswordCode()
    {
        return $this->forgotPasswordCode;
    }
}
