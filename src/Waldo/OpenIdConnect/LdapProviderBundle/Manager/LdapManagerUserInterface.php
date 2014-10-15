<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Manager;

use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapConnectionInterface;

interface LdapManagerUserInterface
{

    /**
     * @param LdapConnectionInterface $conn
     */
    public function __construct(LdapConnectionInterface $conn);

    public function exists($username);

    public function auth();

    public function doPass();

    public function getDn();

    public function getCn();

    public function getEmail();

    public function getAttributes();

    public function getLdapUser();

    public function getDisplayName();

    public function getGivenName();

    public function getSurname();

    public function getUsername();

    public function getRoles();

    public function setUsername($username);

    public function setPassword($password);
}
