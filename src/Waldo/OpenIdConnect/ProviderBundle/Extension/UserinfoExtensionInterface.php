<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Extension;

use Waldo\OpenIdConnect\ModelBundle\Entity\Token;

/**
 * Description of UserinfoExtention
 */
interface UserinfoExtensionInterface
{
    /**
     * @param Token $token
     * @return array
     */
    public function handle(Token $token);
}
