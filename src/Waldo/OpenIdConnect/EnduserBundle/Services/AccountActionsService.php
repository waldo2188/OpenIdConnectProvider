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
     * Handle account after modification.
     * 
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Account $account
     */
    public function handleEditProfile(Account $account)
    {
        $isSameEmail = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Account")
                ->isSameEmailForThisAccount($account->getEmail(), $account);
        
        
        if ($isSameEmail !== true) {
            
            $extendedData = array("newEmail" => $account->getEmail());
            
            $account->setEmailVerified(false)
                    ->setEmail($isSameEmail);
                                    
            $this->sendMailWithToken(
                    $account,
                    "oicp_registration_account_new_email",
                    AccountAction::ACTION_EMAIL_CHANGE_VALIDATION,
                    "email_verification",
                    $extendedData
            );
            
            return 'email_has_changed';
        }
        
        return;
    }

    /**
     * Generic method for handleLostPassword and sendMailRegistrationConfirmation
     * 
     * @param Account $account
     * @param type $routeName
     * @param type $actionType
     * @param type $templateEmail
     */
    protected function sendMailWithToken(Account $account, $routeName, $actionType, $templateEmail, $extendedData = array())
    {
        $token = TokenCodeGenerator::generateCode();
        
        $request = new Request();
        $request->attributes->set('token', $token);
        
        $link = $this->httpUtils->generateUri($request, $routeName);

        /* @var $accountAction AccountAction */
        $accountAction = $this->em->getRepository("WaldoOpenIdConnectModelBundle:AccountAction")
                ->findOneOrGetNewAccountAction($account, $actionType);
        
        $accountAction
                ->setType($actionType)
                ->setAccount($account)
                ->setToken($token)
                ->setIssuedAt(new \DateTime('now'))
                ->setExtendedData($extendedData);
        
        $this->em->persist($accountAction);
        $this->em->flush();
        
        $mailParams = new MailParameters();
        $mailParams->addBodyParameters("link", $link)
                ->addBodyParameters('tokenTimeValidity', AccountAction::TOKEN_VALIDITY);
        
        $this->mailingService->sendEmail(
                array($account->getEmail()),
                $templateEmail,
                $mailParams
                );
    }
       
    
}
