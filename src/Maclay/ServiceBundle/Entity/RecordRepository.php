<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RecordRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RecordRepository extends EntityRepository
{
    public function getRecentRecords($length, $userId){
        return $this->getEntityManager()
                ->createQuery(
                        "SELECT r, u "
                        . "FROM MaclayServiceBundle:Record r "
                        . "JOIN r.student u "
                        . "WHERE u.id = :id "
                        . "ORDER BY r.dateCreated DESC"
                  )
                ->setMaxResults($length)
                ->setParameter("id", $userId)
                ->getResult();
    }
    
    public function getPendingRecords(){
        return $this->getEntityManager()
                ->createQuery(
                        "SELECT r, u "
                        . "FROM MaclayServiceBundle:Record r "
                        . "JOIN r.student u "
                        . "WHERE r.approvalStatus = 0"
                )
                ->getResult();
    }
    
    public function getRecordByGrade($student, $grade){
        return $this->getEntityManager()
                ->createQuery(
                        "SELECT r "
                        . "FROM MaclayServiceBundle:Record r "
                        . "WHERE r.student = :student AND r.currentGrade = :currentGrade"
                        )
                ->setParameter("student", $student)
                ->setParameter("currentGrade", $grade)
                ->getResult();
    }
    
    public function getRecordAndStudentById($id){
        return $this->getEntityManager()
                ->createQuery(
                        "SELECT r, u, s "
                        . "FROM MaclayServiceBundle:Record r "
                        . "JOIN r.student u "
                        . "JOIN u.studentinfo s "
                        . "WHERE r.id = :id"
                        )
                ->setParameter("id", $id)
                ->getResult();
    }
}
