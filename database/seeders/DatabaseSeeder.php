<?php

namespace Database\Seeders;

use App\Enums\Calibre;
use App\Enums\MembershipStatus;
use App\Enums\PaymentPeriod;
use App\Models\ClassCategory;
use App\Models\EmergencyContact;
use App\Models\Event;
use App\Models\JuniorMember;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipRenewal;
use App\Models\MembershipType;
use App\Models\Role;
use App\Models\Score;
use App\Models\Season;
use App\Models\Shoot;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    protected $faker = null;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->faker = Container::getInstance()->make(Generator::class);

        Role::factory(1)->create();
        Member::factory(20)->has(
            EmergencyContact::factory()
            ->count(1)
            ->state(function (array $attributes, Member $member) {
                $name = fake()->firstName . ' ' . $member->last_name;
                return [
                    'name' => $name,
                    'member_id' => $member->id,
                    'address_line1' => $member->address_line1,
                    'address_line2' => $member->address_line2,
                    'city' => $member->city,
                    'county' => $member->county,
                    'postcode' => $member->postcode,
                ];
            })
        )->create();
        JuniorMember::factory(5)->create();
        $this->createMembershipTypes();
        $this->createMemberships();
        $this->createMembershipRenewals();
        Season::factory(1)->create();
        $this->createClasses();
        $this->createEvents();
        $this->createShoots();
        $this->handleClassCategoryShootData();
        $this->handleScoreData();
    }

    /**
     * @return void
     */
    private function createMembershipTypes(): void
    {
        foreach (PaymentPeriod::cases() as $paymentPeriod) {
            switch ($paymentPeriod->value) {
                case PaymentPeriod::LIFETIME->value:
                    $cost = 1200;
                    break;
                case PaymentPeriod::ANNUAL->value:
                    $cost = 120;
                    break;
                case PaymentPeriod::QUARTERLY->value:
                    $cost = 50;
                    break;
                case PaymentPeriod::MONTHLY->value:
                    $cost = 20;
                    break;
                default:
                    $cost = 2400;
                    break;
            }
            MembershipType::factory()->create([
                'name' => 'Adult ' . $paymentPeriod->value,
                'payment_period' => $paymentPeriod->value,
                'cost' => $cost,
            ]);
            MembershipType::factory()->create([
                'name' => 'Junior ' . $paymentPeriod->value,
                'payment_period' => $paymentPeriod->value,
                'cost' => (($cost / 5) * 4),
            ]);
        }
    }

    /**
     * @return void
     */
    private function createMemberships(): void
    {
        $members = Member::with('juniorMembers')->get()->all();

        foreach ($members as $member) {
            $membershipType = MembershipType::where('name', 'LIKE', 'Adult %')->get()->random();;
            $joinedDate = Carbon::parse($this->faker->dateTimeBetween('-1 years', 'now'), 'UTC');
            $status = $this->generateMembershipStatus($membershipType->payment_period, $joinedDate);

            Membership::factory()->create([
                'member_id' => $member->id,
                'member_type' => $member::class,
                'membership_type_id' => $membershipType->id,
                'joined_date' => $joinedDate,
                'status' => $status,
            ]);

            if (!empty($member->juniorMembers)) {
                foreach ($member->juniorMembers as $juniorMember) {
                    $membershipType = MembershipType::where('name', 'LIKE', 'Junior %')->get()->random();;
                    $status = $this->generateMembershipStatus($membershipType->payment_period, $joinedDate);

                    Membership::factory()->create([
                        'member_id' => $juniorMember->id,
                        'member_type' => $juniorMember::class,
                        'membership_type_id' => $membershipType->id,
                        'joined_date' => $joinedDate,
                        'status' => $status,
                    ]);
                }
            }
        }
    }

    /**
     * @param  string  $paymentPeriod
     * @param $joinedDate
     * @return string
     */
    private function generateMembershipStatus(string $paymentPeriod, $joinedDate): string
    {
        $status = MembershipStatus::ACTIVE->value;

        $now = CarbonImmutable::now('UTC');
        switch ($paymentPeriod) {
            case PaymentPeriod::LIFETIME->value:
                return $this->faker->randomElement([
                    MembershipStatus::ACTIVE->value,
                    MembershipStatus::INACTIVE->value,
                    MembershipStatus::CANCELLED->value,
                ]);
            case PaymentPeriod::ANNUAL->value:
                if ($joinedDate->lte($now->subYear()->startOfDay())) {
                    $status = $this->faker->randomElement([
                        MembershipStatus::INACTIVE->value,
                        MembershipStatus::CANCELLED->value,
                        MembershipStatus::OVERDUE->value,
                    ]);
                }
                return $status;
            case PaymentPeriod::QUARTERLY->value:
                if ($joinedDate->lte($now->subQuarter()->startOfDay())) {
                    $status = $this->faker->randomElement([
                        MembershipStatus::INACTIVE->value,
                        MembershipStatus::CANCELLED->value,
                        MembershipStatus::OVERDUE->value,
                    ]);
                }
                return $status;
            case PaymentPeriod::MONTHLY->value:
                if ($joinedDate->lte($now->subMonth()->startOfDay())) {
                    $status = $this->faker->randomElement([
                        MembershipStatus::INACTIVE->value,
                        MembershipStatus::CANCELLED->value,
                        MembershipStatus::OVERDUE->value,
                    ]);
                }
                return $status;
            default:
                return $status;
        }
    }


    /**
     * @return void
     */
    private function createMembershipRenewals(): void
    {
        $members = Member::with('juniorMembers.membership.membershipType')
            ->with('membership.membershipType')
            ->get()
            ->all();

        foreach ($members as $member) {
            $membership = $member->membership->toArray();
            $joinedDate = CarbonImmutable::parse($membership['joined_date'], 'UTC');
            $status = $membership['status'];
            $paymentPeriod = $membership['membership_type']['payment_period'];
            $renewalDateData = $this->getRenewalDateData($joinedDate, $status, $paymentPeriod);

            MembershipRenewal::factory()->create([
                'membership_id' => $membership['id'],
                'start_date' => $renewalDateData['startDate'],
                'renewal_date' => $renewalDateData['renewalDate'],
                'reminder_date' => $renewalDateData['reminderDate'],
                'paid_date' => $renewalDateData['paidDate'],
            ]);

            if (!empty($member->juniorMembers)) {
                foreach ($member->juniorMembers as $juniorMember) {
                    $juniorMembership = $juniorMember->membership->toArray();
                    $joinedDate = CarbonImmutable::parse($juniorMembership['joined_date'], 'UTC');
                    $status = $juniorMembership['status'];
                    $paymentPeriod = $juniorMembership['membership_type']['payment_period'];
                    $juniorRenewalDateData = $this->getRenewalDateData($joinedDate, $status, $paymentPeriod);

                    MembershipRenewal::factory()->create([
                        'membership_id' => $juniorMember->id,
                        'start_date' => $juniorRenewalDateData['startDate'],
                        'renewal_date' => $juniorRenewalDateData['renewalDate'],
                        'reminder_date' => $juniorRenewalDateData['reminderDate'],
                        'paid_date' => $juniorRenewalDateData['paidDate'],
                    ]);
                }
            }
        }
    }

    /**
     * @param  CarbonImmutable  $joinedDate
     * @param  string  $status
     * @param  string  $paymentPeriod
     * @return array
     */
    private function getRenewalDateData(CarbonImmutable $joinedDate, string $status, string $paymentPeriod): array
    {
        $return = [
            'startDate' => $joinedDate->format('Y-m-d'),
            'renewalDate' => null,
            'reminderDate' => null,
            'paidDate' => $joinedDate->format('Y-m-d'),
        ];

        return match ($status) {
            MembershipStatus::ACTIVE->value,
            MembershipStatus::CANCELLED->value,
            MembershipStatus::INACTIVE->value => $this->generatePaidRenewal(
                $joinedDate,
                $paymentPeriod
            ),
            MembershipStatus::OVERDUE->value => $this->generateOverdueRenewal($joinedDate, $paymentPeriod),
            default => $return,
        };
    }

    /**
     * @param  CarbonImmutable  $joinedDate
     * @param  string  $paymentPeriod
     * @return array
     */
    private function generatePaidRenewal(CarbonImmutable $joinedDate, string $paymentPeriod): array
    {
        $now = Carbon::now('UTC');
        switch ($paymentPeriod) {
            case PaymentPeriod::ANNUAL->value:
                return [
                    'startDate' => $joinedDate->format('Y-m-d'),
                    'renewalDate' => $joinedDate->addYear()->format('Y-m-d'),
                    'reminderDate' => $joinedDate->addMonths(11)->format('Y-m-d'),
                    'paidDate' => $joinedDate->format('Y-m-d'),
                ];
            case PaymentPeriod::QUARTERLY->value:
                return [
                    'startDate' => $now->subMonth()->format('Y-m-d'),
                    'renewalDate' => $now->addMonths(2)->format('Y-m-d'),
                    'reminderDate' => $now->format('Y-m-d'),
                    'paidDate' => $now->subMonth()->format('Y-m-d'),
                ];
            case PaymentPeriod::MONTHLY->value:
                return [
                    'startDate' => $now->subWeek()->format('Y-m-d'),
                    'renewalDate' => $now->addWeeks(3)->format('Y-m-d'),
                    'reminderDate' => $now->addWeeks(2)->format('Y-m-d'),
                    'paidDate' => $now->subWeek()->format('Y-m-d'),
                ];
            default:
                return [
                'startDate' => $joinedDate->format('Y-m-d'),
                'renewalDate' => null,
                'reminderDate' => null,
                'paidDate' => $joinedDate->format('Y-m-d'),
            ];
        }
    }

    /**
     * @param  CarbonImmutable  $joinedDate
     * @param  string  $paymentPeriod
     * @return array
     */
    private function generateOverdueRenewal(CarbonImmutable $joinedDate, string $paymentPeriod): array
    {
        $now = Carbon::now('UTC');
        switch ($paymentPeriod) {
            case PaymentPeriod::ANNUAL->value:
                return [
                    'startDate' => $joinedDate->format('Y-m-d'),
                    'renewalDate' => $joinedDate->addYear()->format('Y-m-d'),
                    'reminderDate' => $joinedDate->addMonths(11)->format('Y-m-d'),
                    'paidDate' => null,
                ];
            case PaymentPeriod::QUARTERLY->value:
                return [
                    'startDate' => $now->subMonth()->format('Y-m-d'),
                    'renewalDate' => $now->addMonths(2)->format('Y-m-d'),
                    'reminderDate' => $now->format('Y-m-d'),
                    'paidDate' => null,
                ];
            case PaymentPeriod::MONTHLY->value:
                return [
                    'startDate' => $now->subWeek()->format('Y-m-d'),
                    'renewalDate' => $now->addWeeks(3)->format('Y-m-d'),
                    'reminderDate' => $now->addWeeks(2)->format('Y-m-d'),
                    'paidDate' => null,
                ];
            default:
                return [
                    'startDate' => $joinedDate->format('Y-m-d'),
                    'renewalDate' => null,
                    'reminderDate' => null,
                    'paidDate' => null,
                ];
        }
    }

    /**
     * @return void
     */
    private function createClasses(): void
    {
        $classCategories = [
            'Open Class',
            'Ladies Class',
            'Veterans Class',
            'Juniors Class',
            'Recoil Class',
            '22 Class',
            'Sticks Class',
        ];

        foreach ($classCategories as $classCategory) {
            ClassCategory::factory()->create([
                'name' => $classCategory,
            ]);
        }
    }

    /**
     * @return void
     */
    private function createShoots(): void
    {
        $seasons = Season::all();
        foreach ($seasons as $season) {
            $seasonStart = Carbon::parse($season->start_date)->startOfMonth();
            $seasonEnd = Carbon::parse($season->end_date);
            $seasonPeriod = CarbonPeriod::create($seasonStart, '1 month', $seasonEnd);

            foreach ($seasonPeriod as $schedule) {

                if (!$schedule->isSunday()) {
                    $schedule->nextWeekendDay();
                    if ($schedule->isSaturday()) {
                        $schedule->addDay();
                    }
                }

                Shoot::factory()->create([
                    'name' => 'Club Shoot',
                    'date' => $schedule->format('Y-m-d'),
                    'start_time_utc' => '10:00:00',
                    'end_time_utc' => '14:30:00',

                ]);
            }
        }
    }

    private function handleClassCategoryShootData(): void
    {
        $shoots = Shoot::all();
        $classCategories = ClassCategory::all();
        foreach ($shoots as $shoot)
        {
            $insertArray = [];
            foreach ($classCategories as $classCategory) {
                $insertArray[] = [
                    'class_category_id' => $classCategory->id,
                    'shoot_id' => $shoot->id
                ];
            }
            DB::table('class_category_shoot')->insert($insertArray);
        }
    }

    private function handleScoreData()
    {
        $members = Member::all();
        $juniorMembers = JuniorMember::all();
        $shoots = Shoot::all();

        foreach ($members as $member) {
            $memberClass = ClassCategory::where('name', 'NOT LIKE', 'Junior%')->get()->random();
            $this->generateMemberShootScoreData($member, $shoots, $memberClass);
        }
        foreach ($juniorMembers as $member) {
            $memberClass = ClassCategory::where('name', 'LIKE', "Junior%")->get()->first();
            $this->generateMemberShootScoreData($member, $shoots, $memberClass);
        }
    }

    private function generateMemberShootScoreData(mixed $member, Collection $shoots, ClassCategory $memberClass): void
    {
        if ($memberClass->name == '22 Class') {
           $calibre = Calibre::TWO_TWO->value;
        } else {
            $calibre = $this->faker->randomElement([
                Calibre::ONE_SEVEN_SEVEN->value,
                Calibre::TWO_TWO->value
            ]);
        }

        foreach ($shoots as $shoot) {
            Score::factory()->create([
                'member_id' => $member->id,
                'member_type' => $member::class,
                'shoot_id' => $shoot->id,
                'class_category_id' => $memberClass->id,
                'score' => random_int(24, 60),
                'calibre' => $calibre
            ]);
        }
    }

    /**
     * @return void
     */
    private function createEvents(): void
    {
        $seasons = Season::all();
        foreach ($seasons as $season) {
            Event::factory(3)->create([
                'season_id' => $season->id,
            ]);
        }

    }

}
