<?php

namespace OSSystem\EMTBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use OSSystem\EMTBundle\Entity\Message;
use OSSystem\EMTBundle\Entity\User;

/**
 * SaveMail
 */
class SaveMailService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    
    protected $em;
        
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, \Doctrine\ORM\EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    
    public function MailToMessage($subject, $to, $templatename, $params, $messageTarget = 2) 
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $sc = $this->container->get('security.context');

        $userRepository = $em->getRepository('OSSystemEMTBundle:User');        
        $conferenceLocation = $params->getAssesmentCountry()->getLocation();
        $signature = "Christine SAINVIL";
        switch($conferenceLocation){
            case 'EU':
            default:
                $user = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_EUCOMED));
                $signature = "Christine SAINVIL";
                break;
            case 'ME':
                $user = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_MIDDLE_EAST));  
                $signature = "Arwa Asiri";
                break; 
        };
        
        if($to === NULL){
            $to = $params->getUser()->getEmail();
        }
        $message= new Message();

        $message->setSubject($subject);
        $message->setContent($this->container->get('templating')->render(
                    $templatename,
                    array('conf' => $params, 'signature' => $signature))
                );
        $message->setSender($user);
        $message->setRecipient($params->getUser());
        $message->setConference($params);
        $message->setMailto($to);
        if ($messageTarget === 1) {
            $message->setTarget(Message::MESSAGE_TARGET_APPEAL_BOARD);
        } else {
            $message->setTarget(Message::MESSAGE_TARGET_MAIL_BOARD);
        }
                
        $em->persist($message);
        $em->flush();
        
    }
    
}
