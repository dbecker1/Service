<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Club
 */
class Club
{
    public function __construct()
    {
        $this->members = new ArrayCollection();
    }
    
    protected $clubsponsor;
    protected $members;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $clubName;


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
     * Set clubName
     *
     * @param string $clubName
     * @return Club
     */
    public function setClubName($clubName)
    {
        $this->clubName = $clubName;

        return $this;
    }

    /**
     * Get clubName
     *
     * @return string 
     */
    public function getClubName()
    {
        return $this->clubName;
    }

    /**
     * Set clubsponsor
     *
     * @param \Maclay\ServiceBundle\Entity\User $clubsponsor
     * @return Club
     */
    public function setClubsponsor(\Maclay\ServiceBundle\Entity\User $clubsponsor = null)
    {
        $this->clubsponsor = $clubsponsor;

        return $this;
    }

    /**
     * Get clubsponsor
     *
     * @return \Maclay\ServiceBundle\Entity\User 
     */
    public function getClubsponsor()
    {
        return $this->clubsponsor;
    }

    /**
     * Add members
     *
     * @param \Maclay\ServiceBundle\Entity\User $members
     * @return Club
     */
    public function addMember(\Maclay\ServiceBundle\Entity\User $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \Maclay\ServiceBundle\Entity\User $members
     */
    public function removeMember(\Maclay\ServiceBundle\Entity\User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }
}
