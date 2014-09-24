<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * LostAccountFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class LostAccountFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', 'text', array(
                    'label' => 'Email, Username',
                    'required' => false
                    ))               
                ->add('search', 'submit')
                ->add('cancel', 'submit')
            ;
    }
    
    public function getName()
    {
        return 'oic_lost_account';
    }

}
