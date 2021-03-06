<?php

namespace Waldo\OpenIdConnect\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Waldo\OpenIdConnect\ModelBundle\Entity\Client;
use Waldo\OpenIdConnect\AdminBundle\Form\Type\ClientFormType;

/**
 * @Route("/client")
 */
class ClientController extends Controller
{

    /**
     * @Route("/", name="oicp_admin_client_index")
     */
    public function indexAction()
    {
        $this->buildAccountDatatable();

        return $this->render("WaldoOpenIdConnectAdminBundle:Client:index.html.twig");
    }

    /**
     * @Route("/list", name="oicp_admin_client_list")
     */
    public function accountListAction()
    {
        return $this->buildAccountDatatable()->execute();
    }

    /**
     * @Route("/edit", name="oicp_admin_client_new", defaults={"client":null})
     * @Route("/edit/{client}", name="oicp_admin_client_edit", defaults={"client":null})
     * 
     */
    public function editAction(Request $request, Client $client = null)
    {
        $client = $client === null ? new Client() : $client;
        
        $form = $this->createForm(new ClientFormType(), $client);
        
        $form->handleRequest($request);
        
        if($request->isMethod('POST')) {
            if($form->isValid()) {
                
                $this->get('waldo_oic_p.admin.client_application')->handleClient($client);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                
                return $this->redirect($this->generateUrl('oicp_admin_client_edit', array('client' => $client->getId())));
            }
        }

        return $this->render("WaldoOpenIdConnectAdminBundle:Client:edit.html.twig", array(
                    "form" => $form->createView(),
                    "client" => $client
        ));
    }

    /**
     * @Route("/record/{client}", name="oicp_admin_client_record", defaults={"client":null})
     */
    public function clientRecordAction(Client $client)
    {
        if($client === null) {
            throw $this->createNotFoundException("This client does not exist");
        }
     
        return $this->render("WaldoOpenIdConnectAdminBundle:Client:clientRecord.html.twig", array("client" => $client));
    }
        
    /**
     * @Route("/delete/{client}", name="oicp_admin_client_delete", defaults={"client":null})
     * @Method({"POST"})
     */
    public function deleteClientAction(Client $client)
    {   
        if($client === null) {
            return new JsonResponse(array("errors" => "This client does not exist"), Response::HTTP_NOT_FOUND);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($client);
        $em->flush();
        
        return new JsonResponse("ok");
    }
    
    private function buildAccountDatatable()
    {
        /* @var $datatable \Ali\DatatableBundle\Util\Datatable */
        $datatable = $this->get('datatable')->setEntity("WaldoOpenIdConnectModelBundle:Client", "Client");

        $translator = $this->get('translator');
        
        $datatable->setFields(array(
                    $translator->trans("label.Name") => "Client.clientName",
                    $translator->trans("label.Client_ID") => "Client.clientId",
                    $translator->trans("label.application_type") => "Client.applicationType",
                    $translator->trans("label.scopes") => "Client.scope",
                    $translator->trans("label.Issued_at") => "Client.clientIdIssuedAt",                    
                    $translator->trans("table.Actions") => 'Client.id',
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
