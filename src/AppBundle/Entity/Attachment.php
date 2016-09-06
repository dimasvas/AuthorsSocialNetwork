<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use UserBundle\Entity\User;
use AppBundle\Entity\Composition;

/**
 * Attachment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AttachmentRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 * @JMS\ExclusionPolicy("all")
 */
class Attachment
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
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $file;

    /**
     * @var type
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * 
     * @JMS\Expose
     * @JMS\Type("DateTime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="Composition", inversedBy="attachments")
     * @ORM\JoinColumn(name="composition_id", referencedColumnName="id")
     */
    private $composition;


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
     * @param string $type
     * @return Attachment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Attachment
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set $file
     *
     * @param string $file
     * @return Attachment
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get $file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return Attachment
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
     /**
     * Set created
     * 
     * @ORM\PrePersist
     * @return CompositionSubscription
     */
    public function setCreated()
    {
        $this->created = new \DateTime('now');
        
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set composition
     *
     * @param Composition $composition
     * @return Attachment
     */
    public function setComposition(Composition $composition = null)
    {
        $this->composition = $composition;

        return $this;
    }

    /**
     * Get composition
     *
     * @return Composition 
     */
    public function getComposition()
    {
        return $this->composition;
    }
}
