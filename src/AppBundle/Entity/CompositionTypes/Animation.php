<?php

namespace AppBundle\Entity\CompositionTypes;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="type_animation")
 * @ORM\Entity
 */
class Animation extends BaseType
{
    /**
     * @var type 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Composition", mappedBy="animation")
     */
    private $composition;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $content;
    
        
    /**
     * Set content
     *
     * @param string $content
     * @return Animation
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