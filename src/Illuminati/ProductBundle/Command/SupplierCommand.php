<?php
// src/Illuminati/ProductBundle/Command/SupplierCommand.php
namespace Illuminati\ProductBundle\Command;

use Illuminati\ProductBundle\ProductProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SupplierCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('product:supplier:import')
            ->setDescription('Supplier products import')
            ->addArgument(
                'provider',
                InputArgument::REQUIRED,
                'Please enter provider full class name?'
            );;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provider_name = $input->getArgument('provider');
        if ($provider_name) {

            $provider =  $this->getContainer()->get($provider_name);
            if($provider instanceof ProductProviderInterface) {
                $provider->import();
            }
        }

        //$output->writeln($text);
    }
}