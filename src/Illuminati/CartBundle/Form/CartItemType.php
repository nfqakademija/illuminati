<?php

namespace Illuminati\CartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CartItemType
 * @package Illuminati\CartBundle\Form
 */
class CartItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', [
                'label' => false,
                'attr' => [
                    'class' => 'form-control quantity',
                    'data-quantity' => 'true'
                ]
            ])
            ->add('product_id', 'hidden', [
                'attr' => [
                    'data-product-id' => 'true'
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'illuminati_cart_bundle_cart_item';
    }
}
