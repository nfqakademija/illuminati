<?php

namespace Illuminati\ProductBundle;

/**
 * Interface DataSourceInterface
 * @package Illuminati\ProductBundle
 */
interface DataSourceInterface
{
    /**
     * @param $connectorGuid
     * @param $params
     * @return mixed
     */
    public function query($connectorGuid, $params);
}