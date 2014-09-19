<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ContainsUserPassword extends Constraint
{
    public $messageLessThanMin = "Your password must have at least %length% character";
    public $messageNoUpper = "Your password must have at least %length% uppercase character";
    public $messageNoLower = "Your password must have at least %length% lowercase character";
    public $messageNoSpecial = "Your password must have at least %length% special character";
}
