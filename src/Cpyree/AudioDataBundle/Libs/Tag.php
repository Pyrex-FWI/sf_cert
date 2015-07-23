<?php
namespace Cpyree\AudioDataBundle\Libs;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tag
 *
 * @author christophep
 */
class Tag {
	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var array
	 */
	private $artists;
	/**
	 * @var year
	 */
	private $year;
	/**
	 * @var string
	 */
	private $album;
	/**
	 * @var string
	 */
	private $covers = array();

    function __construct($name = null){
        $this->title = $name;
    }
   
    public function getTitle(){
        return $this->title;
    }
    
    public function getAlbum(){
        return $this->album;
        
    }
    public function getArtist(){
        if(is_array($this->artists)){
            return implode(' Ft. ', $this->artists);
        }
        return $this->artists;
    }
    
    public function getYear(){
        return $this->year;
    }
   
    function __toString(){
        return $this->getArtist() . " - " . $this->getTitle() . " - " . $this->getAlbum() . " (" . $this->getYear() . ")";
    }

	/**
	 * @param mixed $album
	 */
	public function setAlbum($album)
	{
		$this->album = $album;
		return $this;
	}

	/**
	 * @param mixed $artists
	 */
	public function addArtist($artist)
	{
		$this->artists[] = $artist;
		return $this;
	}

	/**
	 * @param mixed $cover
	 */
	public function addCover($cover)
	{
		$this->covers[] = $cover;
		return $this;
	}

	/**
	 * @return string
     */
	public function getCovers()
	{
		return $this->covers;
	}

	/**
	 * @param null $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @param mixed $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
		return $this;
	}
    
    private $source;

    public function setSource($source){
        $this->source = $source;
    }
}