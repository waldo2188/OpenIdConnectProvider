<?php

namespace Waldo\OpenIdConnect\ModelBundle\DataFixtures\ORM;

use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\Address;
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
                                        sprintf("address formated %s\nsecond ligne\nthird line", $this->getSuffix($x)),
                                        sprintf('street address %s', $this->getSuffix($x)),
                                        sprintf('locality %s', $this->getSuffix($x)),
                                        sprintf('region %s', $this->getSuffix($x)),
                                        sprintf('postal code %s', $this->getSuffix($x)),
                                        sprintf('contry %s', $this->getSuffix($x)))
                        )
                        ->setEnabled(true)
                        ->setLocked(false)
                        // Picture from http://brandonmathis.com/projects/fancy-avatars/demo/
                        ->setPicture("data:image/png;base64,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")
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
