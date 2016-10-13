<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConferenceCriteriaState
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConferenceCriteriaState
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
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Criteria")
     */
    private $criteria;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="status")
     */
    private $status;
    
    /**
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="criterias")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     **/
    private $conference;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=255, nullable=true)
     */
    private $comment;
    
    /**
     * @ORM\OneToOne(targetEntity="Document",cascade={"persist"})
     * @ORM\JoinColumn(name="document_id",referencedColumnName="id")
     */
    private $document;

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
     * Set criteria
     *
     * @param string $criteria
     * @return ConferenceCriteriaState
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Get criteria
     *
     * @return string 
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ConferenceCriteriaState
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * Set conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     * @return ConferenceCriteriaState
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
     * Set comment
     *
     * @param string $comment
     * @return ConferenceCriteriaState
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
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
    
    /**
     * 
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }
    
    /**
     * 
     * @param type $document
     * @return \OSSystem\EMTBundle\Entity\ConferenceCriteriaState
     */    
    public function setDocument(Document $document)
    {
        $this->document = $document;
        
        return $this;
    }
}
