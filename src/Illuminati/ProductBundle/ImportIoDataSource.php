<?php

namespace Illuminati\ProductBundle;

/**
 * Class ImportIoDataSource
 * @package Illuminati\ProductBundle
 */
class ImportIoDataSource implements DataSourceInterface
{
    /**
     * @param $connectorGuid
     * @param $params
     * @return string
     */
    public function query($connectorGuid, $params)
    {
        $url = "https://api.import.io/store/data/" . $connectorGuid . "/_query?" . http_build_query($params);
        return file_get_contents($url);
    }
}