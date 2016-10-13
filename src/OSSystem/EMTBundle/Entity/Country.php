<?php
namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="countries")
 * @ORM\HasLifecycleCallbacks 
 */
class Country
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=2)
     */
    private $location;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
	 * {@inheritdoc}
	 */
    public function __toString()
    {
	return $this->getTitle() ? : 'n/a';
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
            $this->setCreatedAt(new \DateTime);

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
     * @return Country
     */
    public function setCountry($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Country
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Country
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
     * Set user
     *
     * @param \OSSystem\EMTBundle\Entity\User $user
     * @return Country
     */
    public function setUser(\OSSystem\EMTBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \OSSystem\EMTBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Country
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Country
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }
}
