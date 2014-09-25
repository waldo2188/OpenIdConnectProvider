<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintUserPassword extends Constraint
{
    public $passwordMinLength = 8;
    public $minUpperCase = 1;
    public $minLowerCase = 1;
    public $minNumber = 1;
    public $minSpecialChar = 1;
    
    public $messageLessThanMin = "Your password must have at least %length% character";
    public $messageUpperCase = "Your password must have at least %length% uppercase character";
    public $messageLowerCase = "Your password must have at least %length% lowercase character";
    public $messageNumber = "Your password must have at least %length% number";
    public $messageSpecialChar = "Your password must have at least %length% special character";
}
