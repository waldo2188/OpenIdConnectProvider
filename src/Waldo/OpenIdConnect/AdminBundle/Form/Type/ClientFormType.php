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
                    'label' => 'label.application_name',
                    'required' => true
                    ))
                
                ->add('clientUri', 'url', array(
                    'label' => 'label.homepage_URI',
                    'required' => true
                    ))
                
                ->add('contacts', 'bootstrap_collection', array(
                    'label' => 'label.Contacts',
                    'type'   => 'text',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'add_button_text'    => 'label.add',
                    'delete_button_text' => 'label.delete',
                    'required' => false
                    ))
                
                ->add('applicationType', 'choice', array(
                    'label' => 'label.application_type',
                    'choices' => array('web' => 'label.web', 'native' => 'label.native'),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                    ))
                
                ->add('logoUri', 'url', array(
                    'label' => 'label.application_logo_URI',
                    'required' => false
                    ))
                
                ->add('tosUri', 'url', array(
                    'label' => 'label.terms_of_service_URI',
                    'required' => false
                    ))
                
                ->add('policyUri', 'url', array(
                    'label' => 'label.policy_URI',
                    'required' => false
                    ))
                
                ->add('redirectUris', 'bootstrap_collection', array(
                    'label' => 'label.redirect_URIs',
                    'type' => 'url',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'add_button_text'    => 'label.add',
                    'delete_button_text' => 'label.delete',
                    'required' => true
                    ))
                
                ->add('postLogoutRedirectUri', 'url', array(
                    'label' => 'label.post_logout_redirect_URIs',
                    'required' => false
                    ))
                
                ->add('tokenEndpointAuthMethod', 'choice', array(
                    'label' => 'label.token_endpoint_authentication_method',
                    'choices' => array(
                        'client_secret_basic' => 'client_secret_basic',
                        'client_secret_post' => 'client_secret_post',
                        'client_secret_jwt' => 'client_secret_jwt',
                        'private_key_jwt' => 'private_key_jwt'
                    ),
                    'required' => true
                    ))
                
                ->add('tokenEndpointAuthSigningAlg', 'choice', array(
                    'label' => 'label.token_endpoint_authentication_signing_algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('jwksUri', 'url', array(
                    'label' => 'label.jwks_URI',
                    'required' => false
                    ))
                
                ->add('jwkEncryptionUri', 'url', array(
                    'label' => 'label.jwks_encryption_URI',
                    'required' => false
                    ))
                
                ->add('x509Uri', 'url', array(
                    'label' => 'label.X509_certificat_URI',
                    'required' => false
                    ))
                
                ->add('x509EncryptionUri', 'url', array(
                    'label' => 'label.X509_encryption_certificat_URI',
                    'required' => false
                    ))
                
                ->add('requestObjectSigningAlg', 'choice', array(
                    'label' => 'label.request_object_signing_algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                ->add('requestObjectEncryptionAlg', 'choice', array(
                    'label' => 'label.request_object_encryption_algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('requestObjectEncryptionEnc', 'choice', array(
                    'label' => 'label.request_object_encryption_encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoSignedResponseAlg', 'choice', array(
                    'label' => 'label.userinfo_object_signing_algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoEncryptedResponseAlg', 'choice', array(
                    'label' => 'label.userinfo_encrypted_response_algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('userinfoEncryptedResponseEnc', 'choice', array(
                    'label' => 'label.userinfo_encrypted_response_encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenSignedResponseAlg', 'choice', array(
                    'label' => 'label.id_token_signined_response_algorithm',
                    'choices' => $sigalgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenEncryptedResponseAlg', 'choice', array(
                    'label' => 'label.id_token_encrypted_response_algorithm',
                    'choices' => $encCEKAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('idTokenEncryptedResponseEnc', 'choice', array(
                    'label' => 'label.id_token_encrypted_response_encoder',
                    'choices' => $encryptionPlaintextAlgs,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                    ))
                
                ->add('defaultMaxAge', 'integer', array(
                    'label' => 'label.default_max_age',
                    'required' => false,
                    'attr' => array(
                        'help_text' => 'The value is time in second'
                        )
                    ))
                
                ->add('requireAuthTime', 'choice', array(
                    'label' => 'label.require_authentication_time',
                    'choices'   => array(true => 'label.Yes', false => 'label.No'),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                    ))
                
                ->add('scope', 'choice', array(
                    'label' => 'label.scopes',
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
                        'label' => 'label.List_roles_filter',
                        'type' => new UserRolesRulesFormType(),
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'add_button_text'    => 'label.add',
                        'delete_button_text' => 'label.delete'
                    ))
                
                
                ->add('save', 'submit', array('label' => 'label.Save'))
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
