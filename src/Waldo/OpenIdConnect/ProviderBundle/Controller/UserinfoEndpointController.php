<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserinfoEndpointController extends Controller
{

    /**
     * @Route("/userinfo", name="oicp_userinfo_endpoint")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function tokenEndpointAction(Request $request)
    {        
        return $this->get('waldo_oic_p.endpoint.userinfo')->handle($request);
    }

}
