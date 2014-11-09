<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * PasswordFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class PasswordFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'label.the_password_fields_must_match',
                    'first_options' => array('label' => 'label.create_a_password'),
                    'second_options' => array('label' => 'label.confirm_your_Password'),
                    'required' => true
                ))
                
                ->add('save', 'submit', array('label' => "label.Save"))
            ;
        
        if($options['hasOldpassword']) {
            $builder
                    ->add('currentpassword', 'password', array(
                        'label'=>'label.current_password',
                        'mapped' => false,
                        'constraints' => new UserPassword(),
                        'validation_groups' => array('Default')
                        ))
                    ;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ModelBundle\Entity\Account",
            'validation_groups' => array('change_password'),
            'hasOldpassword' => false
        ));
    }
    
    public function getName()
    {
        return 'oic_password';
    }

}
