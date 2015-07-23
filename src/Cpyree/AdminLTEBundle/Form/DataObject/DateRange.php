<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 17/11/2014
 * Time: 08:23
 */

namespace Cpyree\AdminLTEBundle\Form\DataObject;


use Symfony\Component\Validator\Constraints\DateTime;

class DateRange {


    /**
     * @var
     */
    public $max;
    public $min;

    /**
     * @param $min
     * @param $max
     */
    public function __construct($min, $max){
        $this->setMin($min)->setMax($max);
    }

    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    public function setMax($max){
        $this->max = $max;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getMax(){
        return new \DateTime('@'.$this->max);
    }

    /**
     * @return \DateTime
     */
    public function getMin(){
        return new \DateTime('@'.$this->min);
    }
} 