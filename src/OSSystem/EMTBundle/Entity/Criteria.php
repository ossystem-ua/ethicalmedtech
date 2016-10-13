<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Criteria
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Criteria
{
    
    const CRITERIA_STATE_NA = 0;
    const CRITERIA_STATE_TOBEREVIEWED = 1;
    const CRITERIA_STATE_COMPLIANT = 2;
    const CRITERIA_STATE_NOTCOMPLIANT = 3;
    const CRITERIA_STATE_MISSING = 4;
    const CRITERIA_STATE_UNDERCORRECTION = 5;
    
    
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
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", length=11)
     */
    private $position;


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
     * @return Criteria
     */
    public function setTitle($title)
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
     * Set position
     *
     * @param integer $position
     * @return Criteria
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    public static function getStatusAsText($status){
        switch ($status)  {
            case 1:
                return 'To be reviewed';
            case 2:
                return 'Compliant';
            case 3:
                return 'Not compliant';
            case 4:
                return 'Missing';
            case 5:
                return 'Under correction';
            case 0:
            default:
                return 'Not applicable';
        }
        
    }
}
