<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statuses
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Status
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
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;


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
     * @return Statuses
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
//        return $this->title!='Saved'?$this->title:'';
    }
    
    public function __toString(){
        if ($this->getTitle() != 'Saved'){
            return $this->getTitle();
        }else{
            return "";
        }
    }
}
