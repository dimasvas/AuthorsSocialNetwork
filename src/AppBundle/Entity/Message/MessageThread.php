<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * MessageThread
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\MessageThreadRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MessageThread
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="first_user_id", referencedColumnName="id")
     */
    private $firstParticipant;
    
    /**
     *
     * @var type 
     * 
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="second_user_id", referencedColumnName="id")
     */
    private $secondParticipant; 
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_msg_created", type="datetime")
     */
    private $lastMessageCreated;
    
    /**
     *
     * @var type 
     * 
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="last_message_id", referencedColumnName="id")
     */
    private $lastMessage;
    
    
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
     * Set created
     * 
     * @return MessageThread
     */
    public function setLastMessageCreated($date)
    {
        $this->lastMessageCreated = $date;
        
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getLastMessageCreated()
    {
        return $this->lastMessageCreated;
    }

    /**
     * Set lastMessage
     *
     * @param Message $lastMessage
     * @return MessageThread
     */
    public function setLastMessage(Message $lastMessage)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage
     *
     * @return Message 
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Get First Participant
     *
     * @return User 
     */
    public function getFirstParticipant()
    {
        return $this->firstParticipant;
    }

    /**
     * Get First Participant
     *
     * @return User 
     */
    public function getSecondParticipant()
    {
        return $this->secondParticipant;
    }
    
    public function addParticipants(User $firstUser, User $secondUser){
        //First participant is Alwaws with smaller Id
        if($firstUser->getId() < $secondUser->getId()){
            $this->firstParticipant = $firstUser;
            $this->secondParticipant = $secondUser;
        } else {
            $this->firstParticipant = $secondUser;
            $this->secondParticipant = $firstUser;
        }
    }

   
}
