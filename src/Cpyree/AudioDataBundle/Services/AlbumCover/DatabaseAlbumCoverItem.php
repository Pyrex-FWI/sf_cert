<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 21/02/2015
 * Time: 19:01
 */

namespace Cpyree\AudioDataBundle\Services\AlbumCover;



use Cpyree\TagBundle\Entity\TagData;

class DatabaseAlbumCoverItem extends AbstractAlbumCoverItem implements   AlbumCoverItemInterface {


    /** @var  \Doctrine\ORM\Mapping\ClassMetadata */
    private $classMetadata;
    public function __construct($item, \Doctrine\ORM\Mapping\ClassMetadata $cm){
        parent::__construct($item);
        $this->classMetadata = $cm;
    }

    /**
     * @param array $source
     * @return string
     */
    public function getSearchTerm($source = array()){

        $terms = array();
        foreach($source['methods'] as $method){
            $terms[] = call_user_func(array($this->item, $method));
        }
        return preg_replace(TagData::getArtistSeperator(), ' ', implode(' ', $terms));
    }
    public function getIdentifier(){
        return call_user_func(array($this->item,'get'.ucfirst($this->classMetadata->getSingleIdentifierFieldName())));
    }

}