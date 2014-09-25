<?php

namespace Waldo\OpenIdConnect\ModelBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface AccountInterface extends AdvancedUserInterface
{
    /**
     * Return the time when the last End-User authentication occurred
     * 
     * @return \DateTime
     */
    public function getLastLoginAt();

    /**
     * set the time when the last End-User authentication occurred
     * 
     * @param \DateTime
     */
    public function setLastLoginAt(\DateTime $lastLoginAt);

}
