<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction;
use Waldo\OpenIdConnect\MailingBundle\Mailing\MailingService;
use Waldo\OpenIdConnect\MailingBundle\Mailing\MailParameters;
use Waldo\OpenIdConnect\ProviderBundle\Utils\TokenCodeGenerator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * LostAccountService
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AccountActionsService
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    /**
     * @var HttpUtils 
     */
    protected $httpUtils;
    
    /**
     * @var MailingService 
     */
    protected $mailingService;


    /**
     * @var Psr\Log\LoggerInterface 
     */
    protected $logger;
    public function __construct(EntityManager $em, HttpUtils $httpUtils, 
            MailingService $mailingService, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->httpUtils = $httpUtils;
        $this->mailingService = $mailingService;
        $this->logger = $logger;
    }

    /**
     * Send an email to the enduser to complete is registration
     * 
     * @param Account $account
     */
    public function sendMailRegistrationConfirmation(Account $account)
    {
        $this->sendMailWithToken(
                $account,
                "oicp_registration_account_validation",
                AccountAction::ACTION_EMAIL_VALIDATION,
                "registration_confirmation"
                );
    }
    
    /**
     * send an email to the end user to provide him the opportunity to regenerate password
     * 
     * @param string $username Username or Email
     */
    public function handleLostPassword($username)
    {
        /* @var $account Account */
        $account = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Account")
                ->findByEmailOrUsername($username);
        
        if($account === null) {
            return false;
        }
        
        $this->sendMailWithToken(
                $account,
                "oicp_lost_account_change_password",
                AccountAction::ACTION_ACCOUNT_LOST,
                "lost_account"
                );
        
        return true;
    }
    
    /**
     * Check if the token is a valid one
     * 
     * @param AccountAction $accountAction
     * @return false|Account
     */
    public function isValidLostPasswordToken($token)
    {
        /* @var $accountAction AccountAction */
        $accountAction = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneBy(array('token' => $token, 'type' => AccountAction::ACTION_ACCOUNT_LOST));
        
        if($accountAction === null)
        {
            return false;
        }

        $accountAction->getIssuedAt()->modify("+1 hour");
        
        $isValid = false;
        
        if($accountAction->getIssuedAt() > new \DateTime('now')) {
            $isValid = $accountAction;
        }
        
        return $isValid;    
    }
    
    /**
     * Generic method for handleLostPassword and sendMailRegistrationConfirmation
     * 
     * @param Account $account
     * @param type $routeName
     * @param type $actionType
     * @param type $templateEmail
     */
    protected function sendMailWithToken(Account $account, $routeName, $actionType, $templateEmail)
    {
        $token = TokenCodeGenerator::generateCode();
        
        $request = new Request();
        $request->attributes->set('token', $token);
        
        $link = $this->httpUtils->generateUri($request, $routeName);

        $accountAction = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneOrGetNewAccountAction($account, $actionType);
        
        $accountAction
                ->setType($actionType)
                ->setAccount($account)
                ->setToken($token);
        
        $this->em->persist($accountAction);
        $this->em->flush();
        
        $mailParams = new MailParameters();
        $mailParams->addBodyParameters("link", $link);
        
        $this->mailingService->sendEmail(
                array($account->getEmail()),
                $templateEmail,
                $mailParams
                );
    }
       
    
}
