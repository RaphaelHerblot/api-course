<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Enocdeur de mot de passe
     *
     * @var [type]
     */
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');


        for($u = 0; $u < 10; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, "password");
            
            $chrono = 1;

            $user->setFirstName($faker->firstName())
            ->setlastName($faker->lastName())
            ->setEmail($faker->email())
            ->setPassword($hash);

            $manager->persist($user);

            for($c = 0; $c < mt_rand(5, 20); $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                ->setlastName($faker->lastName())
                ->setCompany($faker->company())
                ->setEmail($faker->email())
                ->setUser($user);

                $manager->persist($customer);

                for($i = 0; $i < mt_rand(3, 10); $i++) {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                    ->setSentAt($faker->dateTimeBetween('-6 months'))
                    ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                    ->setCustomer($customer)
                    ->setChrono($chrono);

                    $chrono++;

                    $manager->persist($invoice);
                }
            }
        }
        $manager->flush();
    }
}