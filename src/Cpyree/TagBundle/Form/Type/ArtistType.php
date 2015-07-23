<?php
namespace Cpyree\TagBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of ArtistType
 *
 * @author christophep
 */
class ArtistType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name')
                ->add('update','submit');
    }
    
    public function getName() {
        return "artist";
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class'    =>  'Cpyree\TagBundle\Entity\Artist'
        ));
    }

//put your code here
}
