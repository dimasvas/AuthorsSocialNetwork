<?php

namespace AppBundle\Entity\CompositionTypes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="type_lyric")
 * @ORM\Entity
 */
class Lyric extends BaseType
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var type 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Composition", mappedBy="lyric")
     */
    private $composition;
        
    /**
     * Set content
     *
     * @param string $content
     * @return Lyric
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
}