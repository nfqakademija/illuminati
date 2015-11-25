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
            ->add('quantity', 'integer', array('label' => false))
            ->add('product_id', 'hidden')
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
