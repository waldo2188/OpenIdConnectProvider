<?php

namespace Waldo\OpenIdConnect\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="oicp_admin_index")
     */
    public function indexAction()
    {
        return $this->render("WaldoOpenIdConnectAdminBundle:Dashboard:index.html.twig");
    }
    
}
