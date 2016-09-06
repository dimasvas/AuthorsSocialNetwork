<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feedback
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FeedbackRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Feedback
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    private $closed;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_remark", type="text", nullable=true)
     */
    private $adminRemark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="reply", type="text", nullable=true)
     */
    private $reply;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="has_reply", type="boolean")
     */
    private $has_reply;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reply_date", type="datetime", nullable=true)
     */
    private $replyDate;


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
     * @return Feedback
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
     * Set email
     *
     * @param string $email
     * @return Feedback
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     * @return Feedback
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean 
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Feedback
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set adminRemark
     *
     * @param string $adminRemark
     * @return Feedback
     */
    public function setAdminRemark($adminRemark)
    {
        $this->adminRemark = $adminRemark;

        return $this;
    }

    /**
     * Get adminRemark
     *
     * @return string 
     */
    public function getAdminRemark()
    {
        return $this->adminRemark;
    }

    /**
     * Get created
     * @ORM\PrePersist
     * 
     * @return Feedback
     */
    public function setCreated()
    {
        if(!$this->getId()) {
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

    /**
     * Set reply
     *
     * @param string $reply
     * @return Feedback
     */
    public function setReply($reply)
    {
        $this->reply = $reply;

        return $this;
    }

    /**
     * Get reply
     *
     * @return string 
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set has_reply
     *
     * @param boolean $hasReply
     * @return Feedback
     */
    public function setHasReply($hasReply)
    {
        $this->has_reply = $hasReply;

        return $this;
    }

    /**
     * Get has_reply
     *
     * @return boolean 
     */
    public function getHasReply()
    {
        return $this->has_reply;
    }

    /**
     * Set replyDate
     *
     * @param \DateTime $replyDate
     * @return Feedback
     */
    public function setReplyDate($replyDate)
    {
        $this->replyDate = $replyDate;

        return $this;
    }

    /**
     * Get replyDate
     *
     * @return \DateTime 
     */
    public function getReplyDate()
    {
        return $this->replyDate;
    }
    
    /**
     * Initialize the closed value
     * @ORM\PrePersist
     */
    public function  initClosed(){
        if(!$this->getClosed()){
            $this->setClosed(false);
        }
    }
    
    /**
     * Initialize has reply value
     * @ORM\PrePersist
     */
    public function  initHasReply(){
        if(!$this->getId()){
            $this->setHasReply(false);
        }
    }
}
