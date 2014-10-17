<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\PasswordFormType;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\AccountFormType;

/**
 * @Route("/account", name="oicp_account")
 */
class AccountController extends Controller
{

    /**
     * 
     * @Route("/", name="oicp_account_index")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Allow the enduser to change his password
     * 
     * @Route("/change-password/", name="oicp_account_change_password")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function changePasswordAction(Request $request)
    {

        $account = clone $this->get('security.context')->getToken()->getUser();

        $form = $this->createForm(new PasswordFormType(), $account, array('hasOldpassword' => true));

        $form->handleRequest($request);

        if ($request->isMethod("POST")) {
            if ($form->isValid()) {
                $this->get("waldo_oic_enduser.registration")->encodePassword($account);

                $em = $this->getDoctrine()->getManager();
                $em->merge($account);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your password has been changed');

                return $this->redirect($this->generateUrl("oicp_account_change_password"));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Allow the enduser to edit his profile
     * 
     * @Route("/edit-profile/", name="oicp_account_edit_profile")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function editProfileAction(Request $request)
    {
        $account = $this->get('security.context')->getToken()->getUser();

        $form = $this->createForm(new AccountFormType(), $account, array(
            'hasPasswordField' => false,
            'validation_groups' => array('edit'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $warning = $this->get('waldo_oic_p.account_actions')->handleEditProfile($account);

            if ($warning == 'email_has_changed') {
                $this->get('session')->getFlashBag()->add('warning', 'Your new email will be validated once you have used the validation link that was sent to the new email address you entered. Without this action your previous email address will be kept.');
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($account);
            $em->flush();


            $this->get('session')->getFlashBag()->add('notice', 'Your profile has been changed');

            return $this->redirect($this->generateUrl("oicp_account_edit_profile"));
        }

        return array('form' => $form->createView());
    }

    /**
     * Show all applications who have access to enduser's data
     * 
     * @Route("/application-access-liste/", name="oicp_account_application_access_list")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template
     */
    public function applicationsAccessListAction(Request $request)
    {
        /* @var $account \Waldo\OpenIdConnect\ModelBundle\Entity\Account */
        $account = $this->get('security.context')->getToken()->getUser();

        return array('tokenList' => $account->getTokenList());
    }

}
