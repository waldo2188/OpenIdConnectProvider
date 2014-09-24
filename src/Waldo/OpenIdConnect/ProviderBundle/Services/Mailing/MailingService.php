<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Services\Mailing;

use Symfony\Bridge\Monolog\Logger;
use Twig_Environment;
use Swift_Mailer;
use Waldo\OpenIdConnect\ProviderBundle\Services\Mailing\MailParameters;

/**
 *
 * @author Valérian Girard <valerian.girard@eduter.fr>
 */
class MailingService
{

    private $config;

    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    public function __construct($config, Twig_Environment $templating, Swift_Mailer $mailer, Logger $logger)
    {
        $this->config = $config;
        
        //TODO add config resolver
        $this->config['template'] = "WaldoOpenIdConnectProviderBundle";
        $this->config['from'] = array("email" => "no-reply@exemple.com", "name" => "OIC Provider");
        
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * Envoie de mail simple
     *
     * @param array/array <Account> $accountList
     * @param array $paramList
     * @return type
     */
    public function sendEmail(array $accountList, $template, MailParameters $mailParameters = null)
    {
        if($mailParameters == null) {
            $mailParameters = new MailParameters();
        }
        
        $mails = $this->makeGenericMail($template, $mailParameters);

        return $this->send($mails, $accountList, array());
    }

    /**
     * Méthode d'envoie d'email
     *
     * @param array $data 
     * @param array Array Account $aEmailTo
     * @param array $aAttachement
     */
    protected function send($data, $aEmailTo, $aAttachement = array(), $otherThing = array())
    {

        $mailerForSend = $this->mailer;

        foreach ($aEmailTo as $user) {

            //Create the message
            /* @var $message \Swift_Message */
            $message = \Swift_Message::newInstance()
                    // Give the message a subject
                    ->setSubject($data['objet'])
                    // Set the From address with an associative array
                    ->setFrom(array($this->config['from']['email'] => $this->config['from']['name']));

            foreach ($aAttachement as $oneAttachment) {

                $attachment = \Swift_Attachment::newInstance($oneAttachment['content'], $oneAttachment['name'], $oneAttachment['type']);

                $message->attach($attachment);
            }

            $failedRecipients = array();
            $numSent = 0;

            if ($user instanceof Account) {
                $message->setTo($user->getEmail());
            } elseif (is_string($user)) {
                $message->setTo($user);
            } else {
                throw new \RuntimeException('invalid email');
            }
            
            $message->setBody($this->templating->render( $this->config['template'] . ':Mails:' . $data['template'] . '.html.twig', $data), "text/html");            
            $message->addPart($this->templating->render($this->config['template'] . ':Mails:' . $data['template'] . '.txt.twig', $this->getRaw($data)), "text/plain");

            $numSent += $mailerForSend->send($message, $failedRecipients);
        }
        return $numSent;
    }

    /**
     * Build generic email
     *
     * @param string $typeEmail prefix of the block's name to used (defined in Resources/views/Mails/BlocksMail.txt.twig)
     * @param MailParameters $mailParameters
     */
    protected function makeGenericMail($typeEmail, MailParameters $mailParameters)
    {

        $template = $this->templating->loadTemplate($this->config['template'] . ':Mails:BlocksMail.html.twig');
        $aRet = array('template' => 'default');

        $aRet['objet'] = $template->renderBlock($typeEmail . '_object', $mailParameters->getObjectParameters());
        $aRet['body'] = $template->renderBlock($typeEmail . '_body', $mailParameters->getBodyParameters());

        return $aRet;
    }
    
    protected function getRaw($data)
    {
        $data['body'] = strip_tags($data['body']);
        return $data;
    }

}