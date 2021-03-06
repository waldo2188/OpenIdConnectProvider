<?php

namespace Waldo\OpenIdConnect\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Waldo\OpenIdConnect\ModelBundle\Security\Authorization\Voter\AccountVoter;
use Waldo\OpenIdConnect\ModelBundle\Entity\Account;

/**
 * @Route("/account")
 */
class AccountController extends Controller
{

    /**
     * @Route("/", name="oicp_admin_account_index")
     */
    public function indexAction()
    {
        $this->buildAccountDatatable();

        return $this->render("WaldoOpenIdConnectAdminBundle:Account:index.html.twig");
    }

    /**
     * @Route("/list", name="oicp_admin_account_list")
     */
    public function accountListAction()
    {
        return $this->buildAccountDatatable()->execute();
    }

    /**
     * @Route("/disabled", name="oicp_admin_account_disabled", defaults={"action":"disabled"})
     * @Route("/enabled", name="oicp_admin_account_enabled", defaults={"action":"enabled"})
     * @Method("POST")
     */
    public function disabledEnabledAccountAction(Request $request, $action)
    {
        $em = $this->getDoctrine()->getManager();

        $account = $em->getRepository("WaldoOpenIdConnectModelBundle:Account")
                ->findOneById($request->request->getInt('account'));

        if ($account === null) {
            return new JsonResponse(array("message" => "Account not found"), Response::HTTP_NOT_FOUND);
        }

        if (false === $this->get('security.context')->isGranted(AccountVoter::DISABLED, $account)) {
            return new JsonResponse(array("message" => "Unauthorised access !"), Response::HTTP_FORBIDDEN);
        }

        $account->setEnabled($action === "enabled" ? true : false);

        $em->persist($account);
        $em->flush();

        return new JsonResponse(array("message" => "ok"), Response::HTTP_ACCEPTED);
    }
    
    /**
     * @Route("/profil/{account}", name="oicp_admin_account_profile", defaults={"account":null})
     */
    public function profileAction(Account $account)
    {
        return $this->render("WaldoOpenIdConnectAdminBundle:Account:profile.html.twig", array("account" => $account));
    }
    
    /**
     * @Route("/active-account-email/{account}", name="oicp_admin_account_active_account_email", defaults={"account":null})
     * @Method({"POST"})
     */
    public function activeAccountEmailAction(Account $account)
    {
        if ($account === null) {
            return new JsonResponse(array("message" => "Account not found"), Response::HTTP_NOT_FOUND);
        }
        
        $this->get('waldo_oic_p.account_actions')->sendMailRegistrationConfirmation($account);
        
        return new JsonResponse("ok");
    }
    
    /**
     * @Route("/retrieve-password-email/{account}", name="oicp_admin_account_retrieve_password_email", defaults={"account":null})
     * @Method({"POST"})
     */
    public function retrievePasswordEmailAction(Account $account)
    {
        if ($account === null) {
            return new JsonResponse(array("message" => "Account not found"), Response::HTTP_NOT_FOUND);
        }
        
        $this->get('waldo_oic_p.account_actions')->handleLostPassword($account->getUsername());
        
        return new JsonResponse("ok");
    }

    private function buildAccountDatatable()
    {
        $datatable = $this->get('datatable')->setEntity("WaldoOpenIdConnectModelBundle:Account", "Account");

        $translator = $this->get('translator');
        
        $datatable->setFields(array(
                    $translator->trans("table.Enabled") => "Account.enabled",
                    $translator->trans("table.Username") => "Account.username",
                    $translator->trans("table.Email") => "Account.email",
                    $translator->trans("table.Name") => "Account.name",
                    $translator->trans("table.Create") => "Account.createAt",
                    $translator->trans("table.Update") => "Account.updateAt",
                    $translator->trans("table.Actions") => 'Account.id',
                    "_identifier_" => 'Account.id'
                ))
                ->setRenderers(array(
                    0 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_boolean.html.twig",
                        "params" => array("ref" => "enabled")),
                    1 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_link_profile.html.twig"),
                    4 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_datetime.html.twig"),
                    5 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_datetime.html.twig"),
                    6 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_actions_account.html.twig")
                ))
                ->setOrder("Account.username", "asc")
                ->setSearch(false)
                ->setGlobalSearch(true)
        ;

        return $datatable;
    }

}
