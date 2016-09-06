<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 * @JMS\ExclusionPolicy("all")
 */
class Message
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
     * @JMS\Groups({"message"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     * 
     * @Assert\NotBlank(message="comment.body_required")
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"message"})
     */
    private $body;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Type("UserBundle\Entity\User")
     * @JMS\Groups({"message"})
     */
    private $sender;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Type("UserBundle\Entity\User")
     * @JMS\Groups({"message"})
     */
    private $recipient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sender_viewed", type="boolean")
     * 
     */
    private $senderViewed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recipient_viewed", type="boolean")
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\Groups({"message"})
     */
    private $recipientViewed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sender_viewed_date", type="datetime", nullable=true)
     */
    private $senderViewedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recipient_viewed_date", type="datetime", nullable=true)
     */
    private $recipientViewedDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sender_deleted", type="boolean")
     */
    private $senderDeleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recipient_deleted", type="boolean")
     */
    private $recipientDeleted;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="MessageThread")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Groups({"message"})
     * @JMS\Type("integer")
     * @JMS\Accessor(getter="getThreadId") */
    private $thread;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * 
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @JMS\Groups({"message"})
     */
    private $created;

    
    /**
     *  Set Default Data on Persist
     * 
     * @ORM\PrePersist
     */
    public function initDefaultData(){
        if(!$this->getId()){
            $this->setRecipientDeleted(false);
            $this->setRecipientViewed(false);
            $this->setSenderDeleted(false);
            $this->setSenderViewed(false);
        }
    }
    
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
     * Set body
     *
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set sender
     *
     * @param \stdClass $sender
     * @return Message
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \stdClass 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set recipient
     *
     * @param \stdClass $recipient
     * @return Message
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return \stdClass 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set senderViewed
     *
     * @param boolean $senderViewed
     * @return Message
     */
    public function setSenderViewed($senderViewed)
    {
        $this->senderViewed = $senderViewed;

        return $this;
    }

    /**
     * Get senderViewed
     *
     * @return boolean 
     */
    public function getSenderViewed()
    {
        return $this->senderViewed;
    }

    /**
     * Set recipientViewed
     *
     * @param boolean $recipientViewed
     * @return Message
     */
    public function setRecipientViewed($recipientViewed)
    {
        $this->recipientViewed = $recipientViewed;

        return $this;
    }

    /**
     * Get recipientViewed
     *
     * @return boolean 
     */
    public function getRecipientViewed()
    {
        return $this->recipientViewed;
    }

    /**
     * Set senderViewedDate
     *
     * @param \DateTime $senderViewedDate
     * @return Message
     */
    public function setSenderViewedDate($senderViewedDate)
    {
        $this->senderViewedDate = $senderViewedDate;

        return $this;
    }

    /**
     * Get senderViewedDate
     *
     * @return \DateTime 
     */
    public function getSenderViewedDate()
    {
        return $this->senderViewedDate;
    }

    /**
     * Set recipientViewedDate
     *
     * @param \DateTime $recipientViewedDate
     * @return Message
     */
    public function setRecipientViewedDate($recipientViewedDate)
    {
        $this->recipientViewedDate = $recipientViewedDate;

        return $this;
    }

    /**
     * Get recipientViewedDate
     *
     * @return \DateTime 
     */
    public function getRecipientViewedDate()
    {
        return $this->recipientViewedDate;
    }

    /**
     * Set senderDeleted
     *
     * @param boolean $senderDeleted
     * @return Message
     */
    public function setSenderDeleted($senderDeleted)
    {
        $this->senderDeleted = $senderDeleted;

        return $this;
    }

    /**
     * Get senderDeleted
     *
     * @return boolean 
     */
    public function getSenderDeleted()
    {
        return $this->senderDeleted;
    }

    /**
     * Set recipientDeleted
     *
     * @param boolean $recipientDeleted
     * @return Message
     */
    public function setRecipientDeleted($recipientDeleted)
    {
        $this->recipientDeleted = $recipientDeleted;

        return $this;
    }

    /**
     * Get recipientDeleted
     *
     * @return boolean 
     */
    public function getRecipientDeleted()
    {
        return $this->recipientDeleted;
    }

    /**
     * Set thread
     *
     * @param \stdClass $thread
     * @return Message
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \stdClass 
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set created
     *
     * @return Message
     */
    public function setCreated($date)
    {
        if($this->id == null){
            $this->created = $date;
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
    
    public function getThreadId() 
    {
        return $this->thread->getId();
    }
    
}
