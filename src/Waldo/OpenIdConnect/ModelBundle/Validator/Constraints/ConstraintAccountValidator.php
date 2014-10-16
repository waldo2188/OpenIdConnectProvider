<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Validator\UniqueUsernameValidatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintAccountValidator extends ConstraintValidator
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $isUsernameUnique = $this->isUsernameUnique($value->getUsername(), $value);

        if ($isUsernameUnique === false) {

            $this->context->addViolationAt("username", $constraint->existingUsername);
        }

        $email = $this->em->getRepository(get_class($value))
                ->findOneByEmail($value->getEmail(), $value);

        if ($email !== null) {

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
