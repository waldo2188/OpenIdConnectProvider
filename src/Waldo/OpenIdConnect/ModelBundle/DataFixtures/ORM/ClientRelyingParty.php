<?php

namespace Waldo\OpenIdConnect\ModelBundle\DataFixtures\ORM;

use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Process\Process;

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
        
        $process = new Process("ifconfig eth0 | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}");
        $process->run();       
        $hostsipaddress = str_replace("\n","",$process->getOutput());

        $client = new Client();

        $client
                ->setClientId('my_client_id')
                ->setClientName('My application name')
                ->setClientSecret('my_client_secret')
                ->setRedirectUris(
                        array(
                            'http://localhost/OIC-RP/web/app_dev.php/login_check',
                            sprintf('http://%s/OIC-RP/web/app_dev.php/login_check', $hostsipaddress)
                            )
                        )
                ->setContacts(array('contact@exemple.com <contactName contact>'))
                ->setApplicationType("web")
                ->setUserinfoEncryptedResponseAlg("RS256")
                ->setIdTokenEncryptedResponseAlg("RS512")
                ->setLogoUri("http://www.gravatar.com/avatar/9529e3f30dbf81d584eed66b2183cc9d?s=128")
                ->setClientUri("https://github.com/waldo2188")
                ;
        
        $manager->persist($client);
        $manager->flush();
    }

}
