<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Event;

final class LdapEvents
{
    const PRE_BIND = 'waldo_oic_ldap.security.authentication.pre_bind';
    const POST_BIND = 'waldo_oic_ldap.security.authentication.post_bind';
}
