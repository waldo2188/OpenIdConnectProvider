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

            for ($x = 0; $x < 20; $x++) {

                $user = new Account();

                $user->setUsername('user' . $this->getSuffix($x))
                        ->setGivenName(sprintf('user given name %s', $this->getSuffix($x)))
                        ->setName(sprintf('user name %s', $this->getSuffix($x)))
                        ->setFamilyName(sprintf('user family name %s', $this->getSuffix($x)))
                        ->setMiddleName(sprintf('user middle name %s', $this->getSuffix($x)))
                        ->setEmail(sprintf('user%s@exemple.com', $this->getSuffix($x)))
                        ->setEmailVerified(true)
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
                        ->setPicture("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABV0RVh0Q3JlYXRpb24gVGltZQA2LzIwLzA4DqTMIgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNAay06AAAAJ6SURBVGiB7ZoxchNBEEWfICFU6JpofQKb6gOgkMy+AeIEmBNgTmDpBNgnQDoBS0QRdJV8AuRoQ0QGkQlmBC6X5Z3u2bWlgl+1ydb87f7TM90zLQ2ur6/ZZTx5bAdK8V/AY2PnBQy6/qCIjIFj4ACo0us5MFbVVdf2OhOQHH/HX6dvYwEM0zNV1dMu7BYLEJEhcAaMjdRzVX1dav9p6QdCCF+Alw7qYQhh1TTN1xL7RZtYRM6Aw4JPTESkhO8XICIj4KTEeMKHErJ7CYUQPgJ7JcYT9kIINE3z2UN2beI0+5883A1YAc9VdWklepfQGydvE4bEFGyGOQIiUgHfPMYysG+NgicCXc9+0bc9AsYOTi6OrQSTABE5Iq7XvlClyp4NawTMM+TAgWWwVcDIOL53WAVUfThRgmwBIvKiT0duYGQZvPM3MouAZV9O3EJtGZwtQFWviGeWrYJ1CdV9OHELC8vgbRNQq+oPC2HbBJxaCZ7T6Hf6OU4sVXXfSvKk0drBycHMQ/IIMG0yA1wZbpsi4OpO3CtARIZ3tD0qj6EMjDykezdxOv/UxPBeptcH9HcnOAfeW66VrVmox6xzH5bE/umkbWDOHjgt9caBisyLTasAVZ3yMEeIm6jJ7PpldeZCCDPgJzFTPHO71Y4lcKKqb5um+ZVD8FTi9SYeEe/IRc1ZYgFbECvxhZVsrgOqeknMSFd0k1JHxImce8jZEUjtjuP0HHmMZeACmKhqdrVvqwMP4fRdWABTYNb2u9qdAkRk7fSr7n0zYUVcWjUbxPwRkFrmY+JMP3ThysWcuOnrdbUeiMiE6HT1eH65sADmAxHZ6X97/FN9oa3Eb9zamUI5QgiMAAAAAElFTkSuQmCC")
                        ->setRoles(array("ROLE_USER"))
                ;
                
                $this->encodePassword($user);

                $manager->persist($user);
            }
            
            $user = new Account();
            $user->setUsername('admin')
                    ->setGivenName("ADMIN")
                    ->setEmail("admin@exemple.com")
                    ->setEnabled(true)
                    ->setLocked(false)
                    // Picture from http://brandonmathis.com/projects/fancy-avatars/demo/
                    ->setPicture("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABV0RVh0Q3JlYXRpb24gVGltZQA2LzIwLzA4DqTMIgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNAay06AAAAJ6SURBVGiB7ZoxchNBEEWfICFU6JpofQKb6gOgkMy+AeIEmBNgTmDpBNgnQDoBS0QRdJV8AuRoQ0QGkQlmBC6X5Z3u2bWlgl+1ydb87f7TM90zLQ2ur6/ZZTx5bAdK8V/AY2PnBQy6/qCIjIFj4ACo0us5MFbVVdf2OhOQHH/HX6dvYwEM0zNV1dMu7BYLEJEhcAaMjdRzVX1dav9p6QdCCF+Alw7qYQhh1TTN1xL7RZtYRM6Aw4JPTESkhO8XICIj4KTEeMKHErJ7CYUQPgJ7JcYT9kIINE3z2UN2beI0+5883A1YAc9VdWklepfQGydvE4bEFGyGOQIiUgHfPMYysG+NgicCXc9+0bc9AsYOTi6OrQSTABE5Iq7XvlClyp4NawTMM+TAgWWwVcDIOL53WAVUfThRgmwBIvKiT0duYGQZvPM3MouAZV9O3EJtGZwtQFWviGeWrYJ1CdV9OHELC8vgbRNQq+oPC2HbBJxaCZ7T6Hf6OU4sVXXfSvKk0drBycHMQ/IIMG0yA1wZbpsi4OpO3CtARIZ3tD0qj6EMjDykezdxOv/UxPBeptcH9HcnOAfeW66VrVmox6xzH5bE/umkbWDOHjgt9caBisyLTasAVZ3yMEeIm6jJ7PpldeZCCDPgJzFTPHO71Y4lcKKqb5um+ZVD8FTi9SYeEe/IRc1ZYgFbECvxhZVsrgOqeknMSFd0k1JHxImce8jZEUjtjuP0HHmMZeACmKhqdrVvqwMP4fRdWABTYNb2u9qdAkRk7fSr7n0zYUVcWjUbxPwRkFrmY+JMP3ThysWcuOnrdbUeiMiE6HT1eH65sADmAxHZ6X97/FN9oa3Eb9zamUI5QgiMAAAAAElFTkSuQmCC")
                    ->setRoles(array("ROLE_ADMIN"))
            ;
            $this->encodePassword($user);
            $manager->persist($user);
            
            $user = new Account();
            $user->setUsername('super.admin')
                    ->setGivenName("SUPER ADMIN")
                    ->setEmail("super.adminadmin@exemple.com")
                    ->setEnabled(true)
                    ->setLocked(false)
                    ->setRoles(array("ROLE_SUPER_ADMIN"))
            ;
            $this->encodePassword($user);
            $manager->persist($user);
        }

        $manager->flush();
    }
    
    private function setUser($user, $x)
    {
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
                        ->setPicture("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABV0RVh0Q3JlYXRpb24gVGltZQA2LzIwLzA4DqTMIgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNAay06AAAAJ6SURBVGiB7ZoxchNBEEWfICFU6JpofQKb6gOgkMy+AeIEmBNgTmDpBNgnQDoBS0QRdJV8AuRoQ0QGkQlmBC6X5Z3u2bWlgl+1ydb87f7TM90zLQ2ur6/ZZTx5bAdK8V/AY2PnBQy6/qCIjIFj4ACo0us5MFbVVdf2OhOQHH/HX6dvYwEM0zNV1dMu7BYLEJEhcAaMjdRzVX1dav9p6QdCCF+Alw7qYQhh1TTN1xL7RZtYRM6Aw4JPTESkhO8XICIj4KTEeMKHErJ7CYUQPgJ7JcYT9kIINE3z2UN2beI0+5883A1YAc9VdWklepfQGydvE4bEFGyGOQIiUgHfPMYysG+NgicCXc9+0bc9AsYOTi6OrQSTABE5Iq7XvlClyp4NawTMM+TAgWWwVcDIOL53WAVUfThRgmwBIvKiT0duYGQZvPM3MouAZV9O3EJtGZwtQFWviGeWrYJ1CdV9OHELC8vgbRNQq+oPC2HbBJxaCZ7T6Hf6OU4sVXXfSvKk0drBycHMQ/IIMG0yA1wZbpsi4OpO3CtARIZ3tD0qj6EMjDykezdxOv/UxPBeptcH9HcnOAfeW66VrVmox6xzH5bE/umkbWDOHjgt9caBisyLTasAVZ3yMEeIm6jJ7PpldeZCCDPgJzFTPHO71Y4lcKKqb5um+ZVD8FTi9SYeEe/IRc1ZYgFbECvxhZVsrgOqeknMSFd0k1JHxImce8jZEUjtjuP0HHmMZeACmKhqdrVvqwMP4fRdWABTYNb2u9qdAkRk7fSr7n0zYUVcWjUbxPwRkFrmY+JMP3ThysWcuOnrdbUeiMiE6HT1eH65sADmAxHZ6X97/FN9oa3Eb9zamUI5QgiMAAAAAElFTkSuQmCC")
                        ->setRoles(array("ROLE_USER"));
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
