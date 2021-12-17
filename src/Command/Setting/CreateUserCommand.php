<?php

namespace App\Command\Setting;

use App\Entity\Setting\User;
use App\Repository\Setting\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class CreateUserCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CreateUserCommand extends Command
{
    private UserPasswordHasherInterface $passwordEncoder;

    protected EntityManagerInterface $em;

    private UserRepository $ur;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $em, UserRepository $ur)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->ur = $ur;
        parent::__construct();
    }

    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:create:user');
        $this->setDescription('Create user');
        $this->addArgument('username', InputArgument::REQUIRED, 'Username?');
        $this->addArgument('email', InputArgument::REQUIRED, 'Email?');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password?');
        $this->addArgument('role', InputArgument::OPTIONAL, 'Role?');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
    }

    /**
     * Execute.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $factory = $this->get('security.encoder_factory');
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');

        $user = $this->ur->findOneBy(['username' => $username]);
        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setEnabled(1);
        }
        $pass = $this->passwordEncoder->hashPassword($user, $password);
        $user->setEmail($email);
        $user->setPassword($pass);
        if ($role) {
            $user->addRole($role);
        }

        $this->em->persist($user);
        $this->em->flush();

        return 0;
    }
}
