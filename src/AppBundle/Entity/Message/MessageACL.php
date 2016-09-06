<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * MessageACL
 *
 * @ORM\Table(name="message_acl")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MessageACLRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 * @JMS\ExclusionPolicy("all")
 */
class MessageACL
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
     * @JMS\Groups({"default"})
     */
    private $id;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_user", referencedColumnName="id")
     */
    private $owner;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="blocked_user", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Type("UserBundle\Entity\User")
     * @JMS\Groups({"default"})
     */
    private $blocked;
    
    /**
     * @var $created
     *
     * @ORM\Column(type="datetime")
     * 
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @JMS\Groups({"default"})
     */
    private $created;
    
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
     * Set owner
     *
     * @param \stdClass $owner
     * @return MessageACL
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \stdClass 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set blockedUsers
     *
     * @param array $blockedUsers
     * @return MessageACL
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blockedUsers
     *
     * @return ArrayCollection 
     */
    public function getBlocked()
    {
        return $this->blocked;
    }
    
    /**
     * Set created
     * 
     * @ORM\PrePersist
     * @return CompositionSubscription
     */
    public function setCreated()
    {
        if($this->id == null){
            $this->created = new \DateTime('now');
        }
        
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
}
