<?php

namespace Waldo\OpenIdConnect\ProviderBundle\EntityRepository;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Entity\AccountAction;
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
        $qb->where(
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
}
