<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Services;

use Waldo\OpenIdConnect\ProviderBundle\Entity\Address;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Account;
use Waldo\OpenIdConnect\ProviderBundle\Entity\Request\Authentication;
use Waldo\OpenIdConnect\ProviderBundle\Services\ScopeUtils;

/**
 * ScopeUtilsTest
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ScopeUtilsTest extends \PHPUnit_Framework_TestCase
{
    
    public function testUserInfoForScope()
    {
        $address = new Address("formatted", "streetAddress", "locality", "region", "postalCode", "country");
        
        $account = new Account();
        $account->setAddress($address)
                ->setBirthdate(new \DateTime("1961-02-14"))
                ->setEmail("email")
                ->setEmailVerified(true)
                ->setFamilyName("familyName")
                ->setGender("gender")
                ->setGivenName("givenName")
                ->setLocale("locale")
                ->setMiddleName("middleName")
                ->setName("name")
                ->setNickname("nickname")
                ->setPhoneNumber("phoneNumber")
                ->setPhoneNumberVerified(false)
                ->setPicture("picture")
                ->setPreferedUsername("preferedUsername")
                ->setProfile("profile")
                ->setUsername("username")
                ->setWebsite("website")
                ->setZoneInfo("zoneInfo");
        
        $authentication = new Authentication();
        $authentication->setScope(
                array(
                    'openid',
                    'dumb',
                    Account::SCOPE_PROFILE,
                    Account::SCOPE_EMAIL,
                    Account::SCOPE_PHONE,
                    Account::SCOPE_ADDRESS,
                )
                );
        
        $scopeUtils = new ScopeUtils();
        
        $result = $scopeUtils->getUserinfoForScopes($account, $authentication);

        $expected = array(
            "name" => "name",
            "givenName" => "givenName",
            "familyName" => "familyName",
            "middleName" => "middleName",
            "nickname" => "nickname",
            "preferedUsername" => "preferedUsername",
            "profile" => "profile",
            "picture" => "picture",
            "website" => "website",
            "gender" => "gender",
            "birthdate" => new \DateTime("1961-02-14"),
            "zoneInfo" => "zoneInfo",
            "locale" => "locale",
            "email" => "email",
            "emailVerified" => true,
            "phoneNumber" => "phoneNumber",
            "phoneNumberVerified" => false,
            "address" => $address,
        );
        
        $this->assertEquals($expected, $result);
    }

}
