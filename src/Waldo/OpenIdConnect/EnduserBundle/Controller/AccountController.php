<?php

namespace Waldo\OpenIdConnect\EnduserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\PasswordFormType;
use Waldo\OpenIdConnect\EnduserBundle\Form\Type\AccountFormType;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;

/**
 * @Route("/account", name="oicp_account")
 */
class AccountController extends Controller
{

    /**
     * @Route("/", name="oicp_account_index")
     */
    public function indexAction()
    {
        return $this->render("WaldoOpenIdConnectEnduserBundle:Account:index.html.twig");
    }

    /**
     * Allow the enduser to change his password
     * 
     * @Route("/change-password/", name="oicp_account_change_password")
     * @Security("user.getProviderName() == 'self'")
     * @param \Symfony\Component\HttpFoundation\Request $request
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

                $this->get('session')->getFlashBag()->add('notice', 'label.your_password_has_been_changed');

                return $this->redirect($this->generateUrl("oicp_account_change_password"));
            }
        }

        return $this->render("WaldoOpenIdConnectEnduserBundle:Account:changePassword.html.twig", array(
                    'form' => $form->createView()
        ));
    }

    /**
     * Allow the enduser to edit his profile
     * 
     * @Route("/edit-profile/", name="oicp_account_edit_profile")
     * @param \Symfony\Component\HttpFoundation\Request $request
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
                $this->get('session')->getFlashBag()->add('warning', 'label.email_validation_instruction');
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($account);
            $em->flush();


            $this->get('session')->getFlashBag()->add('notice', 'label.your_profile_has_been_changed');

            return $this->redirect($this->generateUrl("oicp_account_edit_profile"));
        }

        return $this->render("WaldoOpenIdConnectEnduserBundle:Account:editProfile.html.twig", array(
                    'form' => $form->createView())
        );
    }

    /**
     * Show all applications who have access to enduser's data
     * 
     * @Route("/application-access-liste/", name="oicp_account_application_access_list")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function applicationsAccessListAction(Request $request)
    {
        /* @var $account \Waldo\OpenIdConnect\ModelBundle\Entity\Account */
        $account = $this->get('security.context')->getToken()->getUser();

        return $this->render("WaldoOpenIdConnectEnduserBundle:Account:applicationsAccessList.html.twig", array(
                    'tokenList' => $account->getTokenList())
        );
    }

    /**
     * @Route("/revoke-authorization/{account}-{client}",
     *  name="oicp_account_revoke_authorization",
     *  defaults={"account":null, "client":null})
     * @Method({"POST"})
     */
    public function revokeAutorizationAction(Account $account, Client $client)
    {
        try {
            $this->get('waldo_oic_p.revoke_authorization')->revoke($account, $client);
        } catch (AccessDeniedException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse("ok");
    }

}
