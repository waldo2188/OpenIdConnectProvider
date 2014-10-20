<?php

namespace Waldo\OpenIdConnect\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ConstraintExpressionLanguageValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $expressionValue = array();

        if(class_exists($constraint->values)) {
            $expressionValue = (array) new $constraint->values();
        }
        
        $language = new ExpressionLanguage();

        try {
            $language->evaluate($value, $expressionValue);
        }  catch (SyntaxError $e) {
            $this->context->addViolation($e->getMessage());
        }
    }
}
