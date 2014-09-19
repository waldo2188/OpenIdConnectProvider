<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Waldo\OpenIdConnect\ProviderBundle\Form\Type\AddressFormType;

/**
 * RegistrationFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', 'text', array(
                    'label' => 'Username',
                    'required' => true
                    ))
                ->add('email', 'email', array(
                    'label' => 'Email',
                    'required' => false
                    ))
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    'required' => true
                ))
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
                ->add('picture', 'text', array(
                    'label' => 'Picture', 
                    'required' => false
                    ))
                ->add('website', 'text', array(
                    'label' => 'Website', 
                    'required' => false
                    ))
                ->add('gender', 'text', array(
                    'label' => 'Gender', 
                    'required' => false
                    ))
                ->add('birthdate', 'date', array(
                    'label' => 'Birthdate', 
                    'required' => false
                    ))
                ->add('zoneInfo', 'text', array(
                    'label' => 'Time zone (France/Paris)', 
                    'required' => false
                    ))
                ->add('locale', 'text', array(
                    'label' => 'Local (en, fr, fr_ca)', 
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
                
                ->add('save', 'submit')
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ProviderBundle\Entity\Account"
        ));
    }
    
    public function getName()
    {
        return 'oic_user_registration';
    }

}
