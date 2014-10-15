<?php

namespace Waldo\OpenIdConnect\ModelBundle\EntityRepository;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction;
use Doctrine\ORM\EntityRepository;

class AccountActionRepository extends EntityRepository
{
    
    /**
     * 
     * @param Account $account
     * @param type $actionType
     * @return AccountAction
     */
    public function findOneOrGetNewAccountAction(Account $account, $actionType)
    {
        $qb = $this->createQueryBuilder("AccountAction");
        $qb->select("AccountAction, Account")
            ->leftJoin("AccountAction.account", "Account")
            ->where(
                $qb->expr()->orX(
                        $qb->expr()->eq("AccountAction.account", ":account"),
                        $qb->expr()->eq("AccountAction.type", ":type")
                        )
                )
            ->setParameter("account", $account)
            ->setParameter("type", $actionType)
            ;
        
        $accountAction = $qb->getQuery()->getOneOrNullResult();
        
        if($accountAction === null) {
            return new AccountAction();
        }
        
        return $accountAction;
    }
    
    /**
     * @param String $token
     * @param int $actionType
     */
    public function findOneAccountActionByToken($token, $actionType)
    {
        $qb = $this->createQueryBuilder("AccountAction");
        
        $qb->select("AccountAction, Account")
            ->leftJoin("AccountAction.account", "Account")
            ->where(
                    $qb->expr()->andX(
                            $qb->expr()->eq("AccountAction.token", ":token"),
                            $qb->expr()->eq("AccountAction.type", ":actionType")
                            )
                    )
            ->setParameter("token", $token)
            ->setParameter("actionType", $actionType)
                ;
        return $qb->getQuery()->getOneOrNullResult();        
    }
}
