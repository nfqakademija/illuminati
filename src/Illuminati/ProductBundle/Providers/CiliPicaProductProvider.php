<?php

namespace Illuminati\ProductBundle\Providers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\DataSourceInterface;
use Illuminati\ProductBundle\Entity\Product;
use Illuminati\ProductBundle\ProductProviderInterface;

class CiliPicaProductProvider implements ProductProviderInterface
{
    public $DataSource;

    public $importIoConfig;

    public $url;

    protected $em;

    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __construct(DataSourceInterface $DataSource, $importIoConfig, $url)
    {
        $this->importIoConfig = $importIoConfig;
        $this->DataSource = $DataSource;
        $this->url = $url;
    }

    public function import()
    {
        $supplier = $this->em->getRepository('ProductBundle:Supplier')->findOneBy([
            'provider_class' => get_class()
        ]);

        if(!$supplier) {
            echo "Provider $supplier doesn't exists";
            exit;
        }

        $params = array(
            'input/webpage/url' => $this->url,
            '_user' => $this->importIoConfig['user_id'],
            '_apikey' => $this->importIoConfig['api_key']
        );

        $result = $this->DataSource->query($this->importIoConfig['connectorGuid'], $params);
        $result = json_decode($result);

        foreach ($result->results as $item) {

            /**
            [pizzasize_value_2_numbers/_source] => 50
            [pizzasize_value_2_numbers] => 50
            [largemedium_price_2/_currency] => EUR
            [ciligreita_link] => http://www.cili.lt/wp-content/uploads/2015/09/STUDENTU.jpg
            [ciligreita_image] => http://www.cili.lt/wp-content/uploads/2015/09/STUDENTU.jpg
            [ciligreita_description] => Virtas kiaulienos kumpis, dešrelės, mocarela, sūris, pomidorų padažas, aliejaus ir česnako padažas, raudonėliai.
            [pizzasize_value_1_numbers] => 30
            [pizzasize_value_1_numbers/_source] => 30
            [pizzasize_value_1] => 30 cm
            [pizzasize_value_2] => 50 cm
            [largemedium_price_1] => 3.99
            [ciligreita_value] => STUDENTŲ
            [largemedium_price_2] => 7.49
            [largemedium_price_1/_source] => 3.99€
            [largemedium_image] => http://www.cili.lt/wp-content/uploads/2015/09/imgPica.svg
            [largemedium_price_1/_currency] => EUR
            [largemedium_price_2/_source] => 7.49€
             */

            $item = (array)$item;

            if(isset($item['ciligreita_description'])) {

                $product = new Product();
                $product->setSupplier($supplier);
                $product->setTitle($item['ciligreita_value']);
                $product->setPrice($item['largemedium_price_1']);
                $product->setImage($item['ciligreita_image']);
                $product->setDescription($item['ciligreita_description']);

                $this->em->persist($product);
                $this->em->flush();
            }
        }
    }
}