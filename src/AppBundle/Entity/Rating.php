<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Composition;
use UserBundle\Entity\User;

/**
 * Rating
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RaitingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Rating
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
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer")
     */
    private $rate;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Composition")
     * @ORM\JoinColumn(name="composition_id", referencedColumnName="id")
     */
    private $composition;
    
    /**
     * @var $updated
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

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
     * Set rate
     *
     * @param integer $rate
     * @return Rating
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer 
     */
    public function getRate()
    {
        return $this->rate;
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
     * Set Composition
     *
     * @param Composition $composition
     * @return Rating
     */
    public function setComposition(Composition $composition)
    {
        $this->composition = $composition;

        return $this;
    }

    /**
     * Get composition
     *
     * @return Usercomposition
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
