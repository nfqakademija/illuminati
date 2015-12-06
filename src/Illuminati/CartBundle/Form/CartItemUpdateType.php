<?php

namespace Illuminati\CartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CartItemUpdateType
 * @package Illuminati\CartBundle\Form
 */
class CartItemUpdateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('PUT')
            ->add('orderId', 'integer', ['attr' => ['class' => 'hidden']])
            ->add('productId', 'integer', ['attr' => ['class' => 'hidden']])
            ->add('quantity', 'integer', [
                'label' => false,
                'attr' => ['class' => 'form-control quantity']
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'IlluminatiCartBundleItemUpdateType';
    }
}
