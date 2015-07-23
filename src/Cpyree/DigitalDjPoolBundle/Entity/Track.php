<?php

namespace Cpyree\DigitalDjPoolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TxDigitaldjpoolDomainModelTrack
 *
 * @ORM\Entity(repositoryClass="Cpyree\DigitalDjPoolBundle\Entity\TrackRepository")
 * @ORM\Table(name="tx_digitaldjpool_domain_model_track", indexes={@ORM\Index(name="parent", columns={"pid"}),@ORM\Index(name="trackid", columns={"track_id"}), @ORM\Index(name="t3ver_oid", columns={"t3ver_oid", "t3ver_wsid"}), @ORM\Index(name="language", columns={"l10n_parent", "sys_language_uid"})})
 *
 */
class Track
{
    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="pid", type="integer", nullable=false)
     */
    private $pid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="track_id", type="integer", nullable=false)
     */
    private $trackId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="fulltitle", type="string", length=255, nullable=false)
     */
    private $fulltitle = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="downloaded", type="boolean", nullable=false)
     */
    private $downloaded = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="raw_data", type="text", nullable=false)
     */
    private $rawData;

    /**
     * @var integer
     *
     * @ORM\Column(name="tstamp", type="integer", nullable=false)
     */
    private $tstamp = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="crdate", type="integer", nullable=false)
     */
    private $crdate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="cruser_id", type="integer", nullable=false)
     */
    private $cruserId = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="hidden", type="boolean", nullable=false)
     */
    private $hidden = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="starttime", type="integer", nullable=false)
     */
    private $starttime = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="endtime", type="integer", nullable=false)
     */
    private $endtime = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_oid", type="integer", nullable=false)
     */
    private $t3verOid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_id", type="integer", nullable=false)
     */
    private $t3verId = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="track", type="integer", nullable=false)
     */
    private $track = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="bpm", type="integer", nullable=false)
     */
    private $bpm = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="release_date", type="integer", nullable=true)
     */
    private $releaseDate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_wsid", type="integer", nullable=false)
     */
    private $t3verWsid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="related_tracks", type="text", nullable=true)
     */
    private $relatedTracks;

    /**
     * @var string
     *
     * @ORM\Column(name="t3ver_label", type="string", length=255, nullable=false)
     */
    private $t3verLabel = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="t3ver_state", type="boolean", nullable=false)
     */
    private $t3verState = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_stage", type="integer", nullable=false)
     */
    private $t3verStage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_count", type="integer", nullable=false)
     */
    private $t3verCount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_tstamp", type="integer", nullable=false)
     */
    private $t3verTstamp = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="t3ver_move_id", type="integer", nullable=false)
     */
    private $t3verMoveId = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="sys_language_uid", type="integer", nullable=false)
     */
    private $sysLanguageUid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="l10n_parent", type="integer", nullable=false)
     */
    private $l10nParent = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="l10n_diffsource", type="blob", nullable=true)
     */
    private $l10nDiffsource;
    /**
     * @var string
     *
     * @ORM\Column(name="full_path", type="string", nullable=true)
     */
    private $fullPath;

    /**
     * @var string
     *
     * @ORM\Column(name="related_genres", type="text", nullable=false)
     */
    private $relatedGenres;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hard_delete", type="boolean", nullable=false)
     */
    private $hardDelete = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="hard_delete_date", type="integer", nullable=false)
     */
    private $hardDeleteDate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="approval", type="integer", nullable=false)
     */
    private $approval = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="approval_date", type="integer", nullable=false)
     */
    private $approvalDate = '0';

    /**
     * @var integer
     * @ORM\Column(name="play_count", type="integer", nullable=false)
     */
    private $playCount = 0;

    /**
     * Get uid
     *
     * @return integer 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return Track
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set trackId
     *
     * @param integer $trackId
     * @return Track
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;

        return $this;
    }

    /**
     * Get trackId
     *
     * @return integer 
     */
    public function getTrackId()
    {
        return $this->trackId;
    }

    /**
     * Set fulltitle
     *
     * @param string $fulltitle
     * @return Track
     */
    public function setFulltitle($fulltitle)
    {
        $this->fulltitle = $fulltitle;

        return $this;
    }

    /**
     * Get fulltitle
     *
     * @return string 
     */
    public function getFulltitle()
    {
        return $this->fulltitle;
    }

    /**
     * Set downloaded
     *
     * @param boolean $downloaded
     * @return Track
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * Get downloaded
     *
     * @return boolean 
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * Set rawData
     *
     * @param string $rawData
     * @return Track
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * Get rawData
     *
     * @return string 
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Set tstamp
     *
     * @param integer $tstamp
     * @return Track
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;

        return $this;
    }

    /**
     * Get tstamp
     *
     * @return integer 
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Set crdate
     *
     * @param integer $crdate
     * @return Track
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;

        return $this;
    }

    /**
     * Get crdate
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        $date = new \DateTime();
        $date->setTimestamp($this->crdate);
        return $date;
    }

    /**
     * Set cruserId
     *
     * @param integer $cruserId
     * @return Track
     */
    public function setCruserId($cruserId)
    {
        $this->cruserId = $cruserId;

        return $this;
    }

    /**
     * Get cruserId
     *
     * @return integer 
     */
    public function getCruserId()
    {
        return $this->cruserId;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Track
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     * @return Track
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean 
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set starttime
     *
     * @param integer $starttime
     * @return Track
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;

        return $this;
    }

    /**
     * Get starttime
     *
     * @return integer 
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set endtime
     *
     * @param integer $endtime
     * @return Track
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;

        return $this;
    }

    /**
     * Get endtime
     *
     * @return integer 
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set t3verOid
     *
     * @param integer $t3verOid
     * @return Track
     */
    public function setT3verOid($t3verOid)
    {
        $this->t3verOid = $t3verOid;

        return $this;
    }

    /**
     * Get t3verOid
     *
     * @return integer 
     */
    public function getT3verOid()
    {
        return $this->t3verOid;
    }

    /**
     * Set t3verId
     *
     * @param integer $t3verId
     * @return Track
     */
    public function setT3verId($t3verId)
    {
        $this->t3verId = $t3verId;

        return $this;
    }

    /**
     * Get t3verId
     *
     * @return integer 
     */
    public function getT3verId()
    {
        return $this->t3verId;
    }

    /**
     * Set track
     *
     * @param integer $track
     * @return Track
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * Get track
     *
     * @return integer 
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Track
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set bpm
     *
     * @param integer $bpm
     * @return Track
     */
    public function setBpm($bpm)
    {
        $this->bpm = $bpm;

        return $this;
    }

    /**
     * Get bpm
     *
     * @return integer 
     */
    public function getBpm()
    {
        return $this->bpm;
    }

    /**
     * Set releaseDate
     *
     * @param integer $releaseDate
     * @return Track
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return \Datetime
     */
    public function getReleaseDate()
    {
        $d = new \DateTime();
        $d->setTimestamp($this->releaseDate);
        return $d;
    }

    /**
     * Set t3verWsid
     *
     * @param integer $t3verWsid
     * @return Track
     */
    public function setT3verWsid($t3verWsid)
    {
        $this->t3verWsid = $t3verWsid;

        return $this;
    }

    /**
     * Get t3verWsid
     *
     * @return integer 
     */
    public function getT3verWsid()
    {
        return $this->t3verWsid;
    }

    /**
     * Set relatedTracks
     *
     * @param string $relatedTracks
     * @return Track
     */
    public function setRelatedTracks($relatedTracks)
    {
        $this->relatedTracks = $relatedTracks;

        return $this;
    }

    /**
     * Get relatedTracks
     *
     * @return string 
     */
    public function getRelatedTracks()
    {
        return $this->relatedTracks;
    }

    /**
     * Set t3verLabel
     *
     * @param string $t3verLabel
     * @return Track
     */
    public function setT3verLabel($t3verLabel)
    {
        $this->t3verLabel = $t3verLabel;

        return $this;
    }

    /**
     * Get t3verLabel
     *
     * @return string 
     */
    public function getT3verLabel()
    {
        return $this->t3verLabel;
    }

    /**
     * Set t3verState
     *
     * @param boolean $t3verState
     * @return Track
     */
    public function setT3verState($t3verState)
    {
        $this->t3verState = $t3verState;

        return $this;
    }

    /**
     * Get t3verState
     *
     * @return boolean 
     */
    public function getT3verState()
    {
        return $this->t3verState;
    }

    /**
     * Set t3verStage
     *
     * @param integer $t3verStage
     * @return Track
     */
    public function setT3verStage($t3verStage)
    {
        $this->t3verStage = $t3verStage;

        return $this;
    }

    /**
     * Get t3verStage
     *
     * @return integer 
     */
    public function getT3verStage()
    {
        return $this->t3verStage;
    }

    /**
     * Set t3verCount
     *
     * @param integer $t3verCount
     * @return Track
     */
    public function setT3verCount($t3verCount)
    {
        $this->t3verCount = $t3verCount;

        return $this;
    }

    /**
     * Get t3verCount
     *
     * @return integer 
     */
    public function getT3verCount()
    {
        return $this->t3verCount;
    }

    /**
     * Set t3verTstamp
     *
     * @param integer $t3verTstamp
     * @return Track
     */
    public function setT3verTstamp($t3verTstamp)
    {
        $this->t3verTstamp = $t3verTstamp;

        return $this;
    }

    /**
     * Get t3verTstamp
     *
     * @return integer 
     */
    public function getT3verTstamp()
    {
        return $this->t3verTstamp;
    }

    /**
     * Set t3verMoveId
     *
     * @param integer $t3verMoveId
     * @return Track
     */
    public function setT3verMoveId($t3verMoveId)
    {
        $this->t3verMoveId = $t3verMoveId;

        return $this;
    }

    /**
     * Get t3verMoveId
     *
     * @return integer 
     */
    public function getT3verMoveId()
    {
        return $this->t3verMoveId;
    }

    /**
     * Set sysLanguageUid
     *
     * @param integer $sysLanguageUid
     * @return Track
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;

        return $this;
    }

    /**
     * Get sysLanguageUid
     *
     * @return integer 
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * Set l10nParent
     *
     * @param integer $l10nParent
     * @return Track
     */
    public function setL10nParent($l10nParent)
    {
        $this->l10nParent = $l10nParent;

        return $this;
    }

    /**
     * Get l10nParent
     *
     * @return integer 
     */
    public function getL10nParent()
    {
        return $this->l10nParent;
    }

    /**
     * Set l10nDiffsource
     *
     * @param string $l10nDiffsource
     * @return Track
     */
    public function setL10nDiffsource($l10nDiffsource)
    {
        $this->l10nDiffsource = $l10nDiffsource;

        return $this;
    }

    /**
     * Get l10nDiffsource
     *
     * @return string 
     */
    public function getL10nDiffsource()
    {
        return $this->l10nDiffsource;
    }

    /**
     * Set relatedGenres
     *
     * @param string $relatedGenres
     * @return Track
     */
    public function setRelatedGenres($relatedGenres)
    {
        $this->relatedGenres = $relatedGenres;

        return $this;
    }

    /**
     * Get relatedGenres
     *
     * @return string 
     */
    public function getRelatedGenres()
    {
        return $this->relatedGenres;
    }

    /**
     * Set hardDelete
     *
     * @param boolean $hardDelete
     * @return Track
     */
    public function setHardDelete($hardDelete)
    {
        $this->hardDelete = $hardDelete;

        return $this;
    }

    /**
     * Get hardDelete
     *
     * @return boolean 
     */
    public function getHardDelete()
    {
        return $this->hardDelete;
    }

    /**
     * Set hardDeleteDate
     *
     * @param integer $hardDeleteDate
     * @return Track
     */
    public function setHardDeleteDate($hardDeleteDate)
    {
        $this->hardDeleteDate = $hardDeleteDate;

        return $this;
    }

    /**
     * Get hardDeleteDate
     *
     * @return integer 
     */
    public function getHardDeleteDate()
    {
        return $this->hardDeleteDate;
    }

    /**
     * @param $approval
     * @return $this
     */
    public function setApproval($approval){
        $this->approval = $approval;
        return $this;
    }

    /**
     * @return int
     */
    public function getApproval(){
        return $this->approval;
    }

    /**
     * @return int
     */
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * @param int $approvalDate
     */
    public function setApprovalDate($approvalDate)
    {
        $this->approvalDate = $approvalDate;
    }



    /**
     * Set fullPath
     *
     * @param string $fullPath
     * @return Track
     */
    public function setFullPath($fullPath)
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    /**
     * Get fullPath
     *
     * @return string 
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }
    public function getSearchTerm(){
        $text = $this->getFulltitle();
        return preg_replace('/\s?\([^)]+\)\s+/', '',$text);
    }
    public function getYear(){
        if( $this->getReleaseDate()->format('Y') <= 1970){
            return 2014;
        }
        return $this->getReleaseDate()->format('Y');
    }

    /**
     * Set playCount
     *
     * @param integer $playCount
     * @return Track
     */
    public function setPlayCount($playCount)
    {
        $this->playCount = $playCount;

        return $this;
    }

    /**
     * Get playCount
     *
     * @return integer 
     */
    public function getPlayCount()
    {
        return $this->playCount;
    }

    public function IncreasePlayCoun()
    {
        $this->playCount++;
    }
}
