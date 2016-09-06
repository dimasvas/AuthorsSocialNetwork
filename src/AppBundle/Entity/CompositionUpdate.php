<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * CompositionSubscription
 *
 * @ORM\Table(name="composition_update", indexes={@ORM\Index(name="user_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CompositionUpdateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CompositionUpdate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * //GeneratedValue(strategy="AUTO")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;
    
    /**
     * @ORM\Column(name="composition_id", type="integer")
     */
    private $compositionId;
    
    /**
     * @ORM\Column(name="composition_title", nullable=false)
     */
    private $compositionTitle;
    
    /**
     * @ORM\Column(name="author_name", nullable=false)
     */
    private $authorName;
    
    /**
     *@ORM\Column(type="string", length=255)
     */
    private $message;
    
    /**
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $viewed;

    /**
     * @var $created
     *
     * @ORM\Column(type="datetime")
     * @ORM\OrderBy({"name" = "ASC"})
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
     * Set user
     *
     * @param User $user
     * @return CompositionUpdate
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Get created
     * @ORM\PrePersist
     * @return CompositionUpdate
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
     * Set viewed
     *
     * @param boolean $viewed
     * @return CompositionUpdate
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get viewed
     *
     * @return boolean 
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Set messsage
     *
     * @param string $messsage
     * @return CompositionUpdate
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get messsage
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Initialize viewed status
     *
     * @ORM\PrePersist
     */
    public function initViewed()
    {
        if ($this->id == null) {
            $this->viewed =  false;
        }
    }

    /**
     * Set compositionId
     *
     * @param integer $compositionId
     * @return CompositionUpdate
     */
    public function setCompositionId($compositionId)
    {
        $this->compositionId = $compositionId;

        return $this;
    }

    /**
     * Get compositionId
     *
     * @return integer 
     */
    public function getCompositionId()
    {
        return $this->compositionId;
    }

    /**
     * Set compositionTitle
     *
     * @param boolean $compositionTitle
     * @return CompositionUpdate
     */
    public function setCompositionTitle($compositionTitle)
    {
        $this->compositionTitle = $compositionTitle;

        return $this;
    }

    /**
     * Get compositionTitle
     *
     * @return boolean 
     */
    public function getCompositionTitle()
    {
        return $this->compositionTitle;
    }

    /**
     * Set authorName
     *
     * @param boolean $authorName
     * @return CompositionUpdate
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get authorName
     *
     * @return boolean 
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }
}
