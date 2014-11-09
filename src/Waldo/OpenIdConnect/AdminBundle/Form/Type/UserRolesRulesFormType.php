<?php

namespace Waldo\OpenIdConnect\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Waldo\OpenIdConnect\AdminBundle\Form\DataTransformer\ArrayToStringTransformer;

/**
 * ClientFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UserRolesRulesFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {          
        $builder
                ->add('expression', 'textarea', array(
                    'label' => 'label.Filter',
                    'required' => true
                    ))
                ->add('roles', 'text', array(
                    'label' => 'label.Roles',
                    'required' => true
                    ))
                ->add('enabled', 'checkbox', array(
                    'label' => 'label.Enabled',
                    'required' => false
                    ))
            ;
        
        $builder->get('roles')->addModelTransformer(new ArrayToStringTransformer());
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules"
        ));
    }
    
    public function getName()
    {
        return 'oic_user_roles';
    }

}
