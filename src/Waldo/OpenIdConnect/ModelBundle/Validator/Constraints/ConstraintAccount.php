<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccount extends Constraint
{

    public $existingEmail = "This email is already in use";

    public function validatedBy()
    {
        return "constraint_account";
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
