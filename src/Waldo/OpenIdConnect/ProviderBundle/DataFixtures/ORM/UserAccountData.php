<?php

namespace Waldo\OpenIdConnect\ProviderBundle\DataFixtures\ORM;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Address;
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
class UserAccountData extends ContainerAware implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        if ($this->container->getParameter('kernel.environment') == 'dev') {

            for ($x = 0; $x < 2; $x++) {

                $user = new Account();

                $user->setUsername('user' . $this->getSuffix($x))
                        ->setGivenName(sprintf('user given name %s', $this->getSuffix($x)))
                        ->setName(sprintf('user name %s', $this->getSuffix($x)))
                        ->setFamilyName(sprintf('user family name %s', $this->getSuffix($x)))
                        ->setMiddleName(sprintf('user middle name %s', $this->getSuffix($x)))
                        ->setEmail(sprintf('user%s@exemple.com', $this->getSuffix($x)))
                        ->setPhoneNumber(sprintf('user phone number %s', $this->getSuffix($x)))
                        ->setBirthdate(\DateTime::createFromFormat('Y/m/d', '1982/02/' . (11 + $x) ))
                        ->setAddress(
                                new Address(
                                        sprintf('address formated %s', $this->getSuffix($x)),
                                        sprintf('street address %s', $this->getSuffix($x)),
                                        sprintf('locality %s', $this->getSuffix($x)),
                                        sprintf('region %s', $this->getSuffix($x)),
                                        sprintf('postal code %s', $this->getSuffix($x)),
                                        sprintf('contry %s', $this->getSuffix($x)))
                        )
                        ->setEnabled(true)
                        ->setLocked(false)
                        ->setRoles(array("ROLE_USER"))
                ;
                
                $this->encodePassword($user);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }

    private function encodePassword(Account $user)
    {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $user->setSalt(md5(time()));
        $pass = $encoder->encodePassword($user->getUsername(), $user->getSalt());
        $user->setPassword($pass);
    }

    private function getSuffix($x)
    {
        return $x > 0 ? ' ' . $x : '';
    }

}
