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
        foreach ($this->getSubscriptionData() as [$plan, $user_id, $price, $valid_until, $resolution, $availabitity, $device, $support]) {
            $subscription = new Subscription();
            $subscription->setPlan($plan);
            $subscription->setPrice($price);
            $subscription->setValidUntil($valid_until);
            $subscription->setResolution($resolution);
            $subscription->setAvailability($availabitity);
            $subscription->setDevice($device);
            $subscription->setSupport($support);


            $user = $manager->getRepository(User::class)->find($user_id);
            $subscription->setUser($user);

            $manager->persist($subscription);
        }

        $manager->flush();
    }

    private function getSubscriptionData(): array
    {
        return [

            ['basic', 3, 0, '7 days', '720 p', 'limited', 'desktop only', 'limited support'],
            ['premium', 2, 24.99, '1 month', 'Full HD', 'lifetime', 'TV and Desktop', '24/7 Support'],
            ['cinematic', 1, 39.99, '2 months', 'Ultra HD', 'lifetime', 'Any Device', '24/7 Support'],


        ];
    }
}
