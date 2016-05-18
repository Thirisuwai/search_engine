<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class CrawledPage
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="CrawledPage")
 */
class CrawledPage
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;

    /**
     * @var string $title
     *
     * @ORM\Column(name="main_page_title", type="string", length=255)
     */
    protected $title;

    /**
     * @var integer $viewCount
     *
     * @ORM\Column(name="view_count", type="integer")
     */
    protected $viewCount;

    /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var string $paragraph
     *
     * @ORM\Column(name="paragraph", type="string", length=255)
     */
    protected $paragraph;

    /**
     * @var \DateTime $createdAt
     *
     * @ORm\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    ///////////////////////
    //  Methods
    ///////////////////////

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    /**
     * Get viewCount
     *
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }


    /**
     * Set viewCount
     *
     * @param int $viewCount
     * @return $this
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
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
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * Get headerParagraph
     *
     * @return string
     */
    public function getParagraph()
    {
        return $this->paragraph;
    }


    /**
     * Set paragraph
     *
     * @param string $paragraph
     * @return $this
     */
    public function setParagraph($paragraph)
    {
        $this->paragraph = $paragraph;
        return $this;
    }


    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}