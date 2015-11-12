<?php

namespace Illuminati\ProductBundle;

interface DataSourceInterface
{
    public function query($connectorGuid, $params);
}