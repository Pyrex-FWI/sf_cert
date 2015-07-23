<?php

namespace Cpyree\CustomerBundle\Form\Type;


class CustomerType extends \Symfony\Component\Form\AbstractType{

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options){

        $builder
            ->add('username', 'text')

            ->add('password', 'repeated', array(
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => 'password'))

            ->add('email', 'email')

            ->add('bornDate','birthday', array(
                'widget'    =>  'single_text'
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "customer";
    }
}