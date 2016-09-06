<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FavouriteComposition
 *
 * @ORM\Table(name="composition_favourite")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FavouriteCompositionRepository")
 */
class FavouriteComposition
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
     *
     * @var type 
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="favouriteComposition")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Composition")
     * @ORM\JoinColumn(name="composition_id", referencedColumnName="id")
     */
    private $composition;
    
    /**
     *@ORM\Column(type="string", length=500, nullable=true)
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
     * @param \UserBundle\Entity\User $user
     * @return FavouriteComposition
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set composition
     *
     * @param \AppBundle\Entity\Composition $composition
     * @return FavouriteComposition
     */
    public function setComposition(\AppBundle\Entity\Composition $composition = null)
    {
        $this->composition = $composition;

        return $this;
    }

    /**
     * Get composition
     *
     * @return \AppBundle\Entity\Composition 
     */
    public function getComposition()
    {
        return $this->composition;
    }
    
    /**
     * Set comment
     *
     * @param string $comment
     * @return FavouriteComposition
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $comment;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }
}
