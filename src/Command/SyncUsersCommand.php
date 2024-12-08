<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\SyncDataMessageInterface;
use App\Enum\ActionTypeEnum;
use App\Messenger\EntityDataSyncMessage;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class SyncUsersCommand extends Command
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly SerializerInterface $serializer,
    )
    {
        parent::__construct('app:user_service:sync-users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->messageBus->dispatch(new EntityDataSyncMessage(
                action: ActionTypeEnum::CREATE->value,
                data: json_decode($this->serializer->serialize($user, 'json', ['groups' => SyncDataMessageInterface::SERIALIZED_FIELD]), true)
            ));
        }

        (new SymfonyStyle($input, $output))->success('Synced quantity: ' . count($users));

        return 0;
    }
}
