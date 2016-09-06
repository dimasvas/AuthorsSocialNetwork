<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Composition;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Comment
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
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Composition")
     * @ORM\JoinColumn(name="composition_id", referencedColumnName="id")
     */
    private $composition;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Type("UserBundle\Entity\User")
     * @JMS\Groups({"list", "admin-search"})
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_composition_owner", type="boolean")
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $isCompositionOwner;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_approved", type="boolean")
     */
    private $isApproved;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_blocked", type="boolean")
     */
    private $isBlocked;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="has_response", type="boolean")
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $hasResponse;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="response_count", type="integer")
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $responseCount;
    
    /**
     * @ORM\OneToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="reply_to", referencedColumnName="id")
     **/
    private $replyTo = null;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     **/
    private $children;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="totalVote", type="integer")
     */
    private $totalVote;
    
    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="comment")
     */
    private $vote;
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children",  cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;


    public function __construct() {
        $this->children = new ArrayCollection();
        $this->vote = new ArrayCollection();
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
     * Set composition
     *
     * @param integer $composition
     * @return Comment
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

    /**
     * Set user
     *
     * @param User $user
     * @return Comment
     */
    public function setUser(User $user)
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
     * Set isCompositionOwner
     *
     * @param boolean $isCompositionOwner
     * @return Comment
     */
    public function setIsCompositionOwner($isCompositionOwner)
    {
        $this->isCompositionOwner = $isCompositionOwner;

        return $this;
    }

    /**
     * Get isCompositionOwner
     *
     * @return boolean 
     */
    public function getIsCompositionOwner()
    {
        return $this->isCompositionOwner;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Comment
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
     * Set isApproved
     *
     * @param boolean $isApproved
     * @return Comment
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get isApproved
     *
     * @return boolean 
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set isBlocked
     *
     * @param boolean $isBlocked
     * @return Comment
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return boolean 
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

     /**
     * Set hasResponse
     *
     * @param boolean $hasResponse
     * @return Comment
     */
    public function setResponse($hasResponse)
    {
        $this->hasResponse = $hasResponse;

        return $this;
    }

    /**
     * Get hasResponse
     *
     * @return boolean 
     */
    public function hasResponse()
    {
        return $this->hasResponse;
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Comment
     * 
     * @ORM\PreUpdate
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime('now');

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set hasResponse
     *
     * @param boolean $hasResponse
     * @return Comment
     */
    public function setHasResponse($hasResponse)
    {
        $this->hasResponse = $hasResponse;

        return $this;
    }

    /**
     * Get hasResponse
     *
     * @return boolean 
     */
    public function getHasResponse()
    {
        return $this->hasResponse;
    }

    /**
     * Set replyTo
     *
     * @param \CommentBundle\Entity\Comment $replyTo
     * @return Comment
     */
    public function setReplyTo(Comment $replyTo = null)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * Get replyTo
     *
     * @return \CommentBundle\Entity\Comment 
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Add children
     *
     * @param AppBundle\Entity\Comment $children
     * @return Comment
     */
    public function addChild(Comment $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \CommentBundle\Entity\Comment $children
     */
    public function removeChild(Comment $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Add vote
     *
     * @param AppBundle\Entity\Vote $vote
     * @return Vote
     */
    public function addVote(Vote $vote)
    {
        $this->vote[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param AppBundle\Entity\Vote cvote
     */
    public function removeVote(Vote $vote)
    {
        $this->vote->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set parent
     *
     * @param \CommentBundle\Entity\Comment $parent
     * @return Comment
     */
    public function setParent(Comment $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Comment 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set responseCount
     *
     * @param integer $responseCount
     * @return Comment
     */
    public function setResponseCount($responseCount)
    {
        $this->responseCount = $responseCount;

        return $this;
    }

    /**
     * Get responseCount
     *
     * @return integer 
     */
    public function getResponseCount()
    {
        return $this->responseCount;
    }
    
     /**
     * Set responseCount
     *
     * @param integer $voteValue
     * @return Comment
     */
    public function setTotalVote($voteValue)
    {
        $this->totalVote = $voteValue;

        return $this;
    }

    /**
     * Get totalVote
     *
     * @return integer 
     */
    public function getTotalVote()
    {
        return $this->totalVote;
    }
    
    /**
     * 
     * @return \AppBundle\Entity\Comment
     */
    public function increaseResponseCount()
    {
        $this->responseCount++;
        
        return $this;
    }
    
    /**
     * 
     * @return \AppBundle\Entity\Comment
     */
    public function decreaseResponseCount()
    {
        if($this->responseCount > 0){
           $this->responseCount--;   
        }
        
        return $this;
    }
    
    public function increaseVote () 
    {
        $this->totalVote++;
        
        return $this;
    }
    
    public function decreaseVote () 
    {
        $this->totalVote--;
        
        return $this;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function init () {
        if(!$this->getId()) {
            $this->responseCount =  0;
            $this->totalVote = 0;
            $this->hasResponse =  false;
            $this->isApproved =  false;
            $this->isBlocked =  false;
            $this->created = new \DateTime('now');
            $this->updated = new \DateTime('now');
        }
    }
}
