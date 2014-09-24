<?php

namespace Waldo\OpenIdConnect\ProviderBundle\EntityRepository;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function findByEmailOrUsername($usernameOrEmail)
    {
        $qb = $this->createQueryBuilder("Account");
        $qb->where(
                $qb->expr()->orX(
                        $qb->expr()->eq("Account.username", ":username"),
                        $qb->expr()->eq("Account.email", ":email")
                        )
                )
            ->setParameter("username", $usernameOrEmail)
            ->setParameter("email", $usernameOrEmail)
            ;
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function findOneByUsername($username, Account $accountExcluded = null)
    {
        $qb = $this->createQueryBuilder("Account");
        $qb->where(
                $qb->expr()->eq("Account.username", ":username")
                )
            ->setParameter("username", $username)
            ;
        
        if($accountExcluded !== null) {
            $qb->andWhere(
                    $qb->expr()->neq("Account", ":accout")
                    )
                ->setParameter("accout", $accountExcluded)
                ;
        }
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function findOneByEmail($email, Account $accountExcluded = null)
    {
        $qb = $this->createQueryBuilder("Account");
        $qb->where(
                $qb->expr()->eq("Account.email", ":email")
                )
            ->setParameter("email", $email)
            ;
        
        if($accountExcluded !== null) {
            $qb->andWhere(
                    $qb->expr()->neq("Account", ":accout")
                    )
                ->setParameter("accout", $accountExcluded)
                ;
        }
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}
