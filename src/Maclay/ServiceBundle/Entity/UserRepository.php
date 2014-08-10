<?php

namespace Maclay\ServiceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    //incredibly inefficient way to do this... need to find a better way
    public function findByRole($role){
        $query = $this->getEntityManager()
                ->createQuery(
                        "SELECT u "
                        . "FROM MaclayServiceBundle:User u "
                  )
                ->getResult();
        
        foreach($query as $key => $user){
            $isRole = false;
            foreach($user->getRoles() as $r){
                if (strpos($r, $role) !== false){
                    $isRole = true;
                }
            }
            if ($isRole === false){
                unset($query[$key]);
            }
        }
        return $query;
    }
}
