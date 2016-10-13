<?php
namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_categories")
 * @ORM\HasLifecycleCallbacks 
 */
class UserCategory
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
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;
    
   
    
     /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $createdUser;
    
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
	 * {@inheritdoc}
	 */
    public function __toString()
    {
	return $this->getCategory() ? : 'n/a';
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
     * Set category
     *
     * @param string $category
     * @return UserCategory
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserCategory
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
     * @return UserCategory
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
     * @param \OSSystem\EMTBundle\Entity\User $createdUser
     * @return UserCategory
     */
    public function setCreatedUser(\OSSystem\EMTBundle\Entity\User $createdUser = null)
    {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get user
     *
     * @return \OSSystem\EMTBundle\Entity\User 
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }
}
