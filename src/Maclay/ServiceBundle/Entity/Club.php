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
        $this->sponsors = new ArrayCollection();
        $this->memberRecords = new ArrayCollection();
    }
    
    /**
     * @var array
     */
    protected $sponsors;
    
    /**
     * @var array
     */
    protected $members;
    
    /**
     * @var array
     */
    protected $memberRecords;
    
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
     * Add sponsor
     *
     * @param \Maclay\ServiceBundle\Entity\User $sponsors
     * @return Club
     */
    public function addSponsor(\Maclay\ServiceBundle\Entity\User $sponsors)
    {
        $this->sponsors[] = $sponsors;

        return $this;
    }

    /**
     * Remove sponsors
     *
     * @param \Maclay\ServiceBundle\Entity\User $sponsors
     */
    public function removeSponsor(\Maclay\ServiceBundle\Entity\User $sponsors)
    {
        $this->sponsors->removeElement($sponsors);
    }

    /**
     * Get sponsors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSponsors()
    {
        return $this->sponsors;
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

    /**
     * Add memberRecords
     *
     * @param \Maclay\ServiceBundle\Entity\Record $memberRecords
     * @return Club
     */
    public function addMemberRecord(\Maclay\ServiceBundle\Entity\Record $memberRecords)
    {
        $this->memberRecords[] = $memberRecords;

        return $this;
    }

    /**
     * Remove memberRecords
     *
     * @param \Maclay\ServiceBundle\Entity\Record $memberRecords
     */
    public function removeMemberRecord(\Maclay\ServiceBundle\Entity\Record $memberRecords)
    {
        $this->memberRecords->removeElement($memberRecords);
    }

    /**
     * Get memberRecords
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberRecords()
    {
        return $this->memberRecords;
    }
}
