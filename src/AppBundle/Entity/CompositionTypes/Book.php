<?php

namespace AppBundle\Entity\CompositionTypes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="type_book")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Book extends BaseType
{
    /**
     * @var
     *
     * @ORM\Column(name="num_pages", type="integer")
     */
    private $pages;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var type 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Composition", mappedBy="book")
     */
    private $composition;

    /**
     * Set pages
     *
     * @param integer $pages
     * @return Book
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }
    
    /**
     * Get pages
     *
     * @return integer 
     */
    public function getPages()
    {
        return $this->pages;
    }
    
    /**
     * Set content
     *
     * @param string $content
     * @return Book
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get content
     *
     * @return Composition
     */
    public function getComposition()
    {
        return $this->composition;
    }
    
    /**
     * Set content
     *
     * @param Composition $compostition
     * @return Book
     */
    public function setCompostition($compostition)
    {
        $this->composition = $compostition;

        return $this;
    }
    
    /**
     * Initialize blocked status
     *
     * @ORM\PrePersist
     */
    public function initPages()
    {
        if ($this->pages == null) {
            $this->pages =  0;
        }
    }
}
