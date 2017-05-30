<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="sexe", type="integer", nullable=true)
     */
    private $sexe;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant", type="integer", nullable=true)
     */
    private $enfant;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant_0_5", type="integer", nullable=true)
     */
    private $enfant_0_5;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant_6_11", type="integer", nullable=true)
     */
    private $enfant_6_11;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant_12_16", type="integer", nullable=true)
     */
    private $enfant_12_16;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant_16_18", type="integer", nullable=true)
     */
    private $enfant_16_18;

    /**
     * @var int
     *
     * @ORM\Column(name="enfant_18", type="integer", nullable=true)
     */
    private $enfant_18;


    /**
     * @var int
     *
     * @ORM\Column(name="situation", type="integer", nullable=true)
     */
    private $situation;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="tombola", type="integer")
     */
    private $tombola;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Client
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Client
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sexe
     *
     * @param integer $sexe
     *
     * @return Client
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return int
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Client
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return Client
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set enfant
     *
     * @param integer $enfant
     *
     * @return Client
     */
    public function setEnfant($enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * Get enfant
     *
     * @return int
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * Set enfantAge
     *
     * @param integer $enfantAge
     *
     * @return Client
     */
    public function setEnfantAge($enfantAge)
    {
        $this->enfantAge = $enfantAge;

        return $this;
    }

    /**
     * Get enfantAge
     *
     * @return int
     */
    public function getEnfantAge()
    {
        return $this->enfantAge;
    }

    /**
     * Set situation
     *
     * @param integer $situation
     *
     * @return Client
     */
    public function setSituation($situation)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation
     *
     * @return int
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Client
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tombola
     *
     * @param integer $tombola
     *
     * @return Client
     */
    public function setTombola($tombola)
    {
        $this->tombola = $tombola;

        return $this;
    }

    /**
     * Get tombola
     *
     * @return integer
     */
    public function getTombola()
    {
        return $this->tombola;
    }

    /**
     * Set enfant05
     *
     * @param integer $enfant05
     *
     * @return Client
     */
    public function setEnfant05($enfant05)
    {
        $this->enfant_0_5 = $enfant05;

        return $this;
    }

    /**
     * Get enfant05
     *
     * @return integer
     */
    public function getEnfant05()
    {
        return $this->enfant_0_5;
    }

    /**
     * Set enfant611
     *
     * @param integer $enfant611
     *
     * @return Client
     */
    public function setEnfant611($enfant611)
    {
        $this->enfant_6_11 = $enfant611;

        return $this;
    }

    /**
     * Get enfant611
     *
     * @return integer
     */
    public function getEnfant611()
    {
        return $this->enfant_6_11;
    }

    /**
     * Set enfant1216
     *
     * @param integer $enfant1216
     *
     * @return Client
     */
    public function setEnfant1216($enfant1216)
    {
        $this->enfant_12_16 = $enfant1216;

        return $this;
    }

    /**
     * Get enfant1216
     *
     * @return integer
     */
    public function getEnfant1216()
    {
        return $this->enfant_12_16;
    }

    /**
     * Set enfant1618
     *
     * @param integer $enfant1618
     *
     * @return Client
     */
    public function setEnfant1618($enfant1618)
    {
        $this->enfant_16_18 = $enfant1618;

        return $this;
    }

    /**
     * Get enfant1618
     *
     * @return integer
     */
    public function getEnfant1618()
    {
        return $this->enfant_16_18;
    }

    /**
     * Set enfant18
     *
     * @param integer $enfant18
     *
     * @return Client
     */
    public function setEnfant18($enfant18)
    {
        $this->enfant_18 = $enfant18;

        return $this;
    }

    /**
     * Get enfant18
     *
     * @return integer
     */
    public function getEnfant18()
    {
        return $this->enfant_18;
    }
}
