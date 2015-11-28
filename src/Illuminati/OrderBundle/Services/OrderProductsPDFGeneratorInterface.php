<?php

namespace Illuminati\OrderBundle\Services;

use Illuminati\OrderBundle\Entity\Host_order;

interface OrderProductsPDFGeneratorInterface
{
    public function generate(Host_order $hostOrder);
}