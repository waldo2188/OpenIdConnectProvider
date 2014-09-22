<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintUserPasswordValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (strlen($value) < $constraint->passwordMinLength) {
            $this->context->addViolation(
                    $constraint->messageLessThanMin, array('%length%' => $constraint->passwordMinLength)
            );
        }
        
        if($this->countUpperCase($value) < $constraint->minUpperCase) {
            $this->context->addViolation(
                    $constraint->messageUpperCase, array('%length%' => $constraint->minUpperCase)
            );
        }
        
        if($this->countLowerCase($value) < $constraint->minLowerCase) {
            $this->context->addViolation(
                    $constraint->messageLowerCase, array('%length%' => $constraint->minLowerCase)
            );
        }
        
        if($this->countNumber($value) < $constraint->minNumber) {
            $this->context->addViolation(
                    $constraint->messageNumber, array('%length%' => $constraint->minNumber)
            );
        }
        
        echo "<pre>";
        var_dump($this->countSpecialChar($value));
        echo "</pre>";



        if($this->countSpecialChar($value) < $constraint->minSpecialChar) {
            $this->context->addViolation(
                    $constraint->messageSpecialChar, array('%length%' => $constraint->minSpecialChar)
            );
        }
    }

    private function countUpperCase($value)
    {
        return strlen(preg_replace('![^A-Z]+!', '', $value));
    }

    private function countLowerCase($value)
    {
        return strlen(preg_replace('![^a-z]+!', '', $value));
    }
    
    private function countNumber($value)
    {
        return strlen(preg_replace('![^0-9]+!', '', $value));
    }
    
    private function countSpecialChar($value)
    {
        return strlen(preg_replace('![0-9a-zA-Z]+!', '', $value));
    }

}
