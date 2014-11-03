<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Services;

use Waldo\OpenIdConnect\EnduserBundle\Services\LostPasswordManagerInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Waldo\OpenIdConnect\MailingBundle\Mailing\MailingService;

class LostPasswordService implements LostPasswordManagerInterface
{
    /**
     * @var LdapManagerUserInterface 
     */
    private $ldapManager;
    
    /**
     * @var MailingService 
     */
    private $mailingService;
    
    public function __construct(LdapManagerUserInterface $ldapManager, MailingService $mailingService)
    {
        $this->ldapManager = $ldapManager;
        $this->mailingService = $mailingService;
    }

    
    public function handleLostPassword($usernameOrEmail)
    {
        $existe = false;
        
        try {   
            $existe |= $this->ldapManager->exists($usernameOrEmail);
        } catch (\Exception $e) { }
        
        if(strpos($usernameOrEmail, "@") !== false) {
            try {
                $existe |= $this->ldapManager->existsEmail($usernameOrEmail);    
            } catch (\Exception $e) { }
        }
        
        if($existe !== false) {

            $this->mailingService->setTemplateBlock("WaldoOpenIdConnectLdapProviderBundle");
            $this->mailingService->sendEmail(array($this->ldapManager->getEmail()), "lost_account");
                    
            return true;
        }
        
        return false;
    }

}
