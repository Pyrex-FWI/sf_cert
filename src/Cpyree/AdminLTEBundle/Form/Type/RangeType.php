<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 15/11/2014
 * Time: 21:31
 */

namespace Cpyree\AdminLTEBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Cpyree\AdminLTEBundle\Form\DataTransformer\RangeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RangeType extends AbstractType{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rangeToStringTransformer = new RangeToStringTransformer();
        $builder->addModelTransformer($rangeToStringTransformer);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'min'           =>  0,
            //'max'           =>  100,
            'showDiff'  =>  false

        ));

        $resolver->setRequired(
            array(
                'min',
                'max'
            )
        );
        $resolver->setOptional(
            array(
                'curMax',
                'curMin',
            )
        );
        $resolver->setAllowedTypes(array(
            //'curMin' => 'integer',
            //'curMax' => 'integer',
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $curVal = explode(':', $view->vars['value']);
        if(!isset($options['curMin'])){
            $options['curMin'] = $options['min'];
        }
        if(!isset($options['curMax'])){
            $options['curMax'] = $options['max'];
        }
        $view->vars = array_merge(
            $view->vars,
            array(
                'curMax'    => count($curVal)>1? $curVal[1] : intval($options['curMax']),
                'curMin'    => count($curVal)>1? $curVal[0] : intval($options['curMin']),
                'max'       => intval($options['max']),
                'min'       => intval($options['min']),
                'showDiff'  => $options['showDiff']
            )
        );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "range";
    }

    /**
     * @return string
     */
    public function getParent(){
        return 'text';
    }
}