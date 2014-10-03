<?php

namespace Waldo\OpenIdConnect\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Waldo\OpenIdConnect\ModelBundle\Security\Authorization\Voter\AccountVoter;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;

/**
 * @Route("/client")
 */
class ClientController extends Controller
{

    /**
     * @Route("/", name="oicp_admin_client_index")
     * @Template()
     */
    public function indexAction()
    {
        $this->buildAccountDatatable();

        return array();
    }

    /**
     * @Route("/list", name="oicp_admin_client_list")
     */
    public function accountListAction()
    {
        return $this->buildAccountDatatable()->execute();
    }

    /**
     * @Route("/edit/{client}", name="oicp_admin_client_edit", defaults={"client":null})
     * 
     * @Template()
     */
    public function profileAction(Client $client)
    {
        
        return array("client" => $client);
    }

    
    /**
     * @Route("/record/{client}", name="oicp_admin_client_record", defaults={"client":null})
     * @Template()
     */
    public function clientRecordAction(Client $client)
    {
        if($client === null) {
            throw $this->createNotFoundException("This client does not exist");
        }
        
        return array("client" => $client);
    }
    
    private function buildAccountDatatable()
    {
        /* @var $datatable \Ali\DatatableBundle\Util\Datatable */
        $datatable = $this->get('datatable')->setEntity("WaldoOpenIdConnectModelBundle:Client", "Client");

        $datatable->setFields(array(
                    "Name" => "Client.clientName",
                    "Client ID" => "Client.clientId",
                    "Application type" => "Client.applicationType",
                    "Scope" => "Client.scope",
                    "Issued At" => "Client.clientIdIssuedAt",                    
                    "Actions" => 'Client.id',
                    "_identifier_" => 'Client.id',
                ))
                ->setRenderers(array(
                    3 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_scope.html.twig"),
                    4 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_datetime.html.twig"),                    
                    5 => array('view' => "WaldoOpenIdConnectAdminBundle:Datatable:_actions_client.html.twig"),                    
                ))

                ->setOrder("Client.clientName", "asc")
                ->setSearch(false)
                ->setGlobalSearch(true)
        ;

        return $datatable;
    }

}
