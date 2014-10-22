<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * AddressFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class AddressFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('formatted', 'textarea', array(
                    'label' => 'label.Full_mailing_address',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "221B Baker Street\nLONDON NW1 6XE\nEngland",
                        )
                    ))
                ->add('streetAddress', 'text', array(
                    'label' => 'label.Full_street_address',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "221B Baker Street\nLONDON NW1 6XE\nEngland",
                        )
                    ))
                ->add('locality', 'text', array(
                    'label' => 'label.City_or_locality',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "LONDON",
                        )
                    ))
                ->add('region', 'text', array(
                    'label' => 'label.State_province_prefecture_or_region',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "LONDON",
                        )
                    ))
                ->add('postalCode', 'text', array(
                    'label' => 'label.Zip_code_or_postal_code',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "NW1 6XE",
                        )
                    ))
                ->add('country', 'country', array(
                    'label' => 'label.Country_name', 
                    'empty_value' => 'Choose an option',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "England",
                        )
                    ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ModelBundle\Entity\Address"
        ));
    }

    public function getName()
    {
        return 'oic_user_address';
    }

}
