<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 12/10/2014
 * Time: 10:53
 */
namespace Cpyree\DigitalDjPoolBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TrackSearchType extends AbstractType{





    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'TrackFilter';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->setMethod('GET')
            ->add('text', 'text')
            ->add('search','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            //'data_class' => 'Cpyree\DigitalDjPoolBundle\Entity\Track',
        ));

    }

}