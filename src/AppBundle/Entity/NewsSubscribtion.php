<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rating
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class NewsSubscribtion 
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
     * @var type 
     * 
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Email not Blank")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;
    
    /**
     * @var $created
     *
     * @ORM\Column(type="string")
     */
    private $hash;
    
    /**
     * @var $created
     *
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * Non mapped field.
     * @var type 
     */
    private $salt = 'oX86s867mG';

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
     * Set email
     *
     * @param string $email
     *
     * @return NewsSubscribtion
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
     * Set hash
     *
     * @return NewsSubscribtion
     */
    public function setHash()
    {
        $secureSalt = $this->getSalt() . $this->getEmail();
        
        $this->hash = \sha1($secureSalt);

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set created
     * 
     * @return NewsSubscribtion
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
     * @ORM\PrePersist
     */
    public function init () 
    {
        if(!$this->getId()) {
            $this->setCreated();
            $this->setHash();
        }
    }
    
}
