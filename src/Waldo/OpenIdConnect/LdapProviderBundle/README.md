# LdapProviderBundle

LdapProviderBundle provides LDAP authentication without using Apache's `mod_ldap`.
The bundle instead relies on PHP's [LDAP extension](http://php.net/manual/en/book.ldap.php) along with a form to authenticate users.
LdapBundle can also be used for authorization by retrieving the user's roles defined in LDAP.

This bundle is a part of Waldo's OpendID Connect Bundle.

This bundle is a fork of [LdapBundle](https://github.com/BorisMorel/LdapBundle) from [Boris Morel](https://github.com/BorisMorel)

## Install

1. Download with composer
2. Enable the Bundle
3. Configure LdapBundle in security.yml
4. Import LdapBundle routing
5. Implement Logout
6. Use chain provider
7. Subscribe to PRE_BIND event
8. Subscribe to POST_BIND event

### Get the Bundle

### Composer
Add LdapProviderBundle in your project's `composer.json`

```json
{
    "require": {
        // TODO define a name
    }
}
```

### Enable the Bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Waldo\OpenIdConnect\LdapProviderBundle\WaldoOpenIdConnectLdapProviderBundle(),
    );
}
```

### Configure security.yml

**Note:**
> An example `security.yml` file is located within the bundle at `./Resources/Docs/security.yml`

``` yaml
# security.yml

security:
  firewalls:
    restricted_area:
      pattern:          ^/
      anonymous:        ~
      provider:         ldap
      waldo_oic_ldap: ~ # this attribute is required
      # alternative configuration
      # waldo_oic_ldap:
      #   login_path:   /ninja/login
      logout:
        path:           /logout
        target:         /

  providers:
    ldap:
      id: waldo_oic_ldap.security.user.provider
                
  encoders:
    IMAG\LdapBundle\User\LdapUser: plaintext

  access_control:
    - { path: ^/login,          roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/,               roles: IS_AUTHENTICATED_FULLY }

waldo_oic_ldap:
  client:
    host: your.host.foo
    port: 389
#    version: 3 # Optional
#    username: foo # Optional
#    password: bar # Optional
#    network_timeout: 10 # Optional
#    referrals_enabled: true # Optional
#    bind_username_before: true # Optional
#    skip_roles: false # Optional

  user:
    base_dn: ou=people,dc=host,dc=foo
#    filter: (&(foo=bar)(ObjectClass=Person)) #Optional
    name_attribute: uid
  roles:
    role_1: # Or whatever you want as name, it does not matter
        base_dn: ou=group, dc=host, dc=foo
    #    filter: (ou=group) #Optional
        name_attribute: cn
        user_attribute: member
        user_id: [ dn or username ]
        roles: [Whatever_You_Want_as_role_name]

```

**How to set ROLEs:**
``` yaml
waldo_oic_ldap:
#...
  roles:
    classic:
        base_dn: ou=people, dc=host, dc=foo
        filter: (givenName=Williams)
        name_attribute: cn
        user_attribute: uid
        user_id: username
        roles: [ROLE_USER, ROLE_LDAP]
```
The config above will check if the user has a givenName equal to "Williams". 
If this is the case, the user will have the roles defined in the `roles`.

**You should configure the parameters under the `waldo_oic_ldap` section to match your environment.**

**Note:**

> The optional parameters have default values if not set.
> You can disable default values by setting a parameter to NULL.

``` yaml
# app/config/security.yml
waldo_oic_ldap:
  # ...
  role:
    # ...
    filter: NULL
```

### Import routing

``` yaml
# app/config/routing.yml

waldo_oic_ldap:
  resource: "@IMAGLdapBundle/Resources/config/routing.yml"
```

### Implement Logout

Just create a link with a logout target.

``` html
<a href="{{ path('logout') }}">Logout</a>
```

**Note:**
> You can refer to the official Symfony documentation :
> http://symfony.com/doc/current/book/security.html#logging-out

### Chain provider ###

You can also chain the login form with other providers, such as database_provider, in_memory provider, etc.

``` yml
# app/config/security.yml
security:
    firewalls:
        secured_area:
            pattern: ^/
            anonymous: ~
            waldo_oic_ldap:
                provider: multiples
            logout:
                path: logout
    providers:
        multiples:
            chain:
                providers: [ldap, db]          
        ldap:
            id: waldo_oic_ldap.security.user.provider
        db:
            entity: { class: FQDN\User }
```

**Note:**
> If you have set the config option `bind_username_before: true` you must chain the providers with the ldap provider in the last position.

``` yml
# app/config/security.yml

providers: [db, ldap]          
```

### Subscribe to PRE_BIND event

The PRE_BIND is fired before the user is authenticated via LDAP. Here you can write a listener to perform your own logic before the user is bound/authenticated to LDAP.
For example, to add your own roles or do other authentication/authorization checks with your application.

If you want to break the authentication process within your listener, throw an Exception.

Example listener:
``` xml
<service id="ldap.listener" class="Acme\HelloBundle\EventListener\LdapSecuritySubscriber">
    <tag name="kernel.event_subscriber" />
</service>
```

Example:
```php
<?php

namespace Acme\HelloBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use IMAG\LdapBundle\Event\LdapUserEvent;

/**
 * Performs logic before the user is found to LDAP
 */
class LdapSecuritySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            \IMAG\LdapBundle\Event\LdapEvents::PRE_BIND => 'onPreBind',
        );
    }

    /**
     * Modifies the User before binding data from LDAP
     *
     * @param \IMAG\LdapBundle\Event\LdapUserEvent $event
     */
    public function onPreBind(LdapUserEvent $event)
    {
        $user = $event->getUser();
        $config = $this->appContext->getConfig();

        $ldapConf = $config['ldap'];

        if (!in_array($user->getUsername(), $ldapConf['allowed'])) {
            throw new \Exception(sprintf('LDAP user %s not allowed', $user->getUsername()));
        }

		$user->addRole('ROLE_LDAP');
        $event->setUser($user);
    }
}
```
### Subscribe to POST_BIND event

The POST_BIND is fired after the user is authenticated via LDAP. You can use it in exactly the same manner as PRE_BIND.

**Note:**

> However each time a page is refreshed, Symfony call the refreshUser method in the provider that is used and doesn't trigger these events (PRE_BIND and POST_BIND).
> If you want to override user (for example like credentials, roles ...), you must create a new provider and override this method.
