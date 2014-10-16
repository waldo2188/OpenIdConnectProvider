<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Validator\UniqueUsernameValidatorInterface;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccountValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $isUsernameUnique = $this->isUsernameUnique($value->getEmail(), $value);

        if ($isUsernameUnique === false) {

            $this->context->addViolationAt("email", $constraint->existingEmail);
        }

    }

    public function addUniqueUsernameChecker(UniqueUsernameValidatorInterface $uniqueUsername)
    {
        $this->uniqueUsernameChecker[get_class($uniqueUsername)] = $uniqueUsername;
    }

    private function isUsernameUnique($username, Account $account)
    {
        $isUnique = true;

        /* @var $checker UniqueUsernameValidatorInterface */
        foreach ($this->uniqueUsernameChecker as $checker) {
            $isUnique &=!$checker->exist($username, $account);                    
        }
        return (bool) $isUnique;
    }

}
