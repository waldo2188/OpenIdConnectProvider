<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Form\Type;

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
                    'label' => 'label.email_or_username',
                    'required' => false
                    ))               
                ->add('search', 'submit', array('label' => 'label.Search'))
                ->add('cancel', 'submit', array('label' => 'label.Cancel'))
            ;
    }
    
    public function getName()
    {
        return 'oic_lost_account';
    }

}
