<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/authorize", name="oicp_authorization")
 */
class AuthorizationEndpointController extends Controller
{
    
    /**
     * @Route("/", name="oicp_authorization_endpoint")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $equest
     */
   public function authorizationEndpointAction(Request $request) 
   {
       return $this->get('waldo_oic_p.endpoint.authorization')->handleRequest($request);
   }
           
}
