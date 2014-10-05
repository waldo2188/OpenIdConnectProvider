<?php

namespace Waldo\OpenIdConnect\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Description of ArrayToSringTransformer
 *
 */
class ArrayToStringTransformer implements DataTransformerInterface
{
    public function reverseTransform($value)
    {
        $valueArray = explode(',', $value);
        
        foreach($valueArray as $key => $value) {
            $valueArray[$key] = trim($value);
        }
        
        return $valueArray;
    }

    public function transform($value)
    {
        if (null === $value) {
            return "";
        }

        return implode(', ', $value);
    }

}
