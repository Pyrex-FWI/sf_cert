<?php

namespace Cpyree\AuthBundle\Form\Type;


use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserRegistrationType extends \Symfony\Component\Form\AbstractType{

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
            ->add('groups', 'entity', array(
                'class' => 'CpyreeAuthBundle:Group',
                'property' => 'role' ,
                'multiple'  => true
            ))
            ->add('email', 'email')
            ->add('valider', 'submit')


        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpyree\AuthBundle\Entity\User',
        ));
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