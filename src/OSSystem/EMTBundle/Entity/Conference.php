<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OSSystem\EMTBundle\Entity\Country;
use OSSystem\EMTBundle\Entity\Document;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Conference
 *
 * @ORM\Table(name="conferences")
 * @ORM\Entity(repositoryClass="OSSystem\EMTBundle\Entity\ConferenceRepository")
 */
class Conference
{
    const CONFERENCE_STATUS_SAVED = 1;
    const CONFERENCE_STATUS_TOBEREVIEWED = 2;
    const CONFERENCE_STATUS_TOBEREVIEWEDFORPRECLEARANCE = 11;
    const CONFERENCE_STATUS_TOBEREVIEWEDFORPARTIALSUBMISSION = 15;
    
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
     * @ORM\Column(name="title", type="string", length=150, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="acronym", type="string", length=100, nullable=true)
     */
    private $acronym;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\TherapeuticArea")
     */
    private $therapeuticArea;
    
    /**
     * @var string
     *
     * @ORM\Column(name="therapeutic_area_other", type="string", length=100, nullable=true)
     */
    private $therapeuticAreaOther;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startConferenceDate", type="date", nullable=true)
     */
    private $startConferenceDate;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="startConferenceTime", type="text", length=5, nullable=true)
     */
    private $startConferenceTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endConferenceDate", type="date", nullable=true)
     */
    private $endConferenceDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="endConferenceTime", type="text", length=5, nullable=true)
     */
    private $endConferenceTime;

    /**
     * @var string
     *
     * @ORM\Column(name="organizingCompaniesNames", type="text", nullable=true)
     */
    private $organizingCompaniesNames;

    /**
     * @var string
     *
     * @ORM\Column(name="contactPersonsNames", type="text", length=255, nullable=true)
     */
    private $contactPersonsNames;

    /**
     * @var string
     *
     * @ORM\Column(name="organizingEmails", type="string", length=200, nullable=true)
     */
    private $organizingEmails;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=150, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     * automate publish after moderate checks
     * @ORM\Column(name="autopublishing", type="boolean", nullable=true)
     */
    private $autopublishing;
    
    
    
    /**
     * @var boolean
     * special state for conference before first submitting to moderator
     * @ORM\Column(name="is_new", type="boolean", nullable=true)
     */
    private $isNew;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="delegatesMoreThan1Country", type="integer", length=1, nullable=true)
     */
    private $delegatesMoreThan1Country;
    

    /**
     * @ORM\ManyToMany(targetEntity="OSSystem\EMTBundle\Entity\Country")
     * @ORM\JoinTable(name="delegates_countries",
     *      joinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="country_id", referencedColumnName="id")}
     * )
     **/
    private $delegatesCountries;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="delegatesAnticipate", type="string", nullable=true)
     */
    private $delegatesAnticipate;
    
    
    //--------------------------------------------------------------------------
    //ASSESMENT
    /**
     * @var string
     *
     * @ORM\Column(name="assesmentNameVenue", type="string", length=250, nullable=true)
     */
    private $assesmentNameVenue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="assesmentCategoryVenue", type="string", length=150, nullable=true)
     */
    private $assesmentCategoryVenue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="assesmentCity", type="string", length=150, nullable=true)
     */
    private $assesmentCity;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Country")
     */
    private $assesmentCountry;
    
    /**
     * @var string
     *
     * @ORM\Column(name="assesmentLocalNA", type="string", length=150, nullable=true)
     */
    private $assesmentLocalNA;
    
    /**
     * @var string
     *
     * @ORM\Column(name="assesmentProposedAccomodation", type="string", length=150, nullable=true)
     */
    private $assesmentProposedAccomodation;
    
    
    /**
     *
     
     * @ORM\ManyToMany(targetEntity="OSSystem\EMTBundle\Entity\Document",cascade={"persist", "remove"} )
     * @ORM\JoinTable(name="assesmentdocuments",
     *      joinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $assesmentProposedAccomodationDocument;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $assesmentProposedAccomodationDocumentFile;
    
    //--------------------------------------------------------------------------
    //APPENDIX
    /**
     * @var string
     *
     * @ORM\Column(name="eventProgramme", type="string", length=250, nullable=true)
     */
    private $eventProgramme;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="OSSystem\EMTBundle\Entity\Document", cascade={"persist", "remove"} )
     * @ORM\JoinTable(name="documents_conference",
     *      joinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id",  onDelete="CASCADE")}
     * )
     */
    private $documents; 
    
    //--------------------------------------------------------------------------
    /**
     * @var bool
     * @ORM\Column(name="is_co_recommendation", type="boolean", nullable=false, options={"default" : false})
     */
    private $isCORecommendation = false;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Status")
     */
    private $conferenceStatus;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="conference_status_date", type="datetime", nullable=true)
     *  */
    private $conferenceStatusDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comments_text", type="text", nullable=true)
     */
    private $conferenceStatusText;
    
    /**
     * @ORM\ManyToOne(targetEntity="OSSystem\EMTBundle\Entity\Status")
     */
