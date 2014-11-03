<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Services;

use Waldo\OpenIdConnect\EnduserBundle\Services\AccountActionsService;
use Waldo\OpenIdConnect\EnduserBundle\Services\LostPasswordManagerInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\AccountAction;

/**
 * LostPasswordService
 * Service manager recovering a password based on a username / email
 * 
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LostPasswordService extends AccountActionsService
{
    /**
     * @var array<LostPasswordManagerInterface>
     */
    private $lostPasswordManagerList = array();
    
    /**
     * Do an action or not for the given username or email
     * 
     * @param string $usernameOrEmail
     * @return bool True if the user has been found and an action has been done, fasle otherwise
     */
    public function handleLostPassword($usernameOrEmail)
    {
        if($this->handleForUserInDataBase($usernameOrEmail)) {
           return true; 
        }
        
        foreach($this->lostPasswordManagerList as $lostPasswordManager) {
            
            $result = $lostPasswordManager->handleLostPassword($usernameOrEmail);
                        
            if($result === true) {
                return true; 
            }
        }
        
        return false; 
    }

    public function addLostPasswordManager(LostPasswordManagerInterface $lostPasswordManager)
    {
        $this->lostPasswordManagerList[] = $lostPasswordManager;
    }

        
    /**
     * Check if the username/email belong to the database.
     * If the user is found, an email is sent with the procedure to change the password
     * 
     * @param string $username Username or Email
     */
    private function handleForUserInDataBase($username)
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
     * @param string $token
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

        $isValid = false;
        
        if($accountAction->getIssuedAt()->getTimestamp() + (AccountAction::TOKEN_VALIDITY * 3600 ) >= time()) {
            $isValid = $accountAction;
        }
        
        return $isValid;    
    }
}
