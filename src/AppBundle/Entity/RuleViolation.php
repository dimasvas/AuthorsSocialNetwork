<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommentViolation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RuleViolationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RuleViolation
{
    const OPEN_STATUS = 1;
    const IN_PROCESS_STATUS = 2;
    const CLOSED_STATUS = 3;
    
    const COMMENT_TYPE = 1;
    const COMPOSITION_TYPE = 2;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var \stdClass
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     */
    private $reporter;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
    /**
     * @var string
     *
     * @ORM\Column(name="manger_comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var type 
     * 
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @var type 
     * 
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
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
     * Set comment
     *
     * @param \stdClass $comment
     * @return CommentViolation
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \stdClass 
     */
    public function getComment()
    {
        return $this->comment;
    }
    

    /**
     * Set reporter
     *
     * @param \stdClass $reporter
     * @return CommentViolation
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \stdClass 
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return CommentViolation
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
     * Set content
     *
     * @param string $content
     * @return CommentViolation
     */
    public function setEntityId($id)
    {
        $this->entityId = $id;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
    
    /**
     * Set status
     *
     * @return CommentViolation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set content
     *
     * @param string $content
     * @return CommentViolation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return TuleViolation
     * 
     * @ORM\PrePersist
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
}
