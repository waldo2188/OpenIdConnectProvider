<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Ali\DatatableBundle\AliDatatableBundle(),
            new Waldo\OpenIdConnect\ProviderBundle\WaldoOpenIdConnectProviderBundle(),
            new Waldo\OpenIdConnect\EnduserBundle\WaldoOpenIdConnectEnduserBundle(),
            new Waldo\OpenIdConnect\MailingBundle\WaldoOpenIdConnectMailingBundle(),
            new Waldo\OpenIdConnect\ModelBundle\WaldoOpenIdConnectModelBundle(),
            new Waldo\OpenIdConnect\AdminBundle\WaldoOpenIdConnectAdminBundle(),
            new Cnerta\BreadcrumbBundle\CnertaBreadcrumbBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),
            new Waldo\OpenIdConnect\LdapProviderBundle\WaldoOpenIdConnectLdapProviderBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
