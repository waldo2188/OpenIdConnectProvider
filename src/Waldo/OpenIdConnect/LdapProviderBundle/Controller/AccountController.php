<?php

namespace Waldo\OpenIdConnect\LdapProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/ldap/account", name="oicp_account")
 */
class AccountController extends Controller
{
    /**
     * @Route("/change-password/", name="oic_ldap_account_change_password")
     * @Security("user.getProviderName() == 'OIC-LDAP'")
     */
    public function changePasswordAction()
    {
        return $this->render('WaldoOpenIdConnectLdapProviderBundle:Account:changePassword.html.twig');
    }

    /**
     * @Route("/edit-profile/", name="oicp_ldap_account_edit_profile")
     * @Security("user.getProviderName() == 'OIC-LDAP'")
     */
    public function editProfileAction()
    {
       return $this->render('WaldoOpenIdConnectLdapProviderBundle:Account:editProfile.html.twig');
    }

}
