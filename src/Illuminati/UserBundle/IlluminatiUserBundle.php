<?php

namespace Illuminati\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IlluminatiUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