//    private $conferenceStatusDocument;
    
    /**
     * @var bool
     * @ORM\Column(name="isPreClearance", type="boolean", nullable=true)
     */
    private $isPreClearance;
    
    /**
     * @var bool
     * @ORM\Column(name="is_partial_submission", type="boolean", nullable=true)
     */
    private $isPartialSubmission;
    
    
    /**
     * @var bool
     * @ORM\Column(name="isPublished", type="boolean", nullable=true)
     */
    private $isPublished;
    
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
     * @var boolean
     * 
     * @ORM\Column(name="archive", type="boolean", nullable=false)
     */
    private $archive= false;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submission_date", type="datetime", nullable=true)
     */
    private $submissionDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="assessed_on_date", type="datetime", nullable=true)
     */
    private $assessedOnDate;
    
    
    /**
     *
     * @ORM\Column(name="snapshot", type="text", nullable=true)
     */
    private $snapshot;
    
    /**
     *
     * @ORM\Column(name="changed_fields", type="text", nullable=true)
     */
    private $changedFields;
    
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="OSSystem\EMTBundle\Entity\ConferenceCriteriaState", mappedBy="conference", cascade={"persist", "remove"})
     */
    private $criterias;
    
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
     * set id
     *
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $name
     * @return Conference
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
     * Set acronym
     *
     * @param string $acronym
     * @return Conference
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * Get acronym
     *
     * @return string 
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * Set therapeuticArea
     *
     * @param string $therapeuticArea
     * @return Conference
     */
    public function setTherapeuticArea($therapeuticArea)
    {
        $this->therapeuticArea = $therapeuticArea;

        return $this;
    }

    /**
     * Get therapeuticArea
     *
     * @return string 
     */
    public function getTherapeuticArea()
    {
        return $this->therapeuticArea;
    }

    /**
     * Set startConferenceDate
     *
     * @param \DateTime $startConferenceDate
     * @return Conference
     */
    public function setStartConferenceDate($startConferenceDate)
    {
        $this->startConferenceDate = $startConferenceDate;

        return $this;
    }

    /**
     * Get startConferenceDate
     *
     * @return \DateTime 
     */
    public function getStartConferenceDate()
    {
        return $this->startConferenceDate;
    }

    /**
     * Set endConferenceDate
     *
     * @param \DateTime $endConferenceDate
     * @return Conference
     */
    public function setEndConferenceDate($endConferenceDate)
    {
        $this->endConferenceDate = $endConferenceDate;

        return $this;
    }

    /**
     * Get endConferenceDate
     *
     * @return \DateTime 
     */
    public function getEndConferenceDate()
    {
        return $this->endConferenceDate;
    }

    /**
     * Set organizingCompaniesNames
     *
     * @param string $organizingCompaniesNames
     * @return Conference
     */
    public function setOrganizingCompaniesNames($organizingCompaniesNames)
    {
        $this->organizingCompaniesNames = $organizingCompaniesNames;

        return $this;
    }

    /**
     * Get organizingCompaniesNames
     *
     * @return string 
     */
    public function getOrganizingCompaniesNames()
    {
        return $this->organizingCompaniesNames;
    }

    /**
     * Set contactPersonsNames
     *
     * @param string $contactPersonsNames
     * @return Conference
     */
    public function setContactPersonsNames($contactPersonsNames)
    {
        $this->contactPersonsNames = $contactPersonsNames;

        return $this;
    }

    /**
     * Get contactPersonsNames
     *
     * @return string 
     */
    public function getContactPersonsNames()
    {
        return $this->contactPersonsNames;
    }

    /**
     * Set organizingEmails
     *
     * @param string $organizingEmails
     * @return Conference
     */
    public function setOrganizingEmails($organizingEmails)
    {
        $this->organizingEmails = $organizingEmails;

        return $this;
    }

    /**
     * Get organizingEmails
     *
     * @return string 
     */
    public function getOrganizingEmails()
    {
        return $this->organizingEmails;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Conference
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Conference
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set autopublishing
     *
     * @param boolean $autopublishing
     * @return Conference
     */
    public function setAutopublishing($autopublishing)
    {
        $this->autopublishing = $autopublishing;

        return $this;
    }

    /**
     * Get autopublishing
     *
     * @return boolean 
     */
    public function getAutopublishing()
    {
        return $this->autopublishing;
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
    
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    
    /**
     * @ORM\PrePersist
     */
    /*public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime);
        }
		$this->setUpdateAt(new \DateTime);
    }*/

    /**
     * @ORM\PreUpdate
     */
    /*public function preUpdate()
    {
        $this->setUpdateAt(new \DateTime);
    }*/
    
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
    
    public function __toString()
    {
        return $this->getTitle() ? : 'n/a';
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->delegatesCountries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->assesmentProposedAccomodationDocument = new \Doctrine\Common\Collections\ArrayCollection();
        $this->criterias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set delegatesMoreThan1Country
     *
     * @param integer $delegatesMoreThan1Country
     * @return Conference
     */
    public function setDelegatesMoreThan1Country($delegatesMoreThan1Country)
    {
        $this->delegatesMoreThan1Country = $delegatesMoreThan1Country;

        return $this;
    }

    /**
     * Get delegatesMoreThan1Country
     *
     * @return integer 
     */
    public function getDelegatesMoreThan1Country()
    {
        return $this->delegatesMoreThan1Country;
    }

    /**
     * Set delegatesAnticipate
     *
     * @param string $delegatesAnticipate
     * @return Conference
     */
    public function setDelegatesAnticipate($delegatesAnticipate)
    {
        $this->delegatesAnticipate = $delegatesAnticipate;

        return $this;
    }

    /**
     * Get delegatesAnticipate
     *
     * @return string 
     */
    public function getDelegatesAnticipate()
    {
        return $this->delegatesAnticipate;
    }

    /**
     * Set assesmentNameVenue
     *
     * @param string $assesmentNameVenue
     * @return Conference
     */
    public function setAssesmentNameVenue($assesmentNameVenue)
    {
        $this->assesmentNameVenue = $assesmentNameVenue;

        return $this;
    }

    /**
     * Get assesmentNameVenue
     *
     * @return string 
     */
    public function getAssesmentNameVenue()
    {
        return $this->assesmentNameVenue;
    }

    /**
     * Set assesmentCategoryVenue
     *
     * @param string $assesmentCategoryVenue
     * @return Conference
     */
    public function setAssesmentCategoryVenue($assesmentCategoryVenue)
    {
        $this->assesmentCategoryVenue = $assesmentCategoryVenue;

        return $this;
    }

    /**
     * Get assesmentCategoryVenue
     *
     * @return string 
     */
    public function getAssesmentCategoryVenue()
    {
        return $this->assesmentCategoryVenue;
    }

    /**
     * Set assesmentCity
     *
     * @param string $assesmentCity
     * @return Conference
     */
    public function setAssesmentCity($assesmentCity)
    {
        $this->assesmentCity = $assesmentCity;

        return $this;
    }

    /**
     * Get assesmentCity
     *
     * @return string 
     */
    public function getAssesmentCity()
    {
        return $this->assesmentCity;
    }

    /**
     * Set assesmentLocalNA
     *
     * @param string $assesmentLocalNA
     * @return Conference
     */
    public function setAssesmentLocalNA($assesmentLocalNA)
    {
        $this->assesmentLocalNA = $assesmentLocalNA;

        return $this;
    }

    /**
     * Get assesmentLocalNA
     *
     * @return string 
     */
    public function getAssesmentLocalNA()
    {
        return $this->assesmentLocalNA;
    }

    /**
     * Set assesmentProposedAccomodation
     *
     * @param string $assesmentProposedAccomodation
     * @return Conference
     */
    public function setAssesmentProposedAccomodation($assesmentProposedAccomodation)
    {
        $this->assesmentProposedAccomodation = $assesmentProposedAccomodation;

        return $this;
    }

    /**
     * Get assesmentProposedAccomodation
     *
     * @return string 
     */
    public function getAssesmentProposedAccomodation()
    {
        return $this->assesmentProposedAccomodation;
    }

    /**
     * Set eventProgramme
     *
     * @param string $eventProgramme
     * @return Conference
     */
    public function setEventProgramme($eventProgramme)
    {
        $this->eventProgramme = $eventProgramme;

        return $this;
    }

    /**
     * Get eventProgramme
     *
     * @return string 
     */
    public function getEventProgramme()
    {
        return $this->eventProgramme;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return Conference
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add delegatesCountries
     *
     * @param \OSSystem\EMTBundle\Entity\Country $delegatesCountries
     * @return Conference
     */
    public function addDelegatesCountry(\OSSystem\EMTBundle\Entity\Country $delegatesCountries)
    {
        $this->delegatesCountries[] = $delegatesCountries;

        return $this;
    }

    /**
     * Remove delegatesCountries
     *
     * @param \OSSystem\EMTBundle\Entity\Country $delegatesCountries
     */
    public function removeDelegatesCountry(\OSSystem\EMTBundle\Entity\Country $delegatesCountries)
    {
        $this->delegatesCountries->removeElement($delegatesCountries);
    }

    /**
     * Get delegatesCountries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDelegatesCountries()
    {
        return $this->delegatesCountries;
    }

    /**
     * Set assesmentCountry
     *
     * @param \OSSystem\EMTBundle\Entity\Country $assesmentCountry
     * @return Conference
     */
    public function setAssesmentCountry(\OSSystem\EMTBundle\Entity\Country $assesmentCountry = null)
    {
        $this->assesmentCountry = $assesmentCountry;

        return $this;
    }

    /**
     * Get assesmentCountry
     *
     * @return \OSSystem\EMTBundle\Entity\Country 
     */
    public function getAssesmentCountry()
    {
        return $this->assesmentCountry;
    }


    /**
     * Set isNew
     *
     * @param boolean $isNew
     * @return Conference
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;

        return $this;
    }

    /**
     * Get isNew
     *
     * @return boolean 
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    

    /**
     * Set assesmentProposedAccomodationDocument
     *
     * @param File
     * @return Conference
     */
    public function setAssesmentProposedAccomodationDocument($assesmentProposedAccomodationDocument = null)
    {
        if (!count($this->assesmentProposedAccomodationDocument)){
            $this->assesmentProposedAccomodationDocument[0] = new Document();
            $this->assesmentProposedAccomodationDocument[0]->setConference($this);
            $this->assesmentProposedAccomodationDocument[0]->setTarget( Document::DOCUMENT_TARGET_ACCOMODATION );
        }
        $this->assesmentProposedAccomodationDocument[0]->setFile($assesmentProposedAccomodationDocument, $this->id);

        return $this;
    }

    /**
     * Get assesmentProposedAccomodationDocument
     *
     * @return \OSSystem\EMTBundle\Entity\Document 
     */
    public function getAssesmentProposedAccomodationDocument()
    {
        if (!count($this->assesmentProposedAccomodationDocument)){
            return false;
        }else{
            return $this->assesmentProposedAccomodationDocument[0];
        }
        
    }
    
    /*
     * function to handle files into additional property Document
     */
    public function getAssesmentProposedAccomodationDocumentFile(){
        if ($this->assesmentProposedAccomodationDocument){
            $doc = $this->getAssesmentProposedAccomodationDocument();
            if ($doc)
                return $doc->getFile();
            else
                return false;
        }else{
            return $this->assesmentProposedAccomodationDocumentFile;
        }
    }
    
    public function setAssesmentProposedAccomodationDocumentFile($file){
        //all logics should be at controller
        return $this;
    }

    /**
     * Set startConferenceTime
     *
     * @param string $startConferenceTime
     * @return Conference
     */
    public function setStartConferenceTime($startConferenceTime)
    {
        $this->startConferenceTime = $startConferenceTime;

        return $this;
    }

    /**
     * Get startConferenceTime
     *
     * @return string 
     */
    public function getStartConferenceTime()
    {
        return $this->startConferenceTime;
    }

    /**
     * Set endConferenceTime
     *
     * @param string $endConferenceTime
     * @return Conference
     */
    public function setEndConferenceTime($endConferenceTime)
    {
        $this->endConferenceTime = $endConferenceTime;

        return $this;
    }

    /**
     * Get endConferenceTime
     *
     * @return string 
     */
    public function getEndConferenceTime()
    {
        return $this->endConferenceTime;
    }



    /**
     * creates a document and uploads a file
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     * @return Conference
     */
    public function uploadNewDocument($file, $comment = '' )
    {
        $aDocument = new Document();
        $aDocument->setConference($this);
        $aDocument->setFile($file, $this->id);
        $aDocument->setTarget( Document::DOCUMENT_TARGET_APPENDIX );
        $aDocument->setComment($comment);
            
        $this->documents[] = $aDocument;

        return $this;
    }

    /**
     * Add documents
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     * @return Conference
     */
    public function addDocuments(\OSSystem\EMTBundle\Entity\Document $documents)
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
     * Add assesmentProposedAccomodationDocument
     *
     * @param \OSSystem\EMTBundle\Entity\Document $assesmentProposedAccomodationDocument
     * @return Conference
     */
    public function addAssesmentProposedAccomodationDocument(\OSSystem\EMTBundle\Entity\Document $assesmentProposedAccomodationDocument)
    {
        $this->assesmentProposedAccomodationDocument[] = $assesmentProposedAccomodationDocument;

        return $this;
    }

    /**
     * Remove assesmentProposedAccomodationDocument
     *
     * @param \OSSystem\EMTBundle\Entity\Document $assesmentProposedAccomodationDocument
     */
    public function removeAssesmentProposedAccomodationDocument(\OSSystem\EMTBundle\Entity\Document $assesmentProposedAccomodationDocument)
    {
        $this->assesmentProposedAccomodationDocument->removeElement($assesmentProposedAccomodationDocument);
    }

    /**
     * Add documents
     *
     * @param \OSSystem\EMTBundle\Entity\Document $documents
     * @return Conference
     */
    public function addDocument(\OSSystem\EMTBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Set conferenceStatus
     *
     * @param \OSSystem\EMTBundle\Entity\Status $conferenceStatus
     * @return Conference
     */
    public function setConferenceStatus(\OSSystem\EMTBundle\Entity\Status $conferenceStatus = null)
    {
        $this->conferenceStatus = $conferenceStatus;

        return $this;
    }

    /**
     * Get conferenceStatus
     *
     * @return \OSSystem\EMTBundle\Entity\Status 
     */
    public function getConferenceStatus()
    {
        return $this->conferenceStatus;
    }

    /**
     * Set isPreClearance
     *
     * @param boolean $isPreClearance
     * @return Conference
     */
    public function setIsPreClearance($isPreClearance)
    {
        $this->isPreClearance = $isPreClearance;

        return $this;
    }

    /**
     * Get isPreClearance
     *
     * @return boolean 
     */
    public function getIsPreClearance()
    {
        return $this->isPreClearance;
    }
    
    /**
     * Set archive
     *
     * @param boolean $rchive
     *
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set submissionDate
     *
     * @param \DateTime $submissionDate
     * @return Conference
     */
    public function setSubmissionDate($submissionDate)
    {
        $this->submissionDate = $submissionDate;

        return $this;
    }

    /**
     * Get submissionDate
     *
     * @return \DateTime 
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }
    
    public function getFrontendStatus(){
      
         switch ($this->conferenceStatus)
        {
            case 'Pre-Clearance approved':
            case 'Pre clearance - compliant':
            case 'Compliant':
                return 'Compliant';
            case 'Non compliant for partial submission':
            case 'Not compliant':
                return 'Not compliant';
            case 'Not assessed':
                return 'Not assessed';
            case 'Compliant for partial submission':
            case 'Partially compliant':
            case 'Partial submission – compliant':
                return 'Partially compliant';
//                  return 'Provisional';

            //case 'To be reviewed for Pre-Clearance':
            //case 'Non compliant criteria for partial submission':
            case 'To be reviewed for partial submission':
            case 'Partial submission – not compliant';
            case 'To be reviewed':
            case 'Non compliant criteria':
                return 'To be reviewed';
                
            case 'Info complete':
            case 'Info completed for partial submission':
            case 'Pre-Clearance not approved':
            default:
                return 'Hidden';
        }
    }

    /**
     * Set criterias
     *
     * @param \OSSystem\EMTBundle\Entity\ConferenceCriteriaState $criterias
     * @return Conference
     */
    public function addCriteria(\OSSystem\EMTBundle\Entity\ConferenceCriteriaState $criteriastate = null)
    {
        $this->criterias[] = $criteriastate;
        return $this;
    }

    /**
     * Get criterias
     *
     * @return \OSSystem\EMTBundle\Entity\ConferenceCriteriaState 
     */
    public function getCriterias()
    {
        return $this->criterias;
    }
    
    /**
     * Remove criterias
     *
     * @return \OSSystem\EMTBundle\Entity\ConferenceCriteriaState 
     */
    public function removeCriteria(\OSSystem\EMTBundle\Entity\ConferenceCriteriaState $criteriaState)
    {
        return $this->criterias->removeElement($criteriaState);
    }

    /**
     * Set assessedOnDate
     *
     * @param \DateTime $assessedOnDate
     * @return Conference
     */
    public function setAssessedOnDate($assessedOnDate)
    {
        $this->assessedOnDate = $assessedOnDate;

        return $this;
    }

    /**
     * Get assessedOnDate
     *
     * @return \DateTime 
     */
    public function getAssessedOnDate()
    {
        return $this->assessedOnDate;
    }

    /**
     * Set criterias
     *
     * @param \OSSystem\EMTBundle\Entity\ConferenceCriteriaState $criterias
     * @return Conference
     */
    public function setCriterias(\OSSystem\EMTBundle\Entity\ConferenceCriteriaState $criterias = null)
    {
        $this->criterias = $criterias;

        return $this;
    }

    /**
     * Set conferenceStatusText
     *
     * @param string $conferenceStatusText
     * @return Conference
     */
    public function setConferenceStatusText($conferenceStatusText)
    {
        $this->conferenceStatusText = $conferenceStatusText;

        return $this;
    }

    /**
     * Get conferenceStatusText
     *
     * @return string 
     */
    public function getConferenceStatusText()
    {
        return $this->conferenceStatusText;
    }

    /**
     * Set isCORecommendation
     *
     * @param boolean $isCORecommendation
     * @return Conference
     */
    public function setIsCORecommendation($isCORecommendation)
    {
        $this->isCORecommendation = $isCORecommendation;

        return $this;
    }

    /**
     * Get isCORecommendation
     *
     * @return boolean 
     */
    public function getIsCORecommendation()
    {
        return $this->isCORecommendation;
    }

    /**
     * Set conferenceStatusDate
     *
     * @param \DateTime $conferenceStatusDate
     * @return Conference
     */
    public function setConferenceStatusDate($conferenceStatusDate)
    {
        $this->conferenceStatusDate = $conferenceStatusDate;

        return $this;
    }

    /**
     * Get conferenceStatusDate
     *
     * @return \DateTime 
     */
    public function getConferenceStatusDate()
    {
        return $this->conferenceStatusDate;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Conference
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set snapshot
     *
     * @return Conference
     */
    public function setSnapshot($snapshot)
    {
        $this->snapshot = serialize($snapshot);

        return $this;
    }

    /**
     * Get snapshot
     *
     */
    public function getSnapshot()
    {
        return unserialize($this->snapshot);
    }

    /**
     * Set changedFields
     *
     * @param string $changedFields
     * @return Conference
     */
    public function setChangedFields($changedFields)
    {
        $this->changedFields = serialize($changedFields);

        return $this;
    }

    /**
     * Get changedFields
     *
     * @return string 
     */
    public function getChangedFields()
    {
        return unserialize($this->changedFields);
    }

    /**
     * Set isPartialSubmission
     *
     * @param boolean $isPartialSubmission
     * @return Conference
     */
    public function setIsPartialSubmission($isPartialSubmission)
    {
        $this->isPartialSubmission = $isPartialSubmission;

        return $this;
    }

    /**
     * Get isPartialSubmission
     *
     * @return boolean 
     */
    public function getIsPartialSubmission()
    {
        return $this->isPartialSubmission;
    }

    /**
     * Set therapeuticAreaOther
     *
     * @param string $therapeuticAreaOther
     * @return Conference
     */
    public function setTherapeuticAreaOther($therapeuticAreaOther)
    {
        $this->therapeuticAreaOther = $therapeuticAreaOther;

        return $this;
    }

    /**
     * Get therapeuticAreaOther
     *
     * @return string 
     */
    public function getTherapeuticAreaOther()
    {
        return $this->therapeuticAreaOther;
    }
}
