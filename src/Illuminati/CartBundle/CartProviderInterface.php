<?php
namespace Illuminati\CartBundle;

/**
 * Interface CartProviderInterface
 * @package Illuminati\CartBundle
 */
interface CartProviderInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function addItem($id);

    /**
     * @param $id
     * @return mixed
     */
    public function getItem($id);

    /**
     * @param $id
     * @return mixed
     */
    public function removeItem($id);

    /**
     * @return mixed
     */
    public function getItems();

    /**
     * @return mixed
     */
    public function getAmount();

    /**
     * @param $id
     * @return integer
     */
    public function getQuantity($id);
}