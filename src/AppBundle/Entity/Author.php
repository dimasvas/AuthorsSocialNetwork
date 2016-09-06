<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Author
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AuthorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Author
{
    /**
     * Items per page (pagination)
     */
    const DEFAULT_NUM_ITEMS = 20;
    
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
     * /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", mappedBy="author")
     **/
    private $user;

    /**
     * @var
     * @ORM\Column(name="in_sandbox", type="boolean")
     */
    private $inSandbox;

    /**
     *
     * @var 
     * @ORM\Column(name="average_book_rate", type="integer")
     */
    private $averageBookRate;
    
    /**
     *
     * @var 
     * @ORM\Column(name="rates_num", type="integer")
     */
    private $rates_num;
    
    /**
     *
     * @var
     * @ORM\Column(name="total_compositions", type="integer")
     */
    private $totalCompositions;
            
    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Composition", mappedBy="author")
     */
    private $composition;

    public function __construct()
    {
        $this->composition = new ArrayCollection();
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
     * Set user
     *
     * @param User $user
     * @return Author
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
     *  Init inSandbox value
     *
     *  @ORM\PrePersist
     */
    public function initializeInSandbox()
    {
        $this->inSandbox =  false;
    }

    /**
     * Set inSandbox
     *
     * @param boolean $inSandbox
     * @return Author
     */
    public function setInSandbox($inSandbox)
    {
        $this->inSandbox = $inSandbox;

        return $this;
    }

    /**
     * Get inSandbox
     *
     * @return boolean 
     */
    public function getInSandbox()
    {
        return $this->inSandbox;
    }

    /**
     * Add composition
     *
     * @param Composition $composition
     * @return Author
     */
    public function addComposition(Composition $composition)
    {
        $this->composition[] = $composition;

        return $this;
    }

    /**
     * Remove composition
     *
     * @param \AppBundle\Entity\Composition $composition
     */
    public function removeComposition(\AppBundle\Entity\Composition $composition)
    {
        $this->composition->removeElement($composition);
    }

    /**
     * Get composition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * Set averageBookRate
     *
     * @param integer $averageBookRate
     * @return Author
     */
    public function setAverageBookRate($averageBookRate)
    {
        $this->averageBookRate = $averageBookRate;

        return $this;
    }

    /**
     * Get averageBookRate
     *
     * @return integer 
     */
    public function getAverageBookRate()
    {
        return $this->averageBookRate;
    }
    
     /**
     * Set averageBookRate
     *
     * @param integer setRatesNum
     * @return Author
     */
    public function setRatesNum($num)
    {
        $this->rates_num = $num;

        return $this;
    }

    /**
     * Get getRatesNum
     *
     * @return integer 
     */
    public function getRatesNum()
    {
        return $this->rates_num;
    }
    
    public function incrementRatesNum()
    {
        return $this->rates_num++;
    }

    /**
     * Set totalCompositions
     *
     * @param integer $totalCompositions
     * @return Author
     */
    public function setTotalCompositions($totalCompositions)
    {
        $this->totalCompositions = $totalCompositions;

        return $this;
    }
    
    /**
     * Increase totalCompositions
     *
     * @param integer $totalCompositions
     * @return Author
     */
    public function incrementTotalCompositions()
    {
        $this->totalCompositions++;

        return $this;
    }
    
    /**
     * Decrease totalCompositions
     *
     * @param integer $totalCompositions
     * @return Author
     */
    public function decreaseTotalCompositions()
    {
        if($this->totalCompositions > 0){
            $this->totalCompositions--;
        }

        return $this;
    }

    /**
     * Get totalCompositions
     *
     * @return integer 
     */
    public function getTotalCompositions()
    {
        return $this->totalCompositions;
    }
     
    /**
     * Initialize averageBookRate property
     * 
     * @ORM\PrePersist
     */
    public function initAverageBookRate()
    {
        if(!$this->id){
            $this->averageBookRate = 0;
        }
    }
    
    /**
     * Initialize totalCompositions property
     * 
     * @ORM\PrePersist
     */
    public function initTotalCompositions()
    {
        if(!$this->id){
            $this->totalCompositions = 0;
        }
    }
    
    /**
     * Initialize totalCompositions property
     * 
     * @ORM\PrePersist
     */
    public function initRatesNum()
    {
        if(!$this->id){
            $this->rates_num = 0;
        }
    }
}
