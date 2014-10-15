<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Provider;

use Waldo\OpenIdConnect\LdapProviderBundle\Exception\ConnectionException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Manager\LdapManagerUserInterface;
use Waldo\OpenIdConnect\LdapProviderBundle\Event\LdapUserEvent;
use Waldo\OpenIdConnect\LdapProviderBundle\Event\LdapEvents;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

class LdapAuthenticationProvider implements AuthenticationProviderInterface
{

    private
            $userProvider,
            $ldapManager,
            $dispatcher,
            $providerKey,
            $hideUserNotFoundExceptions

    ;

    /**
     * Constructor
     *
     * Please note that $hideUserNotFoundExceptions is true by default in order
     * to prevent a possible brute-force attack.
     *
     * @param UserProviderInterface $userProvider
     * @param AuthenticationProviderInterface $daoAuthenticationProvider
     * @param LdapManagerUserInterface $ldapManager
     * @param EventDispatcherInterface $dispatcher
     * @param string $providerKey
     * @param boolean $hideUserNotFoundExceptions
     */
    public function __construct(
    UserProviderInterface $userProvider, AuthenticationProviderInterface $daoAuthenticationProvider, LdapManagerUserInterface $ldapManager, EventDispatcherInterface $dispatcher = null, $providerKey, $hideUserNotFoundExceptions = true
    )
    {
        $this->userProvider = $userProvider;
        $this->daoAuthenticationProvider = $daoAuthenticationProvider;
        $this->ldapManager = $ldapManager;
        $this->dispatcher = $dispatcher;
        $this->providerKey = $providerKey;
        $this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            throw new AuthenticationException('Unsupported token');
        }

        try {
            $user = $this->userProvider
                    ->loadUserByUsername($token->getUsername());

            if ($user instanceof Account) {
                return $this->ldapAuthenticate($user, $token);
            }
        } catch (\Exception $e) {
            if ($e instanceof ConnectionException || $e instanceof UsernameNotFoundException) {
                if ($this->hideUserNotFoundExceptions) {
                    throw new BadCredentialsException('Bad credentials', 0, $e);
                }
            }

            throw $e;
        }

        if ($user instanceof UserInterface) {
            return $this->daoAuthenticationProvider->authenticate($token);
        }
    }

    /**
     * Authentication logic to allow Ldap user
     *
     * @param Account $user
     * @param TokenInterface $token
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken $token
     */
    private function ldapAuthenticate(Account $user, TokenInterface $token)
    {
        $userEvent = new LdapUserEvent($user);
        if (null !== $this->dispatcher) {
            try {
                $this->dispatcher->dispatch(LdapEvents::PRE_BIND, $userEvent);
            } catch (AuthenticationException $expt) {
                if ($this->hideUserNotFoundExceptions) {
                    throw new BadCredentialsException('Bad credentials', 0, $expt);
                }

                throw $expt;
            }
        }

        $this->bind($user, $token);

        $this->manageDatabaseUser($user);
        
        if (null === $user->getExternalId()) {
            $user = $this->reloadUser($user);
        }

        if (null !== $this->dispatcher) {
            $userEvent = new LdapUserEvent($user);
            try {
                $this->dispatcher->dispatch(LdapEvents::POST_BIND, $userEvent);
            } catch (AuthenticationException $authenticationException) {
                if ($this->hideUserNotFoundExceptions) {
                    throw new BadCredentialsException('Bad credentials', 0, $authenticationException);
                }
                throw $authenticationException;
            }
        }

        $token = new UsernamePasswordToken($userEvent->getUser(), null, $this->providerKey, $userEvent->getUser()->getRoles());
        $token->setAttributes($token->getAttributes());

        return $token;
    }

    /**
     * Authenticate the user with LDAP bind.
     *
     * @param Account  $user
     * @param TokenInterface $token
     *
     * @return true
     */
    private function bind(Account $user, TokenInterface $token)
    {
        $this->ldapManager
                ->setUsername($user->getUsername())
                ->setPassword($token->getCredentials());

        $this->ldapManager->auth();

        return true;
    }

    /**
     * Reload user with the username
     *
     * @param Account $user
     * @return Account $user
     */
    private function reloadUser(Account $user)
    {
        try {
            
            $user = $this->userProvider->refreshUser($user);
            
        } catch (UsernameNotFoundException $userNotFoundException) {
            
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials', 0, $userNotFoundException);
            }

            throw $userNotFoundException;
        }

        return $user;
    }

    /**
     * Check whether this provider supports the given token.
     *
     * @param TokenInterface $token
     *
     * @return boolean
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $this->providerKey;
    }
    
    private function manageDatabaseUser(Account $user)
    {

        if($this->userProvider instanceof ChainUserProvider) {
            
            foreach($this->userProvider->getProviders() as $userProvider) {
                if($userProvider instanceof LdapUserProvider) {
                    $userProvider->manageDatabaseUser($user);
                }
            }
            
        } elseif($this->userProvider instanceof LdapUserProvider) {
            $userProvider->manageDatabaseUser($user);
        }
    }

}
