<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Form\Type;

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
                    'label' => 'Full mailing address',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "221B Baker Street\nLONDON NW1 6XE\nEngland",
                        )
                    ))
                ->add('streetAddress', 'text', array(
                    'label' => 'Street address',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "221B Baker Street\nLONDON NW1 6XE\nEngland",
                        )
                    ))
                ->add('locality', 'text', array(
                    'label' => 'City or locality',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "LONDON",
                        )
                    ))
                ->add('region', 'text', array(
                    'label' => 'State, province, prefecture, or region',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "LONDON",
                        )
                    ))
                ->add('postalCode', 'text', array(
                    'label' => 'Postal Code',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => "NW1 6XE",
                        )
                    ))
                ->add('country', 'country', array(
                    'label' => 'Country', 
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
            'data_class' => "\Waldo\OpenIdConnect\ProviderBundle\Entity\Address"
        ));
    }

    public function getName()
    {
        return 'oic_user_address';
    }

}
