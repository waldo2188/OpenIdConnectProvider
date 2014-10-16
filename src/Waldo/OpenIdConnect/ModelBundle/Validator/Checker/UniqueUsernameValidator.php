<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Checker;

use Waldo\OpenIdConnect\ModelBundle\Validator\UniqueUsernameValidatorInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;

/**
 * UniqueUsernameValidator
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UniqueUsernameValidator implements UniqueUsernameValidatorInterface
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function exist($username, Account $account)
    {
        try {
        return null !== $this->em->getRepository("WaldoOpenIdConnectModelBundle:Account")
                ->findOneByUsername($username, $account);
        } catch (NonUniqueResultException $e) {
            return true;
        }
    }
}
