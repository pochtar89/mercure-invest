<?php

// src/Controller/ChatController.php
namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ChatController extends AbstractController
{
    private $security;
    private $entityManager;
    private $hub;

    public function __construct(Security $security, EntityManagerInterface $entityManager, HubInterface $hub)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->hub = $hub;
    }

    #[Route('/chat', name: 'chat', methods: ['GET'])]
    public function list(Request $request, UserRepository $userRepository, MessageRepository $messageRepository): Response
    {
        $receiverId = $request->get('receiverId');
        $receiver = $receiverId ? $userRepository->find($receiverId) : null;
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $messages = array_merge_recursive(
            $messageRepository->findBy(['receiver' => $currentUser, 'sender' => $receiver]),
            $messageRepository->findBy(['receiver' => $receiver, 'sender' => $currentUser])
        );
        usort($messages, function($a, $b) {
            return $a->getCreatedAt() <=> $b->getCreatedAt();
        });

        return $this->render('chat/list.html.twig', [
            'users' => $userRepository->findAll(),
            'currentUser' => $currentUser,
            'receiver' => $receiver,
            'messages' => $messages,
            'chatId' => $receiver instanceof User ? $this->generateChatId($currentUser, $receiver) : '',
        ]);
    }

    #[Route('/chat/send', name: 'chat_send', methods: ['POST'])]
    public function send(Request $request): Response
    {
        $content = $request->request->get('content');
        $receiverId = $request->request->get('receiver_id');
        $receiver = $this->entityManager->getRepository(User::class)->find($receiverId);
        /** @var User $sender */
        $sender = $this->security->getUser();

        $message = new Message();
        $message->setContent($content);
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setCreatedAt(new \DateTime());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $update = new Update(
            $this->generateChatId($sender, $receiver),
            json_encode($message),
            true
        );

        $this->hub->publish($update);

        return new Response('Message sent!');
    }

    private function generateChatId(User $userA, User $userB): string
    {
        $ids = [$userA->getId(), $userB->getId()];
        sort($ids);
        $unique = implode('-', $ids);
        return "https://sibers-mercure.com/chat/$unique";
    }
}
