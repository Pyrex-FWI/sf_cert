<?php

namespace Cpyree\DigitalDjPoolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trackgenre
 *
 * @ORM\Table(name="tx_digitaldjpool_domain_model_trackgenre", indexes={@ORM\Index(name="parent", columns={"pid"}), @ORM\Index(name="t3ver_oid", columns={"t3ver_oid", "t3ver_wsid"}), @ORM\Index(name="language", columns={"l10n_parent", "sys_language_uid"})})
 * @ORM\Entity(repositoryClass="Cpyree\DigitalDjPoolBundle\Entity\TrackgenreRepository")
 */
class Trackgenre
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name = '';

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
     * @ORM\Column(name="t3ver_wsid", type="integer", nullable=false)
     */
    private $t3verWsid = '0';

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


}
