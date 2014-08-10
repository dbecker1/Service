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
    }
    
    protected $studentinfo;
    protected $parents;
    protected $children;
    protected $clubowner;
    protected $clubs;
    protected $records;
    protected $approvedHours;
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
    
}
