<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
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

        $username = $this->em->getRepository(get_class($value))
                ->findOneByUsername($value->getUsername(), $value);

        if ($username !== null) {

            $this->context->addViolationAt("username", $constraint->existingUsername);
        }

        $email = $this->em->getRepository(get_class($value))
                ->findOneByEmail($value->getEmail(), $value);

        if ($email !== null) {

            $this->context->addViolationAt("email", $constraint->existingEmail);
        }
    }

}
