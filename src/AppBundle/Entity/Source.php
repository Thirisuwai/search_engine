<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Source
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="Source")
 */
class Source
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $sortSeq
     *
     * @ORM\Column(name="sortSeq", type="integer")
     */
    protected $sortSeq;

    /**
     * Status => active (1), inactive (0)
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;


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
     * Get sortSeq
     *
     * @return int
     */
    public function getSortSeq()
    {
        return $this->sortSeq;
    }


    /**
     * Set sortSeq
     *
     * @param int $sortSeq
     * @return Source
     */
    public function setSortSeq($sortSeq)
    {
        $this->sortSeq = $sortSeq;
        return $this;
    }


    /**
     * Set status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set status
     * @param int $status
     * @return Source
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Set url
     *
     * @param string $url
     * @return Source
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}