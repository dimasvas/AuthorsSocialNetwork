<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use JMS\Serializer\Annotation as JMS;


/**
 * Composition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CompositionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * 
 * @JMS\ExclusionPolicy("all")
 */
class Composition
{
    const STATUS_INPROCESS = 'in_process';
    const STATUS_FINISHED = 'finished';
    const STATUS_FREEZED = 'freezed';
    const STATUS_DELETED = 'deleted';
  
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"list", "admin-search"})
     */
    private $id;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="composition")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    
    /**
     * @var
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
     * @var $name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="composition.title_required")
     * @Assert\Length(
     *     min=2,
     *     max=100,
     *     minMessage="composition.title_short",
     *     maxMessage="composition.title_long",
     * )
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"list", "admin-search"})
     */
    private $title;

    /**
     * @var $description
     *
     * @ORM\Column(type="string", length=1050)
     * @Assert\NotBlank(message="composition.description_required")
     * @Assert\Length(
     *     max=1000,
     *     maxMessage="composition.description_long",
     * )
     * 
     */
    private $description;

    /**
     * @var $language
     *
     * @ORM\Column(type="string", length=4)
     * @Assert\NotBlank(message="composition.choose_lang")
     * @Assert\Choice(callback = "getLanguages", message = "composition.choose_lang_from_list")
     * 
     */
    private $language;
    
    /**
     * @var type 
     * 
     * @ORM\ManyToMany(targetEntity="Genre")
     * @ORM\JoinTable(name="composition_genres",
     *      joinColumns={@ORM\JoinColumn(name="composition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *      )
     */
    private $genres;

    /**
     * @var $total_rating_num
     *
     * @ORM\Column(type="float", options={"default" = 0})
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"list"})
     */
    private $total_rating_num;

    /**
     * @var $num_users_rate
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"list"})
     */
    private $num_users_rate;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompositionCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     * 
     * @JMS\Expose
     * @JMS\Type("AppBundle\Entity\CompositionCategory")
     * @JMS\Groups({"list"})
     **/
    private $category;

    /**
     * @var $published
     *
     * @ORM\Column(type="boolean", nullable=false)
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\Groups({"admin-search"})
     */
    private $published;
    
    /**
     * @var $blocked
     *
     * @ORM\Column(type="boolean")
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\Groups({"admin-search"})
     */
    private $blocked;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="composition_cover", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"list"})
     */
    private $imageName;

    /**
     * @var $created
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var $updated
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=15)
     *
     * @Assert\Choice(callback = "getStatusList", message = "composition.choose_status_from_list")
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"list"})
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="subscribers_num", type="integer")
     */
    private $subscribers_num;
    
        
    /**
     * var $archived boolean
     * @ORM\Column(name="archived", type="boolean")
     */
    protected $archived;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="composition", cascade={"remove"}, orphanRemoval=true)
     */
    private $attachments;
    
    /**
     * @var $typeName
     *
     * @ORM\Column(type="string", length=255)
     * //Assert\NotBlank(message="Not Blank")
 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"list", "admin-search"})
     */
    private $typeName;
    
      /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Music",inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="music_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Music")
     */
    private $music;
    
    /**
     * @var
     * 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Book", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Book")
     */
    private $book;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Animation", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="animation_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Animation")
     */
    private $animation;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Lyric", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="lyric_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Lyric")
     */
    private $lyric;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Scenario", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="scenario_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Scenario")
     */
    private $scenario;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Idea", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="idea_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Idea")
     */
    private $idea;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Image", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Image")
     */
    private $image;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Game", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Game")
     */
    private $game;
    
    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompositionTypes\Video", inversedBy="composition", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * //Assert\Type(type="AppBundle\Entity\CompositionTypes\Music")
     */
    private $video;
    
    //TODO ALL init in one method
    
    public function __construct() 
    {
        $this->attachments = new ArrayCollection();
        $this->genres = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Composition
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $title;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Composition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set total_rating_num
     *
     * @param float $totalRatingNum
     * @return Composition
     */
    public function setTotalRatingNum($totalRatingNum)
    {
        $this->total_rating_num = $totalRatingNum;

        return $this;
    }

    /**
     * Get total_rating_num
     *
     * @return float 
     */
    public function getTotalRatingNum()
    {
        return $this->total_rating_num;
    }

    /**
     * Set num_users_rate
     *
     * @param integer $numUsersRate
     * @return Composition
     */
    public function setNumUsersRate($numUsersRate)
    {
        $this->num_users_rate = $numUsersRate;

        return $this;
    }

    /**
     * Get num_users_rate
     *
     * @return integer 
     */
    public function getNumUsersRate()
    {
        return $this->num_users_rate;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Composition
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }
    
    /**
     * Set blocked
     *
     * @param boolean $blocked
     * @return Composition
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean 
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Composition
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Used as validation callback
     * 
     * @return array
     */
    public static function getLanguages()
    {
        return Language::getCompositionLangKeys();
    }
    
    /**
     * Set cover
     *
     * @param string $imgPath
     * @return Composition
     */
    public function setCover($imgPath)
    {
        $this->cover = $imgPath;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set author
     *
     * @param Author $author
     * @return Composition
     */
    public function setAuthor(Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Set user
     *
     * @param User $user
     * @return Composition
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
     * Get type
     *
     * @return CompositionCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set type
     *
     * @param CompositionCategory $category 
     * @return CompositionCategory
     */
    public function setCategory(CompositionCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get created
     * @ORM\PrePersist
     * @return Composition
     */
    public function setCreated()
    {
        $this->created = new \DateTime('now');

        return $this;
    }
    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get created
     * @ORM\PrePersist
     * @ORM\PreUpdate()
     * @return Composition
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime('now');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }
    
    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->{$this->getCategory()->getAlias()};
    }
    
    /**
     * 
     * @param type $typeObj
     * @return \AppBundle\Entity\Composition
     */
    public function setType($typeObj)
    {
        $this->{$this->getCategory()->getAlias()} = $typeObj;
        
        return $this;
    }

    /**
     * Set subscribers_num
     *
     * @param integer $subscribersNum
     * @return Composition
     */
    public function setSubscribersNum($subscribersNum)
    {
        $this->subscribers_num = $subscribersNum;

        return $this;
    }

    /**
     * Get subscribers_num
     *
     * @return integer 
     */
    public function getSubscribersNum()
    {
        return $this->subscribers_num;
    }
    
     /**
     * Set archived
     *
     * @param boolean $archived
     * @return Composition
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Add attachments
     *
     * @param \AppBundle\Entity\Attachment $attachments
     * @return Composition
     */
    public function addAttachment(\AppBundle\Entity\Attachment $attachments)
    {
        $this->attachments[] = $attachments;

        return $this;
    }

    /**
     * Remove attachments
     *
     * @param Attachment $attachments
     */
    public function removeAttachment(Attachment $attachments)
    {
        $this->attachments->removeElement($attachments);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
    
    /**
     * 
     */
    public function clearAttachments ()
    {
        $this->getAttachments()->clear();
    }
    
     /**
     * Add genre
     *
     * @param Genre $genre
     * @return Composition
     */
    public function addGenre(Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre
     *
     * @param Genres $genres
     */
    public function removeGenres(Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Validation rules
     */
    
    
    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }
    
    /**
     * Clear genres
     */
    public function clearGenres()
    {
        $this->getGenres()->clear();
    }
    
    /**
     * Get Status List
     * Used as validation callback
     * 
     * @return type
     */
    public static function getStatusList() {
        return [
            self::STATUS_INPROCESS, 
            self::STATUS_FREEZED, 
            self::STATUS_FINISHED
        ];
    }
    
    /**
     * Set composition Status
     * 
     * @return Composition
     */
    public function setStatus($status) 
    {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * Set composition Status
     * 
     * @return Composition
     */
    public function setStatusInProcess() 
    {
        $this->status = self::STATUS_INPROCESS;
        
        return $this;
    }
    
    /**
     * Set composition Status
     * 
     * @return Composition
     */
    public function setStatusFreezed() 
    {
        $this->status = self::STATUS_FREEZED;
        
        return $this;
    }
    
    /**
     * Set composition Status
     * 
     * @return Composition
     */
    public function setStatusFinished() 
    {
        $this->status = self::STATUS_FINISHED;
        
        return $this;
    }
    
    /**
     * Get composition Status
     * 
     * @return string
     */
    public function getStatus() 
    {
         return $this->status;
    }
    
     /**
     * Set typeName
     *
     * @param string $typeName
     * @return Composition
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get typeName
     *
     * @return string 
     */
    public function getTypeName()
    {
        return $this->typeName;
    }
    
    /**
     * Check if status in process
     *  
     * @return boolean
     */
    public function isStausInProcess()
    {
        return $this->status == self::STATUS_INPROCESS;
    }
    
     /**
     * Check if status freezed
     *  
     * @return boolean
     */
    public function isStausFreezed()
    {
        return $this->status == self::STATUS_FREEZED;
    }
    
     /**
     * Check if status finished
     *  
     * @return boolean
     */
    public function isStausFinished()
    {
        return $this->status == self::STATUS_FINISHED;
    }
    
    /**
     * Set default values for new Entity
     * 
     * @ORM\PrePersist
     */
    public function setDefaultValues()
    {
        if ($this->id == null) {
            
            $this->subscribers_num = 0;
            
            $this->num_users_rate = 0;
            
            $this->total_rating_num = 0;
            
            $this->published =  false;
            
            $this->archived =  false;
            
            $this->blocked =  false;
        }
    }
    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->getGenres()->count()) {
             $context->buildViolation('composition.choose_genre')
                ->atPath('genres')
                ->addViolation();
        }
    }
 
}
