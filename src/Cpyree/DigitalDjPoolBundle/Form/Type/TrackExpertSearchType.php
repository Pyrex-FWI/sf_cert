<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 12/10/2014
 * Time: 10:53
 */
namespace Cpyree\DigitalDjPoolBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TrackExpertSearchType extends AbstractType{


    public $uidMin;
    public $uidMax;
    public $uidCurMin;
    public $uidCurMax;

    public $trackIdMin;
    public $trackIdMax;
    public $trackIdCurMin;
    public $trackIdCurMax;

    public $dateReleaseMin;
    public $dateReleaseMax;

    public $approvalMode = true;

    private $translator;
    /**
     * @return boolean
     */
    public function isApprovalMode()
    {
        return $this->approvalMode;
    }

    /**
     * @param boolean $approvalMode
     */
    public function setApprovalMode($approvalMode)
    {
        $this->approvalMode = $approvalMode;
    }

    /**
     * @return mixed
     */
    public function getDateReleaseMin()
    {
        return $this->dateReleaseMin;
    }

    /**
     * @param mixed $dateReleaseMin
     * @return $this
     */
    public function setDateReleaseMin($dateReleaseMin)
    {
        $this->dateReleaseMin = $dateReleaseMin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateReleaseMax()
    {
        return $this->dateReleaseMax;
    }

    /**
     * @param mixed $dateReleaseMax
     * @return $this
     */
    public function setDateReleaseMax($dateReleaseMax)
    {
        $this->dateReleaseMax = $dateReleaseMax;
        return $this;
    }


    /**
     * @return int
     */
    public function getTrackIdMin()
    {
        return $this->trackIdMin;
    }

    /**
     * @param int $trackIdMin
     * @return $this
     */
    public function setTrackIdMin($trackIdMin)
    {
        $this->trackIdMin = $trackIdMin;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrackIdMax()
    {
        return $this->trackIdMax;
    }

    /**
     * @param int $trackIdMax
     * @return $this
     */
    public function setTrackIdMax($trackIdMax)
    {
        $this->trackIdMax = $trackIdMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrackIdCurMin()
    {
        return $this->trackIdCurMin;
    }

    /**
     * @param int $trackIdCurMin
     * @return $this
     */
    public function setTrackIdCurMin($trackIdCurMin)
    {
        $this->trackIdCurMin = $trackIdCurMin;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrackIdCurMax()
    {
        return $this->trackIdCurMax;
    }

    /**
     * @param int $trackIdCurMax
     * @return $this
     */
    public function setTrackIdCurMax($trackIdCurMax)
    {
        $this->trackIdCurMax = $trackIdCurMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getUidMin()
    {
        return $this->uidMin;
    }

    /**
     * @param int $min
     * @return $this
     */
    public function setUidMin($min)
    {
        $this->uidMin = intval($min);
        return $this;
    }

    /**
     * @return int
     */
    public function getUidMax()
    {
        return $this->uidMax;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function setUidMax($max)
    {
        $this->uidMax = intval($max);
        return $this;
    }

    /**
     * @return int
     */
    public function getUidCurMin()
    {
        return $this->uidCurMin;
    }

    /**
     * @param int $curMin
     * @return $this
     */
    public function setUidCurMin($curMin)
    {
        $this->uidCurMin = intval($curMin);
        return $this;
    }

    /**
     * @return int
     */
    public function getUidCurMax()
    {
        return $this->uidCurMax;
    }

    /**
     * @param int $curMax
     * @return $this
     */
    public function setUidCurMax($curMax)
    {
        $this->uidCurMax = intval($curMax);
        return $this;
    }

    public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $trans)
    {
        $this->translator = $trans;
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'TrackExpertFilter';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);

        $builder
            ->setMethod('GET')
            ->add('title', 'text', array(
                'mapped'    => false,
                'required'  =>  false
            ))
            ->add('uidRange', 'range', array(
                'mapped'    =>  false,
                'min'       =>  $this->getUidMin(),
                'max'       =>  $this->getUidMax(),
                'curMin'    =>  $this->getUidCurMin(),
                'curMax'    =>  $this->getUidCurMax(),
                'showDiff'  =>  true
            ))
            ->add('trackIdRange', 'range', array(
                'mapped'    =>  false,
                'min'       =>  $this->getTrackIdMin(),
                'max'       =>  $this->getTrackIdMax(),
                'curMin'    =>  $this->getTrackIdCurMin(),
                'curMax'    =>  $this->getTrackIdCurMax(),
            ))
            ->add('releaseDateRange', 'date_range', array(
                'min'       =>  $this->getDateReleaseMin(),
                'max'       =>  $this->getDateReleaseMax(),
                'format'    =>  'timestamp',
                'mapped'    =>  false
            ))
            ->add('approbationType', 'choice', array(
                'mapped'    =>  false,
                'empty_value' => $this->translator->trans('Choose approval type'),
                'empty_data'  => null,
                'required' => false,
                'choice_list' => new SimpleChoiceList(
                    array(
                        'APPROVED'   => $this->translator->trans('Approved'),
                        'NEUTRAL'   => $this->translator->trans('Neutral'),
                        'NOT_APPROVED'   => $this->translator->trans('Not approved'),
                        'NOT_PROCESSED'   => $this->translator->trans('Not yet processed'),
                    )
                ),
            ))
            ->add('export_all', 'checkbox', array('mapped' => false,'required'  =>  false))
            ->add('deleted', 'checkbox',array('required'=>false))
            ->add('search','submit', array(
                'label' =>  'Run'
            ));
        if($this->isApprovalMode()){
            $builder->add('isApproval', 'hidden', array(
                'mapped'    =>  false,
                'attr'      => array(
                    'value'     =>  1,
                )
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            //'data_class' => 'Cpyree\DigitalDjPoolBundle\Entity\Track',
        ));

    }

}