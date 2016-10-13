<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OSSystem\EMTBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conference
 *
 * @ORM\Table(name="message")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    const MESSAGE_TARGET_MESSAGE_BOARD = 0;
    const MESSAGE_TARGET_APPEAL_BOARD = 1;
    const MESSAGE_TARGET_MAIL_BOARD = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
        /**
	 * @var string $content
	 *
	 * @ORM\Column(name="content", type="text", nullable=false)
	 */
	private $content;
        
        /**
	 * @var string $mailto
	 *
	 * @ORM\Column(name="mailto", type="text", nullable=true)
	 */
	private $mailto;

	/**
	 * @var string $subject
	 *
	 * @ORM\Column(name="subject", type="text", nullable=true)
	 */
	private $subject;
	/**
	 * @var boolean $unread
	 *
	 * @ORM\Column(name="unread", type="boolean", nullable=false)
	 */
	private $unread = true;

	/**
	 * @var datetime $createdAt
	 *
	 * @ORM\Column(name="created_at", type="datetime", nullable=true)
	 */
	private $createdAt;

	/**
	 * @var datetime $updatedAt
	 *
	 * @ORM\Column(name="updated_at", type="datetime", nullable=true )
	 */
	private $updatedAt;    
        
        
        /**
	 * @var  \OSSystem\EMTBundle\Entity\User $sender
	 *
	 * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\User")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="sender_id", referencedColumnName="id")
	 * })
	 */
	private $sender;

	/**
	 * @var  OSSystem\EMTBundle\Entity\User $recipient
	 *
	 * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\User")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
	 * })
	 */
	private $recipient;
        
        /**
	 * @var  OSSystem\EMTBundle\Entity\Conference $conference
	 *
	 * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Conference")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="conference_id", referencedColumnName="id")
	 * })
	 */
	private $conference;
        
    /**
     *
     * @ORM\ManyToMany(targetEntity="OSSystem\EMTBundle\Entity\Document", cascade={"persist", "remove"} )
     * @ORM\JoinTable(name="documents_messages",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $documents; 

    /**
    * @ORM\Column(type="integer", length=1, nullable=true,  options={"default" = 0})
    */
    private $target;
          
        
        
        
        
        /**
	 * {@inheriteDoc}
	 */
	public function __toString()
	{
		return (string) $this->getId();
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
     * Set content
     *
     * @param string $content
     * @return Message
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
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set unread
     *
     * @param boolean $unread
     * @return Message
     */
    public function setUnread($unread)
    {
        $this->unread = $unread;

        return $this;
    }

    /**
     * Get unread
     *
     * @return boolean 
     */
    public function getUnread()
    {
        return $this->unread;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Message
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
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Message
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set sender
     *
     * @param \OSSystem\EMTBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\OSSystem\EMTBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \OSSystem\EMTBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set recipient
     *
     * @param \OSSystem\EMTBundle\Entity\User $recipient
     * @return Message
     */
    public function setRecipient(\OSSystem\EMTBundle\Entity\User $recipient = null)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return \OSSystem\EMTBundle\Entity\User 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     * @return Message
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
         * @ORM\PrePersist
         */
        public function prePersist()
        {

            if (!$this->getCreatedAt()) {
                $this->setCreatedAt(new \DateTime);
            }

            if (!$this->getUpdatedAt()) {
                $this->setUpdatedAt(new \DateTime);
            }
        }

        /**
         * @ORM\PreUpdate
         */
        public function preUpdate()
        {   
            $this->setUpdatedAt(new \DateTime);
        }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * creates a document and uploads a file
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     * @return Message
     */
    public function uploadNewDocument($file, $comment = '' )
    {
        $mDocument = new Document();
        $mDocument->setMessage($this);
        $mDocument->setFile($file, $this->id);
        $mDocument->setTarget( Document::DOCUMENT_TARGET_MESSAGES );
        $mDocument->setComment($comment);
            
        $this->documents[] = $mDocument;

        return $this;
    }

    /**
     * Add documents
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     * @return Message
     */
    public function addDocument(\OSSystem\EMTBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     */
    public function removeDocument(\OSSystem\EMTBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set target
     *
     * @param integer $target
     * @return Message
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return integer 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set mailto
     *
     * @param string $mailto
     * @return Message
     */
    public function setMailto($mailto)
    {
        $this->mailto = $mailto;

        return $this;
    }

    /**
     * Get mailto
     *
     * @return string 
     */
    public function getMailto()
    {
        return $this->mailto;
    }
}
