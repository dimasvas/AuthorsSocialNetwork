<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * CompositionSubscription
 *
 * @ORM\Table(name="composition_subscribtion")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CompositionSubscriptionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CompositionSubscription
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Composition")
     * @ORM\JoinColumn(name="composition_id", referencedColumnName="id")
     **/
    private  $composition;

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
     * @return CompositionSubscription
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
     * Set composition
     *
     * @param Composition $composition
     * @return CompositionSubscription
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
}
