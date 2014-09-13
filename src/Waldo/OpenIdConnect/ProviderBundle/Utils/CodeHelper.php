<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Utils;

/**
 * CodeeHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class CodeHelper
{

    public static function generateUniqueCode($entityRepository, $propertyName)
    {
        do{
            
            $code = self::generateCode();
            
        } while($entityRepository->findOneBy(array($propertyName => $code)) !== null);
            
        return $code;
    }
  
    /**
     * Generate a code/access token value.
     *
     * @return string
     */
    public static function generateCode()
    {
        $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
        $hash = bin2hex(mcrypt_create_iv($size, MCRYPT_DEV_URANDOM));
                
        for($x = mt_rand(2, 10); $x > 0; $x--) {
            $hash = hash('sha512', $hash);
        }
        
        $nonceEnc = strtolower(substr(base64_encode($hash), 0, mt_rand(42, 100)));

        return $nonceEnc;
    }

    
}
