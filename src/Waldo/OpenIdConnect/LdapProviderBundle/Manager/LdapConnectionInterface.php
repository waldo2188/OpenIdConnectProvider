<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Manager;

use Monolog\Logger;

interface LdapConnectionInterface
{
    /**
     * @param array $params
     * @param Logger $logger
     */
    public function __construct(array $params, Logger $logger);

    public function search(array $params);

    public function bind($user_dn, $password);

    public function getParameters();

    public function getHost();

    public function getPort();

    public function getBaseDn($index);

    public function getFilter($index);

    public function getNameAttribute($index);

    public function getUserAttribute($index);

    public function getErrno($resource = null);

    public function getError($resource = null);
}
