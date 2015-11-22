<?php

namespace Illuminati\CartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckoutType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', 'collection', [
                'type' => new CartItemType()
            ])
            ->add('save', 'submit', [
                'label' => 'Confirm',
                'attr' => ['class'=>'btn btn-primary']
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'illuminati_cart_bundle_checkout';
    }
}
