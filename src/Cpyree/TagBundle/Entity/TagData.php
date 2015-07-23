<?php

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * TagData
 *
 * @ORM\Table(name="tag_data", indexes={@ORM\Index(name="FK_tag_data", columns={"media_file_id"})})
 * @ORM\Entity(repositoryClass="Cpyree\TagBundle\Entity\TagDataRepository")
 */
class TagData
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
	 * @Assert\Length(
	 * 		min="1",
	 * 		minMessage="Your title size must be greater than one character"
	 * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", type="string", length=255, nullable=true)
	 * @Assert\Length(
	 * 		minMessage="Your artist name must be greater than one character",
	 * 		min="1"
	 * )
     */
    private $artist;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Artist", inversedBy="tagDatas", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="tag_data_artists",
     *      joinColumns={@ORM\JoinColumn(name="tag_data_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="artist_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" =  "ASC"})
     */
    private $artists;
    
    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=45, nullable=true)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=4, nullable=true)
	 * @Assert\Regex(
	 * 		pattern="/^\d{4}$/",
	 *		message="Year must be respect YYYY format"
	 * )
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="album", type="string", length=255, nullable=true)
	 * @Assert\NotBlank()
     */
    private $album;

    /**
     * @var string
     *
     * @ORM\Column(name="bpm", type="decimal", precision=5, scale=2, nullable=true)
	 * @Assert\Type(
	 *		type="float"
	 * )
	 * @Assert\Range(
	 * 		min="60",
	 * 		max="180",
	 * 		minMessage="Bpm must greater than or equal 60, {{value}} is too slow",
	 * 		maxMessage="Bpm must less than or equal 160, {{value}] is too fast",
	 * 		groups={"QA"}
	 * )
     */
    private $bpm;

    /**
     * @var string
     *
     * @ORM\Column(name="initial_key", type="string", length=10, nullable=true)
     */
    private $initialKey;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     *
     * @var integer
     * @ORM\Column(name="cover_in_tag", type="boolean")
	 * @Assert\Type(
	 * 		type="bool"
	 * )
     */
    private $coverInTag = false;

    /**
     * @var \Cover
     *
     * @ORM\ManyToOne(targetEntity="Cover")
     * @ORM\JoinColumn(name="cover_id", referencedColumnName="id")
     */
    private $cover;

    /**
     * @var \MediaFile
     *
     * @ORM\OneToOne(targetEntity="MediaFile", inversedBy="tagData", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="media_file_id", referencedColumnName="id")
     */
    private $mediaFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
	 *
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bulk_last_pass", type="datetime", nullable=true)
     */
    private $bulkLastPass;    

    protected static $ArtistSeparators = array(
        "/(\s?_?\(?(feat.?|\sft.?|&|vs.?)\)?_?\s)|,\s|\sand\s|\set\s|\sFeaturing\s/i"
    );
    
    protected $cleanrArtistName = array(
        '/\s?\(www.*\)/i',
        '/\s\[.*\]/i',
        '/\"/'
    );


    public static function getArtistSeperator(){
        return self::$ArtistSeparators[0];
    }
    public function explodeArtistName(){
        $artists = array();
        $artist = mb_convert_case($this->getArtist(), MB_CASE_TITLE, "UTF-8");
        foreach ($this->ArtistSeparators as $pattern){
            $artists = array_merge($artists, preg_split($pattern, $artist));
        }
        $artists = $this->cleanArtistName($artists);
        return $artists;
    }
    
    
    public function cleanArtistName($inputs){
        $inputs = (array) $inputs;
        $output = array();
        foreach($inputs as $artist){
            foreach($this->cleanrArtistName as $patern){
                $artist = trim(preg_replace($patern, "", $artist));
            }
            if(preg_match("/^[a-z0-9]/i", $artist)){
                $output[] = $artist;
            }
        }
        return $output;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return TagData
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set artist
     *
     * @param string $artist
     * @return TagData
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return string 
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set genre
     *
     * @param string $genre
     * @return TagData
     */
    public function setGenre($genre)
    {
        $this->genre = ucwords(strtolower($genre));
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return TagData
     */
    public function setYear($year)
    {
        if(preg_match("/^(\d{1,4})(\-\d{1,2}-\d{1,2})?/", $year, $matches)){
            $this->year = $matches[1];
        }
        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set album
     *
     * @param string $album
     * @return TagData
     */
    public function setAlbum($album)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return string 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set bpm
     *
     * @param string $bpm
     * @return TagData
     */
    public function setBpm($bpm)
    {
        $bpm = floatval($bpm);
        //if(is_int($bpm) || is_float($bpm)){   
            $this->bpm = $bpm;
        //}
        return $this;
    }

    /**
     * Get bpm
     *
     * @return string 
     */
    public function getBpm()
    {
        return $this->bpm;
    }

    /**
     * Set initialKey
     *
     * @param string $initialKey
     * @return TagData
     */
    public function setInitialKey($initialKey)
    {
        if(preg_match("/^\d{1,2}\w$/", $initialKey)){
            $this->initialKey = $initialKey;
        }
        return $this;
    }

    /**
     * Get initialKey
     *
     * @return string 
     */
    public function getInitialKey()
    {
        return $this->initialKey;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return TagData
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return TagData
     */
    public function setCover(Cover $cover)
    {
        //$this->cover = base64_encode($cover);
        $this->cover = $cover;
        return $this;
    }

    /**
     * Get cover
     *
     * @return Cover 
     */
    public function getCover()
    {
       return $this->cover;
        //return base64_decode($this->cover);
    }

    /**
     * Set MefiaFile
     *
     * @param \Cpyree\TagBundle\Entity\MediaFile $mediaFile
     * @return TagData
     */
    public function setMediaFile(\Cpyree\TagBundle\Entity\MediaFile $mediaFile = null)
    {
        $this->mediaFile = $mediaFile;

        return $this;
    }

    /**
     * Get $mediaFile
     *
     * @return \Cpyree\TagBundle\Entity\MediaFile
     */
    public function getMediaFile()
    {
        return $this->mediaFile;
    }
    
    public function buildFromId3(\Cpyree\TagBundle\Lib\Id3 $id3){

        $this->setAlbum($id3->get('album'));
        $this->setTitle($id3->get('title'));
        $this->setYear($id3->get('year'));
        $this->setGenre($id3->get('genre'));
        $this->setArtist($id3->get('artist'));
        $this->setBpm($id3->get('bpm'));
        $this->setComment($id3->get('comment'));
        $this->setInitialKey($id3->get('initial_key'));
        if($id3->get('cover')) $this->setCoverInTag(1);

        return $this;
    }
    
    
    public function getBulkLine(){
        $ENCLOSE = '"';
        $SEP = ";";
        $line = "";
        $line .=  $ENCLOSE.$this->mediaFile->getId().$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->title.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->artist.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->genre.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->year.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->album.$ENCLOSE;
        $line .=  $SEP;
        if($this->bpm > 0){
            $line .=  $ENCLOSE.$this->bpm.$ENCLOSE;
        }else{
            $line .=  '0';
        }
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->initialKey.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->comment.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->coverInTag.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->created->format("Y-m-d H:i:s").$ENCLOSE;
        
        
        
        return $line;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return TagData
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return TagData
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set coverInTag
     *
     * @param boolean $coverInTag
     * @return TagData
     */
    public function setCoverInTag($coverInTag)
    {
        $this->coverInTag = $coverInTag;

        return $this;
    }

    /**
     * Get coverInTag
     *
     * @return boolean 
     */
    public function getCoverInTag()
    {
        return $this->coverInTag;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->artists = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add artists
     *
     * @param \Cpyree\TagBundle\Entity\Artist $artists
     * @return TagData
     */
    public function addArtist(\Cpyree\TagBundle\Entity\Artist $artists)
    {
        if(!$this->artists->contains($artists)){
            $this->artists[] = $artists;
        }
        return $this;
    }

    /**
     * Remove artists
     *
     * @param \Cpyree\TagBundle\Entity\Artist $artists
     */
    public function removeArtist(\Cpyree\TagBundle\Entity\Artist $artists)
    {
        $this->artists->removeElement($artists);
    }

    /**
     * Get artists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * Set bulkLastPass
     *
     * @param \DateTime $bulkLastPass
     * @return TagData
     */
    public function setBulkLastPass($bulkLastPass)
    {
        $this->bulkLastPass = $bulkLastPass;

        return $this;
    }

    /**
     * Get bulkLastPass
     *
     * @return \DateTime 
     */
    public function getBulkLastPass()
    {
        return $this->bulkLastPass;
    }
}
