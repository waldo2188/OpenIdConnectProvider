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
    
    /**
     * If the $email is the same as store in database, this method return true
     * If the two email are different, the method return the old email
     * 
     * @param type $email
     * @param Account $account
     * @return boolean|string
     */
    public function isSameEmailForThisAccount($email, Account $account)
    {
        $qb = $this->createQueryBuilder("Account");
        $qb->select("Account.email")
                ->where($qb->expr()->eq("Account", ":account"))
                ->setParameter("account", $account)
        ;

        $dbEmail = $qb->getQuery()->getOneOrNullResult();

        if ($dbEmail['email'] === $email) {
            return true;
        }

        return $dbEmail['email'];
    }

}
