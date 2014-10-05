<?php

namespace Waldo\OpenIdConnect\AdminBundle\Services;

use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Waldo\OpenIdConnect\ProviderBundle\Utils\TokenCodeGenerator;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;
use Cocur\Slugify\Slugify;


/**
 * Description of ClientApplicationService
 *
 */
class ClientApplicationService
{
    /**
     * @var SecurityContextInterface
     */
    private $security;
    
    /**
     * @var EntityManager 
     */
    private $em;

    /**
     * @var Slugify 
     */
    private $slugify;
    
    public function __construct(SecurityContextInterface $security, EntityManager $em, Slugify $slugify)
    {
        $this->security = $security;
        $this->em = $em;
        $this->slugify = $slugify;
    }

    
    /**
     * Handle a client after they are been edit
     * 
     * @param Client $client
     */
    public function handleClient(Client $client)
    {
        
        if($client->getClientId() === null) {
            $this->createClientId($client);
        }
        
        if($client->getClientSecret() === null) {
            $client->setClientSecret(TokenCodeGenerator::generateCode());
        }
               
            
    }
    
    /**
     * Create a uniq client ID
     * @param Client $client
     */
    private function createClientId(Client $client)
    {
        $repo = $this->em->getRepository("WaldoOpenIdConnectModelBundle:Client");
        
        $x = 0;
        
        do {
            
            $clientId = $this->slugify->slugify($client->getClientName() . ($x == 0 ? '' : ' ' . $x));
            $x++;
            
        } while($repo->findOneByClientId($clientId) !== null);
        
        $client->setClientId($clientId);
    }
}
