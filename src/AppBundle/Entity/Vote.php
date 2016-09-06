<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * Vote (like, dislike)
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Vote {
    
    const DISLIKE_VALUE = -1;
    const LIKE_VALUE = 1;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="vote", type="integer")
     * 
     * @Assert\Range(
     *      max = 1,
     *      maxMessage = "Maximum vote is {{ limit }}"
     * )
     */
    private $vote;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="text")
     * 
     * @Assert\Choice(choices = {"like", "dislike"}, message = "Choose a valid type.")
     */
    private $type;
    
    /**
     * @var $updated
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;
    
    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="vote")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     */
    private $comment;
    
    
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
     * @return Rating
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
     * Set comment
     *
     * @param Comment $comment
     * @return Vote
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get vote
     *
     * @return integer 
     */
    public function getVote()
    {
        return $this->vote;
    }
    
     /**
     * Set Updated
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate()
     * 
     * @return Rating
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime('now');

        return $this;
    }
    
    /**
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    public function setLike () 
    {
        return $this->setVote(self::LIKE_VALUE);
    }
    
    public function setDislike () 
    {
        return $this->setVote(self::DISLIKE_VALUE);
    }
    
    /**
     * Set vote
     *
     * @param integer $vote
     * @return Vote
     */
    private function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }
    
    /**
     * Set type
     *
     * @param $type
     * @return Vote
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Comment
     */
    public function getType()
    {
        return $this->type;
    }
}
