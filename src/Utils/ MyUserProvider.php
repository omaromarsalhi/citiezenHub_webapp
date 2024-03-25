<?php
class MyUserProvider implements UserProviderInterface
{
        private $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }

        public function loadUserByUsername(string $email): User
        {
            return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        }

}
