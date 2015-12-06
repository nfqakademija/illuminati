<?php
// src/Illuminati/ProductBundle/Command/SupplierCommand.php
namespace Illuminati\ProductBundle\Command;

use Illuminati\ProductBundle\ProductProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SupplierCommand
 * @package Illuminati\ProductBundle\Command
 */
class SupplierCommand extends ContainerAwareCommand
{
    protected $provider;

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    protected function configure()
    {
        $this
            ->setName('product:supplier:import')
            ->setDescription('Supplier products import')
            ->addArgument(
                'provider',
                InputArgument::REQUIRED,
                'Please enter provider full class name?'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $providerName = $input->getArgument('provider');
        if ($providerName) {

            $supplier = $entityManager->getRepository('ProductBundle:Supplier')->findOneBy([
                'provider' => $providerName
            ]);

            $provider = $this->getContainer()->get($providerName);
            if ($provider instanceof ProductProviderInterface) {
                $provider->import($supplier, $entityManager);
            }
        }

        $output->writeln('Your products was imported successfully!');
    }
}
