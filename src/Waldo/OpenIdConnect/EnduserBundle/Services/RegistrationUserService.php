<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\AccountFormType;
use Waldo\OpenIdConnect\ProviderBundle\Utils\TokenCodeGenerator;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
     *
     * @var Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;
    
    /**
     * @var Psr\Log\LoggerInterface 
     */
    protected $logger;
    
    public function __construct(EntityManager $em, EncoderFactoryInterface $encoderFactory,
            LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->logger = $logger;
    }

    
    public function getBlankUser()
    {
        return new Account();
    }
    
    public function getFormType()
    {
        return new AccountFormType();
    }
    
    public function encodePassword(Account $account)
    {
        $encoder = $this->encoderFactory->getEncoder($account);
        
        $account->setSalt(TokenCodeGenerator::generateCode());
        
        $password = $encoder->encodePassword($account->getPassword(), $account->getSalt());
        $account->setPassword($password);
    }
    
    public function handleValidationToken($token)
    {
        /* @var $token AccountAction */
        $token = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneBy(array('token' => $token, 'type' => AccountAction::ACTION_EMAIL_VALIDATION));
        
        if($token === null)
        {
            return false;
        }

        $token->getIssuedAt()->modify("+1 hour");
        
        $isValid = false;
        
        if($token->getIssuedAt() < new \DateTime('now')) {
            $token->getAccount()
                    ->setEmailVerified(true)
                    ->setRoles(array("USER_ROLE"))
                    ;
            $this->em->persist($token->getAccount());
            return true;
        }
        
        $this->em->remove($token);
        
        $this->em->flush();
        return $isValid;    
    }
}
