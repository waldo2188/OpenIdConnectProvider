<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as defaultWebTestCase;

/**
 * Description of WebTestCase
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class WebTestCase extends defaultWebTestCase
{

    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $entityManager;

    protected static function initialize()
    {
        self::createClient();
    }

    public function setUp()
    {
        $this->populateVariables();
    }

    protected function populateVariables()
    {
        $this->client = static::createClient();
        $container = static::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
    }

}
