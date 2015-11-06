<?php

namespace Illuminati\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Host_orderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array("label"=>"order.title"))
            ->add('description','textarea', array("label"=>"order.description"))
            ->add('closeDate','datetime', array("label"=>"order.closeDate"))
            ->add('orderPatricipants','textarea',array('mapped' => false,"label"=>"order.participants" ))
            ->add('submit','submit',array("label"=>"order.submit"));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Illuminati\OrderBundle\Entity\Host_order'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'illuminati_orderbundle_host_order';
    }
}
