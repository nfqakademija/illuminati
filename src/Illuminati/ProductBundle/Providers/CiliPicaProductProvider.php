<?php

namespace Illuminati\ProductBundle\Providers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\DataSourceInterface;
use Illuminati\ProductBundle\Entity\Product;
use Illuminati\ProductBundle\Entity\Supplier;
use Illuminati\ProductBundle\ProductProviderInterface;

class CiliPicaProductProvider implements ProductProviderInterface
{
    public $DataSource;

    public $importIoConfig;

    public $url;

    public function __construct(DataSourceInterface $DataSource, $importIoConfig, $url)
    {
        $this->importIoConfig = $importIoConfig;
        $this->DataSource = $DataSource;
        $this->url = $url;
    }

    public function import(Supplier $supplier, EntityManagerInterface $em)
    {
        $params = array(
            'input/webpage/url' => $this->url,
            '_user' => $this->importIoConfig['user_id'],
            '_apikey' => $this->importIoConfig['api_key']
        );

        $result = $this->DataSource->query($this->importIoConfig['connectorGuid'], $params);
        if($result = json_decode($result)) {

            $query = $em->createQuery('DELETE FROM Illuminati\ProductBundle\Entity\Product');
            //$query->setParameter('supplier_id', $supplier->getId());
            $query->execute();

            $this->createProducts($em, $supplier, $result);
        }
    }

    private function createProducts(EntityManagerInterface $em, Supplier $supplier, $result)
    {
        foreach ($result->results as $item) {

            /**
             * [pizzasize_value_2_numbers/_source] => 50
             * [pizzasize_value_2_numbers] => 50
             * [largemedium_price_2/_currency] => EUR
             * [ciligreita_link] => http://www.cili.lt/wp-content/uploads/2015/09/STUDENTU.jpg
             * [ciligreita_image] => http://www.cili.lt/wp-content/uploads/2015/09/STUDENTU.jpg
             * [ciligreita_description] => Virtas kiaulienos kumpis, dešrelės, mocarela, sūris, pomidorų padažas, aliejaus ir česnako padažas, raudonėliai.
             * [pizzasize_value_1_numbers] => 30
             * [pizzasize_value_1_numbers/_source] => 30
             * [pizzasize_value_1] => 30 cm
             * [pizzasize_value_2] => 50 cm
             * [largemedium_price_1] => 3.99
             * [ciligreita_value] => STUDENTŲ
             * [largemedium_price_2] => 7.49
             * [largemedium_price_1/_source] => 3.99€
             * [largemedium_image] => http://www.cili.lt/wp-content/uploads/2015/09/imgPica.svg
             * [largemedium_price_1/_currency] => EUR
             * [largemedium_price_2/_source] => 7.49€
             */

            $item = (array)$item;

            if (isset($item['ciligreita_description'])) {

                $product = new Product();

                $product->setSupplier($supplier);
                $product->setTitle($item['ciligreita_value']);
                $product->setPrice($item['largemedium_price_1']);
                $product->setCurrency($item['largemedium_price_1/_currency']);
                $product->setImage($item['ciligreita_image']);

                $description = array();
                $description[] = $item['ciligreita_description'];
                $description[] = $item['pizzasize_value_1'];

                $product->setDescription(implode('<br />', $description));

                $em->persist($product);
                $em->flush();
            }
        }
    }
}