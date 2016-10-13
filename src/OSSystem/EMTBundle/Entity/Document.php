<?php 
namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use OSSystem\EMTBundle\Entity\Conference; 
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="document")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks 
 */
class Document
{
    const DOCUMENT_TARGET_NA = 0;
    const DOCUMENT_TARGET_ACCOMODATION = 1;
    const DOCUMENT_TARGET_APPENDIX = 2;
    const DOCUMENT_TARGET_VAULT = 3;
    const DOCUMENT_TARGET_MESSAGES = 4;
    const DOCUMENT_TARGET_APPEAL = 5;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;
    
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Conference")
     */
    private $conference;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Message")
     */
    private $message;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    
    private $temp;
    private $hiddenName;
    private $tempConferenceId;
    
    
    /**
     * @ORM\Column(type="integer", length=1, nullable=true,  options={"default" = 0})
     */
    private $target;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateAt", type="datetime", nullable=true)
     */
    private $updateAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\User")
     */
    private $user;
    
     /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile( \Symfony\Component\HttpFoundation\File\UploadedFile  $file = null, $conferenceId = false)
    {
        
        if (!isset($file)) {
            return false;
        }
        
        $gext = $file->guessExtension();
        if (($gext == 'exe') || ($gext == 'htaccess') || $gext == 'php'){
            return 'os_emt_fileextension_error';
        }
        $this->temp = $this->path; //old file should be deleted
        $this->file = $file;
        
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        } else {
            $this->path = 'initial';
        }
        
        if (null !== $this->file) {
            $this->hiddenName = md5($this->file->getClientOriginalName() . time());
            $this->path = ($conferenceId ? $conferenceId."/" : "") . $this->hiddenName.'.'.  pathinfo($this->getFile()->getClientOriginalName(), PATHINFO_EXTENSION) ;
            $this->name = $this->file->getClientOriginalName() ;
            
            $this->tempConferenceId = $conferenceId;
        }
        return true;
    }
    
     /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does

        if ($this->tempConferenceId){
            if (!file_exists($this->getUploadRootDir().'/'.$this->tempConferenceId)){
                mkdir($this->getUploadRootDir().'/'.$this->tempConferenceId);
            }
            $uploaddir = $this->getUploadRootDir().'/'.$this->tempConferenceId;
        }else{
            $uploaddir = $this->getUploadRootDir();
        }
        
        $this->getFile()->move(
            $uploaddir,
            pathinfo( $this->path, PATHINFO_BASENAME )
        );

        $this->setFile(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            @unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }


    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../httpdocs/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
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
     * Set name
     *
     * @param string $name
     * @return Document
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
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Set conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     * @return Document
     */
    public function setConference(\OSSystem\EMTBundle\Entity\Conference $conference = null)
    {
        $this->conference = $conference;

        return $this;
    }

    /**
     * Get conference
     *
     * @return \OSSystem\EMTBundle\Entity\Conference 
     */
    public function getConference()
    {
        return $this->conference;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conference = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     * @return Document
     */
    public function addConference(\OSSystem\EMTBundle\Entity\Conference $conference)
    {
        $this->conference[] = $conference;

        return $this;
    }

    /**
     * Remove conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     */
    public function removeConference(\OSSystem\EMTBundle\Entity\Conference $conference)
    {
        $this->conference->removeElement($conference);
    }
    
    public function __toString()
    {
        return $this->getName() ? : 'n/a';
    }
    
    
    /**
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Realty
     */
    public function setCreatedAt($createdAt) 
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt > new \DateTime('0000-00-00 00:00:00') ? $this->createdAt : null;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return Realty
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }
    

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }
    
    
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime);
        }
        
        $this->setUpdateAt(new \DateTime);
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdateAt(new \DateTime);
    }
    
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    
    public function getTargetAsText()
    {
        switch ($this->target) {
            case self::DOCUMENT_TARGET_NA :
               return "n/a";
            case self::DOCUMENT_TARGET_ACCOMODATION :
               return "accomodation";
            case self::DOCUMENT_TARGET_APPENDIX :
               return "appendix";
            case self::DOCUMENT_TARGET_VAULT :
               return "vault";
            case self::DOCUMENT_TARGET_MESSAGES :
               return "messages";
            case self::DOCUMENT_TARGET_APPEAL :
               return "appeal board";
            default:
                return "n/a";
        }
        
    }

    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }
    
    public function getComment()
    {
        return $this->comment;
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
    
    
    

    /**
     * Set message
     *
     * @param \OSSystem\EMTBundle\Entity\Message $message
     * @return Document
     */
    public function setMessage(\OSSystem\EMTBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \OSSystem\EMTBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
}
