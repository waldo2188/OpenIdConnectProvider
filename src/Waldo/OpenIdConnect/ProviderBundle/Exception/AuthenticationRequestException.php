<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Exception;

/**
 * AuthenticationRequestException
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AuthenticationRequestException extends \Exception
{
    /**
     * @var string
     */
    protected $message;
    
    /**
     * @var string
     */
    protected $error;
    
    public function __construct($message, $error)
    {
        parent::__construct($message);
        
        $this->message = $message;
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }   
}
