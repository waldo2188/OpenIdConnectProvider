<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * ScopeApprovalType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ScopeApprovalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('accept', 'button', array('label' => 'Accept'))
                ->add('cancel', 'button', array('label' => 'Cancel'))
            ;
    }

    public function getName()
    {
        return 'scope_approval';
    }

}
