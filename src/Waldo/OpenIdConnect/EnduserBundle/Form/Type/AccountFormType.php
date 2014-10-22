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
                ->add('name', 'text', array(
                    'label' => 'label.Name',
                    'required' => false
                    ))
                ->add('givenName', 'text', array(
                    'label' => 'label.First_name',
                    'required' => false
                    ))
                
                ->add('email', 'email', array(
                    'label' => 'label.Email',
                    'required' => true
                    ))
                
                ->add('save', 'submit')
            ;
        
        if($options['hasProfileField'] === true) {
            $builder
                ->add('familyName', 'text', array(
                    'label' => 'label.Family_name',
                    'required' => false
                    ))
                ->add('middleName', 'text', array(
                    'label' => 'label.Middle_name', 
                    'required' => false
                    ))
                ->add('nickname', 'text', array(
                    'label' => 'label.Nickname', 
                    'required' => false
                    ))
                ->add('preferedUsername', 'text', array(
                    'label' => 'label.Prefered_username',
                    'required' => false
                    ))
                ->add('picture', 'text', array(
                    'label' => 'label.Picture', 
                    'required' => false
                    ))
                ->add('website', 'url', array(
                    'label' => 'label.Web_site', 
                    'required' => false
                    ))
                ->add('gender', 'choice', array(
                    'label' => 'label.Gender', 
                    'empty_value' => 'label.choose_an_option',
                    'choices' => array(
                        'label.Female',
                        'label.Male',
                        'label.Other...'
                    ),
                    'required' => false
                    ))
                ->add('birthdate', 'date', array(
                    'label' => 'label.Birthdate',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                    ))
                ->add('zoneInfo', 'timezone', array(
                    'label' => 'label.Time_zone', 
                    'empty_value' => 'label.choose_an_option',
                    'required' => false
                    ))
                ->add('locale', 'locale', array(
                    'label' => 'label.Locale', 
                    'empty_value' => 'label.choose_an_option',
                    'required' => false
                    ))
                ->add('phoneNumber', 'text', array(
                    'label' => 'label.Telephone_number', 
                    'required' => false
                    ))
                ->add('address', new AddressFormType(), array(
                    'label' => 'label.Address', 
                    'required' => false
                    ))
                ;
        }
        
        if($options['hasPasswordField'] === true) {
            $builder
                    ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'label.the_password_fields_must_match',
                    'first_options' => array('label' => 'label.create_a_password'),
                    'second_options' => array('label' => 'label.confirm_your_Password'),
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
            'hasPasswordField' => true,
            'hasProfileField' => true
        ));
    }
    
    public function getName()
    {
        return 'oic_user_registration';
    }

}
