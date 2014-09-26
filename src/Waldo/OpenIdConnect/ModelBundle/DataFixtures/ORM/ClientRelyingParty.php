<?php

namespace Waldo\OpenIdConnect\ModelBundle\DataFixtures\ORM;

use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * UserAccount
 * 
 * Create fake account to simplify development
 * 
 * User login (username property) is also their password (just for dev)
 * To create user in database use this cmd `php app/console doctrine:fixtures:load`
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ClientRelyingParty extends ContainerAware implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $client = new Client();

        $client
                ->setClientId('my_client_id')
                ->setClientName('My application name')
                ->setClientSecret('my_client_secret')
                ->setRedirectUris(array('http://localhost/OIC-RP/web/app_dev.php/login_check'))
                ->setContacts(array('contact@exemple.com <contactName contact>'))
                ->setApplicationType("web")
                ->setUserinfoEncryptedResponseAlg("RS256")
                ->setIdTokenEncryptedResponseAlg("RS512")
                ->setLogoUri("http://www.gravatar.com/avatar/9529e3f30dbf81d584eed66b2183cc9d?s=128")
                ;
        
        $manager->persist($client);
        $manager->flush();
    }

}
