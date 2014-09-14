<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Utils;

/**
 * CodeeHelper
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class CodeHelper
{

    /**
     * 
     * @param \Doctrine\ORM\EntityRepository $entityRepository
     * @param string $propertyName
     * @param boolean $isBearer if the code is for an access_token ou refresh_token
     * @return string
     */
    public static function generateUniqueCode($entityRepository, $propertyName, $isBearer = false)
    {
        do {
            
            $code = self::generateCode($isBearer);
            
        } while($entityRepository->findOneBy(array($propertyName => $code)) !== null);
            
        return $code;
    }
  
    /**
     * Generate a code/access token value.
     * 
     * @param boolean $isBearer if the code is for an access_token ou refresh_token
     * @return string
     */
    public static function generateCode($isBearer = false)
    {
        $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
        $hash = bin2hex(mcrypt_create_iv($size, MCRYPT_DEV_URANDOM));
                
        for($x = mt_rand(2, 10); $x > 0; $x--) {
            $hash = hash('sha512', $hash);
        }
       
        $hash = base64_encode($hash);
        
        $replaceBy = array('-','_',',');
        
        if($isBearer) {
            $replaceBy = array_merge($replaceBy, array('.', '+', '~', '/'));
        }
        shuffle($replaceBy);
        
        $hash = str_replace(array('+','/','='), $replaceBy, $hash);
        
        for($x = mt_rand(2, 15); $x > 0; $x--) {
            $pos = mt_rand(1, strlen($hash) - 1);
            $hash[$pos] = $replaceBy[array_rand($replaceBy, 1)];
        }
        
        $nonceEnc = strtolower(substr($hash, 0, mt_rand(42, 100)));

        return $nonceEnc;
    }

    
}
