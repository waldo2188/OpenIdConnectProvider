<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\AddressFormType;

/**
 * RegistrationFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AccountFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', 'email', array(
                    'label' => 'Email',
                    'required' => true
                    ))
                
                ->add('save', 'submit')
            ;
        
        if($options['hasProfileField'] === true) {
            $builder
                    ->add('name', 'text', array(
                    'label' => 'Name',
                    'required' => false
                    ))
                ->add('givenName', 'text', array(
                    'label' => 'Given name',
                    'required' => false
                    ))
                ->add('familyName', 'text', array(
                    'label' => 'Family name',
                    'required' => false
                    ))
                ->add('middleName', 'text', array(
                    'label' => 'Middle name', 
                    'required' => false
                    ))
                ->add('nickname', 'text', array(
                    'label' => 'Nickname', 
                    'required' => false
                    ))
                ->add('preferedUsername', 'text', array(
                    'label' => 'Prefered username',
                    'required' => false
                    ))
                ->add('picture', 'url', array(
                    'label' => 'Picture', 
                    'required' => false
                    ))
                ->add('website', 'url', array(
                    'label' => 'Website', 
                    'required' => false
                    ))
                ->add('gender', 'choice', array(
                    'label' => 'Gender', 
                    'empty_value' => 'Choose an option',
                    'choices' => array(
                        'Female',
                        'Male',
                        'Other...'
                    ),
                    'required' => false
                    ))
                ->add('birthdate', 'date', array(
                    'label' => 'Birthdate',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                    ))
                ->add('zoneInfo', 'timezone', array(
                    'label' => 'Time zone', 
                    'empty_value' => 'Choose an option',
                    'required' => false
                    ))
                ->add('locale', 'locale', array(
                    'label' => 'Local', 
                    'empty_value' => 'Choose an option',
                    'required' => false
                    ))
                ->add('phoneNumber', 'text', array(
                    'label' => 'Phone number', 
                    'required' => false
                    ))
                ->add('address', new AddressFormType(), array(
                    'label' => 'Address', 
                    'required' => false
                    ))
                ;
        }
        
        if($options['hasUsernameField'] === true) {
            $builder
                    ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'first_options' => array('label' => 'Create a password'),
                    'second_options' => array('label' => 'Confirm your Password'),
                    'required' => true
                ))
                    ;
        }
        
        if($options['hasPasswordField'] === true) {
            $builder
                    ->add('username', 'text', array(
                    'label' => 'Username',
                    'required' => true
                    ))
                    ;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ModelBundle\Entity\Account",
            'validation_groups' => array('registration'),
            'hasUsernameField' => true,
            'hasPasswordField' => true,
            'hasProfileField' => true
        ));
    }
    
    public function getName()
    {
        return 'oic_user_registration';
    }

}
