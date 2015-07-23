<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 24/01/15
 * Time: 17:03
 */


namespace Cpyree\AudioDataBundle\TagReader;

interface BinTagReaderInterface extends TagReaderInterface{

    public function setBin($bin);

    public function getCommandLine();
}