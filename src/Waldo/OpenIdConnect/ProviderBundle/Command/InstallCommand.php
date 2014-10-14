<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class InstallCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('waldo:oicp:install')
                ->setDescription('Installs the OpenIdConnect Provider Bundle');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernelRootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $datatableDestination = "web/bundles/datatable";
        $datatableFoldersToLink = array(
            '/css',
            '/images',
            '/js'
        );

        $datatableSource = $kernelRootDir . "/../vendor/datatables/datatables/media";


//        "mkdir app/Resources/public",
//            "mkdir ,
//            "cp -R vendor/datatables/datatables/media/* app/Resources/public/datatable",
//            "rm -rf app/Resources/public/datatable/unit_testing"

        $filesystem = $this->getContainer()->get('filesystem');

        if ($filesystem->exists($datatableSource)) {

            foreach ($datatableFoldersToLink as $datatableFolderToLink) {
                
                $filesystem->remove($datatableDestination . $datatableFolderToLink);
                $filesystem->symlink($datatableSource . $datatableFolderToLink, $datatableDestination . $datatableFolderToLink);
                
            }
        }
    }

}
