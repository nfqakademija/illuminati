<?php

namespace Illuminati\OrderBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\OrderBundle\Entity\Order_participants;

class OrderInvitesGenerator
{
    protected $hostOrder;
    protected $participantsEmailsArray;
    protected $em;
    protected $hostOrderInvites = array();

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $hostOrder
     */
    public function setHostOrder($hostOrder)
    {
        $this->hostOrder = $hostOrder;
    }

    /**
     * @param mixed $participantsEmailsArray
     */
    public function setParticipantsEmailsArray($participantsEmailsArray)
    {
        $this->participantsEmailsArray = $participantsEmailsArray;
    }

    /**
     * Generates host order particiapnt's invites
     */
    public function generate()
    {
        if(is_null($this->participantsEmailsArray) || !is_array($this->participantsEmailsArray))
        {
            throw new \Exception("Participants array must be set in order to generate invite list");
        }
        else if(is_null($this->hostOrder))
        {
            throw new \Exception("Host_order must be set in order to generate invite list");
        }
        else if(empty($this->participantsEmailsArray))
        {
            throw new \Exception("Participants array can't be an empty array");
        }
        else if(is_array($this->participantsEmailsArray))
        {
            // Removing duplicate Emails
            $this->participantsEmailsArray = array_unique($this->participantsEmailsArray);
        }

        $validEmails = array();

        foreach ($this->participantsEmailsArray as $email ) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $validEmails[] = $email;
            }
        }

        foreach($validEmails as $email)
        {
            $inviteToken = bin2hex(openssl_random_pseudo_bytes(32));

            $orderParticipant = new Order_participants();
            $orderParticipant->setHostOrderId($this->hostOrder);
            $orderParticipant->setUsersEmail($email);
            $orderParticipant->setInviteToken($inviteToken);

            $this->hostOrderInvites[] = $orderParticipant;
        }
    }

    /**
     * Inserts host order participants into DB
     */
    public function flush()
    {
        if(!empty($this->hostOrderInvites))
        {
            foreach ($this->hostOrderInvites as $participant ) {
                $this->em->persist($participant);
            }
            $this->em->flush();
        }
        else
        {
            throw new \Exception("Can't flush invites to DB, because no invites were generated!");
        }
    }

    /**
     * @return array
     */
    public function getHostOrderInvites()
    {
        return $this->hostOrderInvites;
    }
}