<?php

namespace OSSystem\EMTBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use OSSystem\EMTBundle\Entity\User;

/**
 * NotificationService
 */
class NotificationService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Send notification email to users from CO
     *
     * @param string $subject
     * @param string $to
     * @param string $templatename
     * @param Conference
     */
    public function sendNotificationEmail($subject, $to, $templatename, $params, $from = null )
    {
        $assets = $this->container->get('templating.helper.assets');
        $em = $this->container->get('doctrine')->getEntityManager();
        $userRepository = $em->getRepository('OSSystemEMTBundle:User');     
        
        foreach ($params as $param){
            var_dump(get_class($param));    
        }
        
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

        if(!$from){    
            $from = $params->getUser()->getEmail();
        }
        $message = \Swift_Message::newInstance();
        
        $message->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->container->get('templating')->render(
                    $templatename,
                    array('conf' => $params, 'signature' => $signature)
                ), 'text/html');
        
        $this->container->get('mailer')->send($message);
    }

    /**
     * @param string $to
     * @param conferece  $params
     */
    public function sendNotificationNewMessage($to, $params = array())
    {
        $subject = 'Ethical Med Tech - New Message';        
        $templatename = 'OSSystemEMTBundle:Default:Notifications/newMessage.html.twig';
        $this->sendNotificationEmail($subject, $to, $templatename, $params);
    }

    /**
     * Send notification "NewMessage" to email
     *
     * @param string $to
     * @param array  $params
     */
    public function sendNotificationAppealMessage($to, $params = array())
    {
        $subject = 'Ethical Med Tech - New appeal'; 
        $templatename = 'OSSystemEMTBundle:Default:Notifications/appealMessage.html.twig';
        $this->sendNotificationEmail($subject, $to, $templatename, $params);
    }
}