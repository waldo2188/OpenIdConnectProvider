<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Form\Type\RegistrationFormType;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * RegistrationUserService
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class RegistrationUserService
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    /**
     * @var Psr\Log\LoggerInterface 
     */
    protected $logger;
    
    public function __construct(EntityManager $em, LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    
    public function getBlankUser()
    {
        return new Account();
    }
    
    public function getFormType()
    {
        return new RegistrationFormType();
    }
}
