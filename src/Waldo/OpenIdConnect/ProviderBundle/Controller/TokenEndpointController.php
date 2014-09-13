<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class TokenEndpointController extends Controller
{

    /**
     * @Route("/token", name="oicp_token_endpoint")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $equest
     */
    public function tokenEndpointAction(Request $request)
    {
        //TODO add other Client Authentication's method http://openid.net/specs/openid-connect-core-1_0.html#ClientAuthentication
        
        return $this->get('waldo_oic_p.endpoint.token')->handle($request);
    }

}
