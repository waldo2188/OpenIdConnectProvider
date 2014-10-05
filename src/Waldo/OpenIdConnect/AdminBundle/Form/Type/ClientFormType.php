<?php

namespace Waldo\OpenIdConnect\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\AdminBundle\Form\Type\UserRolesRulesFormType;

/**
 * ClientFormType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ClientFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sigalgs = array(
            'RS256' => 'RS256',
            'RS384' => 'RS384',
            'RS512' => 'RS512',
            'HS256' => 'HS256',
            'HS384' => 'HS384',
            'HS512' => 'HS512'
            );
        $encryptionPlaintextAlgs = array(
            'A128GCM' => 'A128GCM', 
            'A256GCM' => 'A256GCM', 
            'A128CBC-HS256' => 'A128CBC-HS256', 
            'A256CBC-HS512' => 'A256CBC-HS512'
            );
        // content-encryption key (CEK)
        $encCEKAlgs = array('RSA1_5' => 'RSA1_5', 'RSA-OAEP' => 'RSA-OAEP');
        
        $builder
                ->add('clientName', 'text', array(
                    'label' => 'Application name',
                    'required' => true
                    ))
                
                ->add('clientUri', 'url', array(
                    'label' => 'Homepage URI',
                    'required' => true
                    ))
                
                ->add('contacts', 'bootstrap_collection', array(
                    'label' => 'Contacts',
                    'type'   => 'text',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'add_button_text'    => 'Add',
                    'delete_button_text' => 'Delete',
                    'required' => false
                    ))
                
                ->add('applicationType', 'choice', array(
                    'label' => 'Application type',
                    'choices' => array('web' => 'web', 'native' => 'native'),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                    ))
                
                ->add('logoUri', 'url', array(
                    'label' => 'Application logo URI',
                    'required' => false
                    ))
                
                ->add('tosUri', 'url', array(
                    'label' => 'Terms of service URI',
                    'required' => false
                    ))
                
                ->add('policyUri', 'url', array(
                    'label' => 'Policy URI',
                    'required' => false
                    ))
                
                ->add('redirectUris', 'bootstrap_collection', array(
                    'label' => 'Redirect URIs',
                    'type' => 'url',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'add_button_text'    => 'Add',
                    'delete_button_text' => 'Delete',
                    'required' => true
                    ))
                
                ->add('postLogoutRedirectUri', 'url', array(
                    'label' => 'Post logout redirect URIs',
                    'required' => false
                    ))
                
                ->add('tokenEndpointAuthMethod', 'choice', array(
                    'label' => 'Token endpoint authentication method',
                    'choices' => array(
                        'client_secret_basic' => 'client_secret_basic',
                        'client_secret_post' => 'client_secret_post',
                        'client_secret_jwt' => 'client_secret_jwt',
                        'private_key_jwt' => 'private_key_jwt'
                    ),
                    'required' => true
                    ))
                
                ->add('tokenEndpointAuthSigningAlg', 'choice', array(
                    'label' => 'Token endpoint authentication signing algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('jwksUri', 'url', array(
                    'label' => 'Jwks URI',
                    'required' => false
                    ))
                
                ->add('jwkEncryptionUri', 'url', array(
                    'label' => 'Jwks encryption URI',
                    'required' => false
                    ))
                
                ->add('x509Uri', 'url', array(
                    'label' => 'X509 certificat URI',
                    'required' => false
                    ))
                
                ->add('x509EncryptionUri', 'url', array(
                    'label' => 'X509 encryption certificat URI',
                    'required' => false
                    ))
                
                ->add('requestObjectSigningAlg', 'choice', array(
                    'label' => 'Request object signing algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                ->add('requestObjectEncryptionAlg', 'choice', array(
                    'label' => 'Request object encryption algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('requestObjectEncryptionEnc', 'choice', array(
                    'label' => 'Request object encryption encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoSignedResponseAlg', 'choice', array(
                    'label' => 'Userinfo object signing algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoEncryptedResponseAlg', 'choice', array(
                    'label' => 'Userinfo encrypted response algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoEncryptedResponseEnc', 'choice', array(
                    'label' => 'Userinfo encrypted response encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenSignedResponseAlg', 'choice', array(
                    'label' => 'ID token signined response algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenEncryptedResponseAlg', 'choice', array(
                    'label' => 'ID token encrypted response algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenEncryptedResponseEnc', 'choice', array(
                    'label' => 'ID token encrypted response encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('defaultMaxAge', 'integer', array(
                    'label' => 'Default max age',
                    'required' => false,
                    'attr' => array(
                        'help_text' => 'The value is time in second'
                        )
                    ))
                
                ->add('requireAuthTime', 'choice', array(
                    'label' => 'Require authentication time',
                    'choices'   => array(true => 'Yes', false => 'No'),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                    ))
                
                ->add('scope', 'choice', array(
                    'label' => 'Scopes',
                    'choices'   => array(
                        Account::SCOPE_PROFILE => Account::SCOPE_PROFILE,
                        Account::SCOPE_EMAIL => Account::SCOPE_EMAIL,
                        Account::SCOPE_ADDRESS => Account::SCOPE_ADDRESS,
                        Account::SCOPE_PHONE => Account::SCOPE_PHONE
                        ),
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                    ))
                
                ->add('userRolesRulesList', 'bootstrap_collection', array(
                        'type' => new UserRolesRulesFormType(),
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'add_button_text'    => 'Add',
                        'delete_button_text' => 'Delete'
                    ))
                
                
                ->add('save', 'submit')
            ;
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "\Waldo\OpenIdConnect\ModelBundle\Entity\Client",
            'cascade_validation' => true,
        ));
    }
    
    public function getName()
    {
        return 'oic_client';
    }

}
