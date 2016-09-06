<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Author;
use AppBundle\Entity\FavouriteComposition;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use AppBundle\Service\AppLocale;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * 
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"default", "list", "message", "admin-search"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank(message="Please enter your Name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=15,
     *     minMessage="The Name is too short.",
     *     maxMessage="The Name is too long.",
     *     groups={"Registration"}
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{Cyrillic}\s\-]+$/u",
     *     match=true,
     *     message="Your name cannot contain numbers and symbols",
     *     groups={"Registration"}
     * )
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"default", "list", "message", "admin-search"})
     */
    protected $name;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\Length(
     *     max=20,
     *     maxMessage="The Surname is too long.",
     *     groups={"Registration"}
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{Cyrillic}\s\-]+$/u",
     *     match=true,
     *     message="Your Surname cannot contain numbers and symbols",
     *     groups={"Registration"}
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"default", "list", "message", "admin-search"})
     */
    protected $surname;
    
    /**
     * @Assert\Length(
     *     min=5,
     *     minMessage="The Pasword to long.",
     *     groups={"Registration", "Settings"}
     * )
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{Cyrillic}\s\-]+$/u",
     *     match=true,
     *     message="Your middleName cannot contain numbers and symbols",
     *     groups={"Registration"}
     * )
     */
    protected $middleName = null;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{Cyrillic}\d\s\-]+$/u",
     *     match=true,
     *     message="Your name cannot contain symbols",
     *     groups={"Registration"}
     * )
     */
    protected $aliasName = null;

    /**
     * @var boolean $showAliasName
     * @ORM\Column(type="boolean")
     * 
     * @Assert\Choice(
     *      choices = {"0", "1"}, 
     *      message = "Choose a valid type.",
     *      groups={"Registration", "Profile"}
     * )
     */
    protected $showAliasName;

    /**
     * @var $dateOfBirth
     *
     * @ORM\Column(name="date_of_birth", type="datetime")
     * @Assert\DateTime(groups={"Registration"})
     */
    protected $dateOfBirth;

    /**
     * @var boolean $hideYearOfBirth
     * @ORM\Column(name="hide_year_of_birth", type="boolean")
     * 
     * @Assert\Choice(
     *      choices = {"0", "1"}, 
     *      message = "Choose a valid type.",
     *      groups={"Registration", "Profile"}
     * )
     */
    protected $hideYearOfBirth;

    /**
     * @var $gender
     *
     * @ORM\Column(name="gender", type="string", length=7)
     * @Assert\NotBlank(message="Please choose your gender.", groups={"Registration", "Profile"})
     * @Assert\Choice(
     *      choices = {"male", "female"}, 
     *      message = "Choose a valid gender.",
     *      groups={"Registration"}
     * )
     */
    protected $gender;

    /**
     * @var boolean $isAuthor
     * @ORM\Column(name="is_author", type="boolean")
     * 
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\Groups({"default"})
     */
    protected $isAuthor;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     * 
     * @Assert\NotBlank(message="Please provide locale.", groups={"Registration", "Profile"})
     * @Assert\Choice(
     *      choices = {"ru", "ua"}, 
     *      message = "Choose a valid locale.",
     *      groups={"Registration", "Profile"}
     * )
     */
    protected $locale = null;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank(message="Please choose country.", groups={"Registration", "Profile"})
     * @Assert\Country(message="Country value must be valid", groups={"Registration", "Profile"})
     */
    protected $country;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank(message="Please fill your city.", groups={"Registration", "Profile"})
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{Cyrillic}\d\s\-]+$/u",
     *     match=true,
     *     message="Your name cannot contain symbols",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Length(
     *     max=20,
     *     maxMessage="The City is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected  $city = null;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max=255,
     *     maxMessage="The Description is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $aboutMe = null;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Author", inversedBy="user")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     **/
    protected $author;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="profile_image", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"message"})
     * @JMS\SerializedName("image")
     */
    private $imageName;
    
    /**
     *
     * @var 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FavouriteComposition", mappedBy="user")
     */
    private $favouriteComposition;
    
    /**
     * @var type 
     * @ORM\Column(name="friendship", type="integer")
     */
    protected $friendship;
    
    /**
     *
     * @var type 
     */
    private $default_user_locale = AppLocale::LANG_RUS;


    public function __construct()
    {
        parent::__construct();
        
        $this->favouriteComposition = new ArrayCollection();
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
     * Set lastName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
 
    /**
    * @JMS\VirtualProperty
    * @JMS\Type("string")
    * @JMS\Groups({"default", "list", "message", "admin-search"})
    * @JMS\SerializedName("nameSurname")
    */
    public function getNameSurname () 
    {
        return $this->getName() .' '. $this->getSurname();
    }

    /**
     * Set aliasName
     *
     * @param string $aliasName
     * @return User
     */
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;

        return $this;
    }

    /**
     * Get aliasName
     *
     * @return string
     */
    public function getAliasName()
    {
        return $this->aliasName;
    }

    /**
     * @ORM\PrePersist()
     */
    public function initShowAliasName()
    {
        $this->showAliasName = false;
    }

    /**
     * Set showAliasName
     *
     * @param boolean $showAliasName
     * @return User
     */
    public function setShowAliasName($showAliasName)
    {
        $this->showAliasName = $showAliasName;

        return $this;
    }

    /**
     * Get showAliasName
     *
     * @return boolean
     */
    public function getShowAliasName()
    {
        return $this->showAliasName;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }


    /**
     * Set hideYearOfBirth
     *
     * @param boolean $hideYearOfBirth
     * @return User
     */
    public function setHideYearOfBirth($hideYearOfBirth)
    {
        $this->hideYearOfBirth = $hideYearOfBirth;

        return $this;
    }

    /**
     * Get hideYearOfBirth
     *
     * @return boolean 
     */
    public function getHideYearOfBirth()
    {
        return $this->hideYearOfBirth;
    }

    /**
     * Set isAuthor
     *
     * @param string $isAuthor
     * @return User
     */
    public function setIsAuthor($isAuthor)
    {
        $this->isAuthor = $isAuthor == 'yes'? true : false;

        return $this;
    }

    /**
     * Get isAuthor
     *
     * @return string
     */
    public function getIsAuthor()
    {
        return $this->isAuthor;
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        return $this->isAuthor;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return User
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string 
     * 
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * Set created
     * @ORM\PrePersist
     * @return \DateTime
     */
    public function setCreated()
    {
        return $this->created = new \DateTime('now');
    }
    
    /**
     * Get created
     * 
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Updated
     * @ORM\PrePersist
     * @ORM\PreUpdate()
     * @return \DateTime
     */
    public function setUpdated()
    {
        return $this->updated = new \DateTime('now');
    }

    /**
     * Get updated
     * 
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set author
     *
     * @param Author $author
     * @return User
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
     * Compare Users Entities
     * @param User $user
     * @return bool
     */
    public function isEqualTo(User $user) {

        if($this->getId() == $user->getId() and
            $this->getEmail() == $user->getEmail()) {
            return true;
        }

        return false;
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
     * Add composition
     *
     * @param FavouriteComposition $favouriteComposition
     * @return Author
     */
    public function addFavouriteComposition(FavouriteComposition $favouriteComposition)
    {
        $this->favouriteComposition[] = $favouriteComposition;

        return $this;
    }

    /**
     * Remove composition
     *
     * @param FavouriteComposition $favouriteComposition
     */
    public function removeFavouriteComposition(FavouriteComposition $favouriteComposition)
    {
        $this->favouriteComposition->removeElement($favouriteComposition);
    }

    /**
     * Get composition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavouriteComposition()
    {
        return $this->favouriteComposition;
    }
    
     /**
     * Set frienship
     *
     * @param integer $value
     * @return User
     */
    public function setFriendship($value)
    {
        $this->friendship = $value;

        return $this;
    }

    /**
     * Get frienship
     *
     * @return integer 
     */
    public function getFriendship()
    {
        return $this->friendship;
    }
    
    /**
     * Init User Locale
     * @ORM\PrePersist
     */
    public function initLocale()
    {
        if(!$this->getId()){
            $this->locale = $this->default_user_locale;
            $this->friendship = 20;
        }
    }
    
    /**
     * @JMS\VirtualProperty
     * @JMS\Type("string")
     * @JMS\Groups({"default", "list", "message", "admin-search"})
     * @JMS\SerializedName("username")
     */
    public function getVirtualUsername() 
    {
        return $this->getUsername();
    }
}
