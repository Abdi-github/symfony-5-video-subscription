<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getSubscriptionData() as [$user_id, $plan, $price, $valid_until, $payment_status, $free_plan_used]) {
            $subscription = new Subscription();
            $subscription->setPlan($plan);
            $subscription->setPrice($price);
            $subscription->setValidUntil($valid_until);
            $subscription->setPaymentStatus($payment_status);
            $subscription->setFreePlanUsed($free_plan_used);

            $user = $manager->getRepository(User::class)->find($user_id);
            $user->setSubscription($subscription);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getSubscriptionData(): array
    {
        return [

            [3, 'Basic', 0, (new \Datetime())->modify('+1 month'), 'not-paid', true],
            [4, 'Premium', 24.99, (new \Datetime())->modify('+1 month'), 'paid', true],
            [1, 'Cinematic', 39.99, (new \Datetime())->modify('+100 year'), 'paid', false], //  admin
            [2, 'Cinematic', 39.99, (new \Datetime())->modify('+100 year'), 'paid', false] //  admin

        ];
    }
}
