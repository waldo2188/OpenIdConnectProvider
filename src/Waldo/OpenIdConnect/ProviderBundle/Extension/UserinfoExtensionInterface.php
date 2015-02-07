<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Extension;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;

/**
 * UserinfoExtention
 * 
 * Allow other bundle to handle specific scope and return claimed data.
 * 
 * The method "handle" is call in the class "UserinfoHelper" when a Relying Party
 * call the Userinfo Endpoint.
 */
interface UserinfoExtensionInterface
{
    /**
     * @param Token $token
     * @return array[<string>](<string>)
     */
    public function handle(Token $token);
}
