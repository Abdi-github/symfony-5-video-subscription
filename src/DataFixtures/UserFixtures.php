<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\Query\AST\Subselect;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $password_hasher)
    {
        $this->password_hasher = $password_hasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [
            $first_name, $last_name, $email, $password, $subscription_id,
            $subsciption_valid_until, $free_plan_used, $payment_status, $api_key, $roles
        ]) {
            $user = new User();
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_hasher->hashPassword($user, $password));
            $subsciption = $manager->getRepository(Subscription::class)->find($subscription_id);
            $user->setSubscription($subsciption);
            $user->setSubscriptionValidUntil($subsciption_valid_until);
            $user->setFreePlanUsed($free_plan_used);
            $user->setPaymentStatus($payment_status);
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }


    private function getUserData(): array
    {
        return [

            ['Abdi', 'Ahmed', 'aa@gm.com', '111111', 3, (new DateTime())->modify('+1 year'), false, false, 'hjd8dehdh', ['ROLE_ADMIN']],
            ['Mark', 'Wayne', 'mw@gm.com', '111111', 3, (new DateTime())->modify('+1 month'), false, false, null, ['ROLE_ADMIN']],
            ['John', 'Doe', 'jd@gm.com', '111111', 1, (new DateTime())->modify('+7 day'), true, false, null, ['ROLE_USER']],
            ['Anne', 'Doe', 'ad@gm.com', '111111', 1, null, false, false, null, ['ROLE_USER']]

        ];
    }
}
