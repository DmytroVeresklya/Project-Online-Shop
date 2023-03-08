<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Exception\ConversationInvalidException;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conversations', name: 'conversations.')]
class ConversationController extends AbstractController
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ConversationRepository $conversationRepository,
    ) {
    }

    #[Route('/{id}', name: 'newConversations', methods: ['POST'])]
    public function index(int $id): Response
    {
        $otherUser = $this->userRepository->find($id);
        $currentUser = $this->userRepository->find(103);

        if (is_null($otherUser)) {
            throw new ConversationInvalidException('The user was not found');
        }

        if ($otherUser->getId() === $currentUser->getId()) {
            throw new ConversationInvalidException('That is deep but you cannot create a conversation with yourself');
        }

        $conversation = $this->conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $currentUser->getId()
        );

        if (count($conversation)) {
            throw new ConversationInvalidException('The conversation already exists');
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($currentUser);
        $participant->setConversation($conversation);


        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($participant);
            $this->entityManager->persist($otherParticipant);

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw new ConversationInvalidException($exception->getMessage());
        }

        return $this->json(['id' => $conversation->getId()], Response::HTTP_CREATED);
    }
}
