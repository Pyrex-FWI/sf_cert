<?php

namespace Cpyree\CustomerBundle\Entity;

use Cpyree\CustomerBundle\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="customer_address")
 * @ORM\Entity(repositoryClass="Cpyree\CustomerBundle\Entity\AddressRepository")
 */
class Address
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nameLastName", type="string", length=255)
     */
    private $nameLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="adress1", type="string", length=255)
     */
    private $adress1;

    /**
     * @var string
     *
     * @ORM\Column(name="adress2", type="string", length=255)
     */
    private $adress2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=100)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="stateOrRegion", type="string", length=100)
     */
    private $stateOrRegion;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=20)
     */
    private $postalCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="country", type="integer")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;


    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="address")
     */
    private $customer;
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
     * Set name
     *
     * @param string $name
     * @return Adress
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nameLastName
     *
     * @param string $nameLastName
     * @return Adress
     */
    public function setNameLastName($nameLastName)
    {
        $this->nameLastName = $nameLastName;

        return $this;
    }

    /**
     * Get nameLastName
     *
     * @return string 
     */
    public function getNameLastName()
    {
        return $this->nameLastName;
    }

    /**
     * Set adress1
     *
     * @param string $adress1
     * @return Adress
     */
    public function setAdress1($adress1)
    {
        $this->adress1 = $adress1;

        return $this;
    }

    /**
     * Get adress1
     *
     * @return string 
     */
    public function getAdress1()
    {
        return $this->adress1;
    }

    /**
     * Set adress2
     *
     * @param string $adress2
     * @return Adress
     */
    public function setAdress2($adress2)
    {
        $this->adress2 = $adress2;

        return $this;
    }

    /**
     * Get adress2
     *
     * @return string 
     */
    public function getAdress2()
    {
        return $this->adress2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Adress
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set stateOrRegion
     *
     * @param string $stateOrRegion
     * @return Adress
     */
    public function setStateOrRegion($stateOrRegion)
    {
        $this->stateOrRegion = $stateOrRegion;

        return $this;
    }

    /**
     * Get stateOrRegion
     *
     * @return string 
     */
    public function getStateOrRegion()
    {
        return $this->stateOrRegion;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return Adress
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set country
     *
     * @param integer $country
     * @return Adress
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return integer 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Adress
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set customer
     *
     * @param \Cpyree\CustomerBundle\Entity\Customer $customer
     * @return Address
     */
    public function setCustomer(\Cpyree\CustomerBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Cpyree\CustomerBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
