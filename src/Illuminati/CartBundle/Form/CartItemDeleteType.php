<?php

namespace Illuminati\CartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CartItemDeleteType
 * @package Illuminati\CartBundle\Form
 */
class CartItemDeleteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return \Symfony\Component\Form\FormConfigBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add('orderId', 'integer', ['attr' => ['class' => 'hidden']])
            ->add('productId', 'integer', ['attr' => ['class' => 'hidden']])
            ->add('redirectUrl', 'hidden')
            ->setMethod('DELETE');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'IlluminatiCartBundleItemDeleteType';
    }
}
