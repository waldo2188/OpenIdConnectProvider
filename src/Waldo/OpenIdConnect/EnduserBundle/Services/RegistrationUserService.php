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
    
    /**
     * Helper for encoding password
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Account $account
     */
    public function encodePassword(Account $account)
    {
        $encoder = $this->encoderFactory->getEncoder($account);
        
        $account->setSalt(TokenCodeGenerator::generateCode());
        
        $password = $encoder->encodePassword($account->getPassword(), $account->getSalt());
        $account->setPassword($password);
    }
    
    /**
     * Handle the token for validate an account
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction $token
     * @return Account
     */
    public function handleValidationToken($token)
    {
        
        $user = null;
        
        /* @var $token AccountAction */
        $token = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneAccountActionByToken($token, AccountAction::ACTION_EMAIL_VALIDATION);
        
        if($token === null)
        {
            return false;
        }

        $token->getIssuedAt()->modify("+1 hour");
                
        if($token->getIssuedAt() > new \DateTime('now')) {
            $user = $token->getAccount();
            $user->setEmailVerified(true);
            
            if(count($user->getRoles()) <= 0) {
                $user->setRoles(array("ROLE_USER"));
            }
            
            $this->em->persist($user);   
        }
        

        $this->em->remove($token);
        
        $this->em->flush();
        return $user;    
    }
    
    /**
     * Handle the token for validate enduser's new email
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction $token
     * @return boolean
     */
    public function handleNewEmailToken($token)
    {
        /* @var $token AccountAction */
        $token = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneBy(array('token' => $token, 'type' => AccountAction::ACTION_EMAIL_CHANGE_VALIDATION));
        
        if($token === null) {
            return false;
        }
        
        $token->getAccount()
                ->setEmailVerified(true)
        ;
        $this->em->persist($token->getAccount());

        $this->em->remove($token);

        $this->em->flush();
        
        return true;
    }

}
