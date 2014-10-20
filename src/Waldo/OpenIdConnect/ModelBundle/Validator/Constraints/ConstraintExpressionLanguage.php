<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintExpressionLanguage extends Constraint
{
    public $values = array();
}
