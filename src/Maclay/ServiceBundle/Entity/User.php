<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 */
class User implements UserInterface, \Serializable
{
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->clubs = new ArrayCollection();
    }
    
    protected $roles;
    protected $studentinfo;
    protected $parents;
    protected $children;
    protected $clubowner;
    protected $clubs;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
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
    
    public function getSalt(){
        return null;
    }
    
    public function eraseCredentials() {
        
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }
    
    public function unserialize($serialized){
        list (
                $this->id,
                $this->username,
                $this->password,
        ) = unserialize($serialized);
    }
    
    public function getRoles(){
        return $this->roles;
    }
}
