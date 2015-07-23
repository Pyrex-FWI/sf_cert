<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 15/03/2015
 * Time: 14:46
 */

namespace Cpyree\DigitalDjPoolBundle\Event;


use Cpyree\DigitalDjPoolBundle\Entity\Track;
use Symfony\Component\EventDispatcher\Event;

class TrackEventDispatcher extends Event{

    /**
     * @var Track
     */
    protected $track;

    public function __construct(Track $track){
        $this->track = $track;
    }

    /**
     * @return Track
     */
    public function getTrack(){
        return $this->track;
    }
}