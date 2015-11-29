<?php

namespace Illuminati\ProductBundle\Providers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\DataSourceInterface;
use Illuminati\ProductBundle\Entity\Product;
use Illuminati\ProductBundle\Entity\Supplier;
use Illuminati\ProductBundle\ProductProviderInterface;

/**
 * Class CiliPicaProductProvider
 * @package Illuminati\ProductBundle\Providers
 */
class CiliPicaProductProvider implements ProductProviderInterface
{
    /**
     * @var DataSourceInterface
     */
    public $DataSource;

    /**
     * @var array
     */
    public $importIoConfig;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $currency;

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * CiliPicaProductProvider constructor.
     * @param DataSourceInterface $dataSource
     * @param $importIoConfig
     * @param $url
     * @param $currency
     */
    public function __construct(DataSourceInterface $dataSource, $importIoConfig, $url, $currency)
    {
        $this->importIoConfig = $importIoConfig;
        $this->DataSource = $dataSource;
        $this->url = $url;
        $this->currency = $currency;
    }

    /**
     * @param Supplier $supplier
     * @param EntityManagerInterface $entityManager
     * @return null
     */
    public function import(Supplier $supplier, EntityManagerInterface $entityManager)
    {
        $params = array(
            'input/webpage/url' => $this->url,
            '_user' => $this->importIoConfig['user_id'],
            '_apikey' => $this->importIoConfig['api_key']
        );

        $result = $this->DataSource->query($this->importIoConfig['connectorGuid'], $params);
        if ($result = json_decode($result)) {

            // delete all supplier products
            $entityManager->createQuery('DELETE ProductBundle:Product p WHERE p.supplier = :supplier')
                ->setParameter('supplier', $supplier)
                ->execute();

            $this->createProducts($entityManager, $supplier, $result);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Supplier $supplier
     * @param $result
     */
    private function createProducts(EntityManagerInterface $entityManager, Supplier $supplier, $result)
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

                $this->create($entityManager, [
                    'title' => $item['ciligreita_value'] . ' ' . $item['pizzasize_value_1'],
                    'supplier' => $supplier,
                    'price' => $item['largemedium_price_1'],
                    'currency' => $item['largemedium_price_1/_currency'],
                    'imageUrl' => $item['ciligreita_image'],
                    'description' => $item['ciligreita_description']
                ]);

                if (isset($item['pizzasize_value_2'])) {
                    $this->create($entityManager, [
                        'title' => $item['ciligreita_value'] . ' ' . $item['pizzasize_value_2'],
                        'supplier' => $supplier,
                        'price' => $item['largemedium_price_2'],
                        'currency' => $item['largemedium_price_2/_currency'],
                        'imageUrl' => $item['ciligreita_image'],
                        'description' => $item['ciligreita_description']
                    ]);
                }
            }
        }

        $entityManager->flush();
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $data
     */
    private function create(EntityManagerInterface $entityManager, $data)
    {
        $product = new Product();

        $product->setSupplier($data['supplier']);
        $product->setTitle($data['title']);
        $product->setPrice($data['price']);
        $product->setCurrency($data['currency']);
        $product->setImage($data['imageUrl']);
        $product->setDescription($data['description']);

        $entityManager->persist($product);
    }
}