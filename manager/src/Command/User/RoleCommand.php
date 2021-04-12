<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\Entity\User\Role as RoleValue;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleCommand extends Command
{
    private UserFetcher $users;
    private ValidatorInterface $validator;
    private Role\Handler $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Role\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:role')
            ->setDescription('Changes user role');
    }

    /**
     * Консольная команда для изменения роли пользователей.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findForEmail($email)) {
            throw new LogicException('User is not found');
        }

        $command = new Role\Command($user->id);

        $roles = [
            RoleValue::USER,
            RoleValue::ADMIN,
        ];

        $command->role = $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage() . '</error>');
            }
            return Command::FAILURE;
        }

        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
