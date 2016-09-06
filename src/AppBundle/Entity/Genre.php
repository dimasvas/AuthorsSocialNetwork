<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use JMS\Serializer\Annotation as JMS;

/**
 * Genre
 *
 * @ORM\Table(name="composition_genre")
 * @ORM\Entity
 * 
 * @JMS\ExclusionPolicy("all")
 */
class Genre implements Translatable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=20)
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"admin-search"})
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $alias;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompositionCategory", inversedBy="genres")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;


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
     * @return Genre
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
     * Set composition_type
     *
     * @param CompositionCategory $category
     * @return Genre
     */
    public function setCategory(CompositionCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get composition_type
     *
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Set alias
     *
     * @param string $alias
     * @return Genre
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function __toString()
    {
        return $this->name;
    }
}
