<?php

namespace AppBundle\Entity\CompositionTypes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="type_music")
 * @ORM\Entity
 */
class Music extends BaseType
{
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $content;
    
      
    /**
     * @var type 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Composition", mappedBy="music")
     */
    private $composition;
    
    /**
     * Set content
     *
     * @param string $content
     * @return Scenario
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