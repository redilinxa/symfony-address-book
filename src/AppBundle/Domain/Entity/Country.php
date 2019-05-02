<?php


namespace AppBundle\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country
 *
 * @ORM\Table(name="countries_cities")
 * @ORM\Entity(repositoryClass="AppBundle\Domain\Repository\CountryRepository")
 */
class Country
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Domain\Entity\Country", mappedBy="country")
     */
    private $cities;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)
     */
    private $zip;


    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="AppBundle\Domain\Entity\Country", inversedBy="cities", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Domain\Entity\Person", mappedBy="city", cascade={"remove"})
     */
    private $persons;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->persons = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @param string $cities
     */
    public function setCities($cities)
    {
        $this->cities = $cities;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * @param mixed $persons
     */
    public function setPersons($persons)
    {
        $this->persons = $persons;
    }

    public function __toString(){
        return $this->getName();
    }
}