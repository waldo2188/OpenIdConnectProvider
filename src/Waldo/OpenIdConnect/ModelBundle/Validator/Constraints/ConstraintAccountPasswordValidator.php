<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccountPasswordValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if ($value->getPassword() === $value->getUsername()) {

            $this->context->addViolationAt("password", $constraint->usernameSameAsPassword);
        } elseif (stripos($value->getPassword(), $value->getUsername()) !== false) {

            $this->context->addViolationAt("password", $constraint->usernameInPassword);
        }
    }

}
