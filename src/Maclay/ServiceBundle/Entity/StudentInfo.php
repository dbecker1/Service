<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StudentInfo
 */
class StudentInfo
{
    /**
     * @var User
     */
    protected $student;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $studentNumber;

    /**
     * @var integer
     */
    private $gradYear;

    /**
     * @var integer
     */
    private $grade;

    /**
     * @var string
     */
    private $gender;


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
     * Set studentNumber
     *
     * @param integer $studentNumber
     * @return StudentInfo
     */
    public function setStudentNumber($studentNumber)
    {
        $this->studentNumber = $studentNumber;

        return $this;
    }

    /**
     * Get studentNumber
     *
     * @return integer 
     */
    public function getStudentNumber()
    {
        return $this->studentNumber;
    }

    /**
     * Set gradYear
     *
     * @param integer $gradYear
     * @return StudentInfo
     */
    public function setGradYear($gradYear)
    {
        $this->gradYear = $gradYear;

        return $this;
    }

    /**
     * Get gradYear
     *
     * @return integer 
     */
    public function getGradYear()
    {
        return $this->gradYear;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     * @return StudentInfo
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return integer 
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return StudentInfo
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set student
     *
     * @param \Maclay\ServiceBundle\Entity\User $student
     * @return StudentInfo
     */
    public function setStudent(\Maclay\ServiceBundle\Entity\User $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \Maclay\ServiceBundle\Entity\User 
     */
    public function getStudent()
    {
        return $this->student;
    }
}
