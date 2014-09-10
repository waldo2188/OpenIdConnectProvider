<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Entity;

/**
 * Address 
 * 
 * @see http://openid.net/specs/openid-connect-core-1_0.html#AddressClaim
 */
class Address
{
    
    /**
     * Full mailing address, formatted for display or 
     * use on a mailing label.This field MAY contain multiple lines, separated 
     * by newlines. Newlines can be represented either as a carriage return/line
     * feed pair ("\r\n") or as a single line feed character ("\n"). 
     * @var string
     */
    public $formatted;
    
    /**
     * Full street address component, which 
     * MAY include house number, street name, Post Office Box, and multi-line
     * extended street address information. This field MAY contain multiple lines,
     * separated by newlines. Newlines can be represented either as a carriage
     * return/line feed pair ("\r\n") or as a single line feed character ("\n"). 
     * @var string 
     */
    public $streetAddress;
    
    /**
     * City or locality component. 
     * @var string 
     */
    public $locality;
    
    /**
     * State, province, prefecture, or region component. 
     * @var string 
     */
    public $region;
    
    /**
     * Zip code or postal code component. 
     * @var string 
     */
    public $postalCode;
    
    /**
     * Country name component. 
     * @var string 
     */
    public $country;
    
    /**
     * Address
     * 
     * @param type $formatted
     * @param type $streetAddress
     * @param type $locality
     * @param type $region
     * @param type $postalCode
     * @param type $country
     */
    public function __construct($formatted = null, $streetAddress = null,
            $locality = null, $region = null, $postalCode = null, $country = null)
    {
        $this->formatted = $formatted;
        $this->streetAddress = $streetAddress;
        $this->locality = $locality;
        $this->region = $region;
        $this->postalCode = $postalCode;
        $this->country = $country;
    }
    
}
