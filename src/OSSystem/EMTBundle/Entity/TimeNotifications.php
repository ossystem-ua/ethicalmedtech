<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statuses
 *
 * @ORM\Table(name="time_notifications")
 * @ORM\Entity
 */
class TimeNotifications
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
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="criterias")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id" , onDelete="CASCADE")
     **/
    private $conference;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline_date", type="datetime", nullable=true)
     */
    private $deadlineDate;


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
     * Set deadlineDate
     *
     * @param \DateTime $deadlineDate
     * @return TimeNotifications
     */
    public function setDeadlineDate($deadlineDate)
    {
        $this->deadlineDate = $deadlineDate;

        return $this;
    }

    /**
     * Get deadlineDate
     *
     * @return \DateTime 
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    /**
     * Set conference
     *
     * @param \OSSystem\EMTBundle\Entity\Conference $conference
     * @return TimeNotifications
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
}
