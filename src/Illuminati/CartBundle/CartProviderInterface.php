<?php
namespace Illuminati\CartBundle;

/**
 * Interface CartProviderInterface
 * @package Illuminati\CartBundle
 */
interface CartProviderInterface
{
    /**
     * @param $productId
     * @return mixed
     */
    public function addItem($productId);

    /**
     * @param $productId
     * @return mixed
     */
    public function getItem($productId);

    /**
     * @param $productId
     * @return mixed
     */
    public function removeItem($productId);

    /**
     * @return mixed
     */
    public function getItems();

    /**
     * @return mixed
     */
    public function getAmount();

    /**
     * @param $productId
     * @return integer
     */
    public function getQuantity($productId);

    /**
     * @param $productId
     * @param $price
     * @return mixed
     */
    public function geItemTotalAmount($productId, $price);

    /**
     * @param $productId
     * @param $quantity
     * @return mixed
     */
    public function updateQuantity($productId, $quantity);

    /**
     * @return string
     */
    public function getProviderCurrency();

    /**
     * Loads cart data from database
     * @param $userId
     * @param $orderId
     * @return mixed
     */
    public function load($userId, $orderId);
}