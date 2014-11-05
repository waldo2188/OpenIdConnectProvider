<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Security\Authorization\Voter\TokenVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;

/**
 * Description of RevokeAuthorization
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class RevokeAuthorization
{
 
    /**
     * @var SecurityContextInterface 
     */
    protected $securityContext;

    /**
     * @var EntityManager 
     */
    protected $em;
    
    public function __construct(SecurityContextInterface $securityContext, EntityManager $em)
    {
        $this->securityContext = $securityContext;
        $this->em = $em;
    }
        
    public function revoke(Account $account, Client $client)
    {
        $token = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Token")->getTokenByClientAndAccount($client, $account);

        if (false === $this->securityContext->isGranted(TokenVoter::REVOKE_AUTHORIZATION, $token)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        
        $this->em->remove($token);
        $this->em->flush();
    }
    
}
