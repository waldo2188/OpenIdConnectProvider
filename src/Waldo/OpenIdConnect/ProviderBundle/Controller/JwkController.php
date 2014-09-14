<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Controller;

use Waldo\OpenIdConnect\ProviderBundle\Form\Type\ScopeApprovalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/jwk", name="oicp_jwp")
 */
class JwkController extends Controller
{
    /**
     * @Route("/oicp.jwk", name="oicp_jwp_file")
     */
    public function getJwkAction(Request $request)
    {
        
        return $this->get('waldo_oic_p.provider.jwk')->getJwkResponse();
        
    }
    
    

}
