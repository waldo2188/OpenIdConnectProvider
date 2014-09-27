<?php

namespace Waldo\OpenIdConnect\ModelBundle\EntityRepository;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
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
            
            if($accountExcluded !== null && $accountExcluded->getId() !== null) {
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
        
        if($accountExcluded !== null && $accountExcluded->getId() !== null) {
            $qb->andWhere(
                    $qb->expr()->neq("Account", ":accout")
                    )
                ->setParameter("accout", $accountExcluded)
                ;
        }
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function isSameEmailForThisAccount($email, Account $account)
    {
        $qb = $this->createQueryBuilder("Account");
        $qb->where(
                $qb->expr()->andX(
                        $qb->expr()->eq("Account.email", ":email"),
                        $qb->expr()->eq("Account", ":account")
                    )
                )
            ->setParameter("email", $email)
            ->setParameter("account", $account)
            ;
                
        return $qb->getQuery()->getOneOrNullResult() !== null ? true : false;
    }
    
}
