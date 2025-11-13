<?php

namespace Database\Seeders;

use App\Models\DailyCheckIn;
use App\Models\Goal;
use App\Models\GoalMilestone;
use App\Models\GoalUpdate;
use App\Models\User;
use App\Models\Vision;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JourneySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@loggd.life')->first();

        if (! $user) {
            $this->command->error('Test user not found. Run DatabaseSeeder first.');

            return;
        }

        $this->seedVision($user);
        $this->seedGoals($user);
        $this->seedWeeklyReviews($user);
        $this->seedDailyCheckIns($user);

        $this->command->info('Journey data seeded successfully!');
    }

    private function seedVision(User $user): void
    {
        Vision::updateOrCreate(
            ['user_id' => $user->id],
            [
                'eulogy_method' => "When I'm gone, I want to be remembered as someone who:\n\n- Built something meaningful that helped others grow\n- Was always there for family, present and engaged\n- Never stopped learning and pushing boundaries\n- Inspired others to chase their dreams\n- Lived authentically and took calculated risks",
                'bucket_list' => [
                    ['text' => 'Launch a successful SaaS business', 'completed' => false],
                    ['text' => 'Travel to Japan with family', 'completed' => false],
                    ['text' => 'Build a dream garage workshop', 'completed' => false],
                    ['text' => 'Restore a classic car project', 'completed' => false],
                    ['text' => 'Speak at a tech conference', 'completed' => false],
                    ['text' => 'Write a book about entrepreneurship', 'completed' => false],
                    ['text' => 'Learn to fly a plane', 'completed' => false],
                    ['text' => 'Pay off mortgage completely', 'completed' => false],
                ],
                'mission_prompt' => 'To build products that help people achieve their goals while maintaining a balanced life with my family. To prove that you can be successful without sacrificing what matters most.',
                'definition_of_success' => [
                    'career' => 'Running a profitable business that generates passive income and helps thousands of users',
                    'health' => 'Maintaining fitness through cycling 400km+ per year and regular strength training',
                    'relationships' => 'Being a present, engaged parent and partner. Quality time over quantity',
                    'financial' => 'Mortgage-free by 2028, stable recurring revenue from business',
                    'growth' => 'Constantly learning new technologies and improving my craft',
                    'impact' => 'Creating products that genuinely help people improve their lives',
                ],
                'odyssey_plan' => [
                    'current_path' => "Self: Maintaining health through cycling 400km+ per year. Building discipline and mental clarity.\n\nWork: Building LOGGD and other accountability tools. Balancing contracting with product development. Gradually transitioning to full-time entrepreneur.\n\nRelationships: Present for family while building business. Quality time over quantity. Weekend family adventures.\n\nIn 5-10 years: Running a successful SaaS with steady MRR, working full-time on my own products. Mortgage-free. Dream garage completed.",
                    'alternative_path' => "Self: Fully embracing content creation lifestyle. Speaking, teaching, building in public.\n\nWork: Focus heavily on YouTube, building audience, teaching others to code and build businesses. Course creation. Sponsorships.\n\nRelationships: More flexible schedule allows for better work-life integration. Family included in content occasionally.\n\nIn 5-10 years: Successful YouTube channel with 100k+ subscribers, sponsorships and course sales providing stable income.",
                    'radical_path' => "Self: Living in a warmer climate. Daily outdoor activities. Peak physical and mental health.\n\nWork: Running a portfolio of 5-10 small profitable products. No client work. Full creative freedom. Work 4-6 hours per day.\n\nRelationships: More time for family adventures and travel. Home-schooling kids while traveling. Deep community connections.\n\nIn 5-10 years: Multiple successful products generating passive income. Complete location independence. Financial freedom allowing for philanthropy and mentoring.",
                ],
                'future_calendar' => [
                    'ideal_tuesday' => "Wake up: 6:30 AM naturally, no alarm needed\n\nMorning: Quick workout or bike ride (30 min). Healthy breakfast with family. Kids off to school.\n\n8:30 AM - 12:00 PM: Deep work on LOGGD features. No meetings, no interruptions. In flow state building features that help people.\n\nLunch: 30 min break, walk outside, clear my head\n\n12:30 PM - 3:00 PM: Customer support, team collaboration (hired first employee!), planning next features based on user feedback\n\n3:00 PM: Work ends. Kids home from school. Present and engaged.\n\nEvening: Family dinner. Quality time. No work email checking.\n\n9:00 PM: Personal time - reading, planning, reflecting on the day\n\n10:30 PM: Sleep, feeling accomplished and balanced",
                    'ideal_sunday' => "Wake up: 7:30 AM naturally, well-rested\n\nMorning: Slow coffee with partner. No rush. Kids play while we plan the day together.\n\n10:00 AM: Family adventure - hiking, biking, exploring new places. Active and together.\n\nLunch: Casual meal somewhere new or picnic outdoors\n\nAfternoon: Project time in the garage workshop. Tinkering on the car restoration project. Kids 'helping' and learning.\n\n4:00 PM: Relaxed family time at home. Board games, reading, just being together.\n\nEvening: Home-cooked family dinner. Movie night or quality conversation.\n\n9:00 PM: Reflect on the week in journal. Gratitude practice. Plan the week ahead lightly.\n\n10:00 PM: Sleep, feeling recharged and grateful",
                ],
                'is_public' => true,
                'private_sections' => [],
            ]
        );

        $this->command->info('Vision seeded');
    }

    private function seedGoals(User $user): void
    {
        // Store goal references for parent-child relationships
        $goalRefs = [];

        // 3-Year Goals
        $threeYearGoals = [
            [
                'title' => 'Pay off 69k euro mortgage completely',
                'description' => 'Become mortgage-free by making extra payments from business revenue and contracting income',
                'target_date' => '2028-09-15',
                'life_area' => 'financial',
                'tracking_type' => 'metric',
                'metric_unit' => '€',
                'metric_start_value' => 69000,
                'metric_target_value' => 0,
                'metric_current_value' => 65200,
                'metric_decrease' => true,
                'period_identifier' => '2025-2028',
            ],
            [
                'title' => 'Replace contracting income with own business',
                'description' => 'Build sustainable MRR from LOGGD and other products to fully replace contracting work',
                'target_date' => '2028-09-15',
                'life_area' => 'career',
                'tracking_type' => 'evolution',
                'period_identifier' => '2025-2028',
                'last_update_note' => 'Nov 2025: Launched LOGGD MVP. Business: €0/mo, Contracting: €5,000/mo. Long way to go but first step taken.',
            ],
            [
                'title' => 'Design and organize dream garage setup',
                'description' => 'Create a fully equipped workshop space for car projects and DIY work',
                'target_date' => '2028-09-15',
                'life_area' => 'other',
                'tracking_type' => 'milestone',
                'period_identifier' => '2025-2028',
                'progress_percentage' => 20,
            ],
            [
                'title' => 'Purchase and customize a car project',
                'description' => 'Find and restore a classic car to create something personally meaningful',
                'target_date' => '2028-09-15',
                'life_area' => 'other',
                'tracking_type' => 'evolution',
                'period_identifier' => '2025-2028',
                'last_update_note' => 'Researching EV conversion kits. Leaning toward electric retrofit vs classic restoration.',
            ],
        ];

        foreach ($threeYearGoals as $goalData) {
            $goal = Goal::firstOrCreate(
                ['user_id' => $user->id, 'title' => $goalData['title']],
                array_merge($goalData, [
                    'time_horizon' => '3_year',
                    'status' => 'active',
                    'is_public' => true,
                    'started_at' => '2025-01-01',
                ])
            );

            // Store references for parent-child linking
            $goalRefs[$goalData['title']] = $goal;

            // Add metric updates for mortgage goal
            if ($goal->title === 'Pay off 69k euro mortgage completely') {
                $updates = [
                    ['date' => '2025-01-15', 'value' => 69000, 'note' => 'Starting balance'],
                    ['date' => '2025-04-15', 'value' => 68200, 'note' => 'Q1 payment'],
                    ['date' => '2025-07-15', 'value' => 67100, 'note' => 'Q2 payment + extra from bonus'],
                    ['date' => '2025-10-15', 'value' => 65900, 'note' => 'Q3 payment'],
                    ['date' => '2025-11-13', 'value' => 65200, 'note' => 'Extra payment from contracting'],
                ];

                foreach ($updates as $update) {
                    GoalUpdate::firstOrCreate(
                        ['goal_id' => $goal->id, 'update_date' => $update['date']],
                        [
                            'metric_value' => $update['value'],
                            'note' => $update['note'],
                        ]
                    );
                }
            }

            // Add milestones for garage goal
            if ($goal->title === 'Design and organize dream garage setup') {
                $milestones = [
                    ['title' => 'Research and plan layout', 'completed' => true, 'order' => 0],
                    ['title' => 'Purchase essential tools and equipment', 'completed' => true, 'order' => 1],
                    ['title' => 'Install pegboard and wall storage', 'completed' => false, 'order' => 2],
                    ['title' => 'Set up workbench area', 'completed' => false, 'order' => 3],
                    ['title' => 'Organize parts and materials storage', 'completed' => false, 'order' => 4],
                    ['title' => 'Install proper lighting', 'completed' => false, 'order' => 5],
                    ['title' => 'Final touches and optimization', 'completed' => false, 'order' => 6],
                ];

                foreach ($milestones as $milestone) {
                    GoalMilestone::firstOrCreate(
                        ['goal_id' => $goal->id, 'title' => $milestone['title']],
                        $milestone
                    );
                }
            }

            // Add journal updates for evolution goals
            if ($goal->title === 'Replace contracting income with own business') {
                $updates = [
                    ['date' => '2025-09-01', 'note' => 'Started seriously thinking about what product to build. Too many ideas, need to focus.'],
                    ['date' => '2025-10-15', 'note' => 'Decided on LOGGD - personal development tracking. Something I would use myself.'],
                    ['date' => '2025-11-13', 'note' => 'Nov 2025: Launched LOGGD MVP. Business: €0/mo, Contracting: €5,000/mo. Long way to go but first step taken.'],
                ];

                foreach ($updates as $update) {
                    GoalUpdate::firstOrCreate(
                        ['goal_id' => $goal->id, 'update_date' => $update['date']],
                        ['note' => $update['note']]
                    );
                }
            }

            if ($goal->title === 'Purchase and customize a car project') {
                $updates = [
                    ['date' => '2025-08-20', 'note' => 'Started browsing classic car listings. So many options - BMW 2002, VW Golf Mk1, Porsche 924...'],
                    ['date' => '2025-10-05', 'note' => 'Attended local car meet. Talked to people about their builds. EV conversions are really interesting!'],
                    ['date' => '2025-11-10', 'note' => 'Researching EV conversion kits. Leaning toward electric retrofit vs classic restoration. More aligned with future vision.'],
                ];

                foreach ($updates as $update) {
                    GoalUpdate::firstOrCreate(
                        ['goal_id' => $goal->id, 'update_date' => $update['date']],
                        ['note' => $update['note']]
                    );
                }
            }
        }

        // Yearly Goals (linked to 3-year parents)
        $yearlyGoals = [
            [
                'title' => 'Complete detailed business plan for accountability app',
                'description' => 'Market analysis, competition review, financial projections, and time investment requirements',
                'target_date' => '2025-10-01',
                'life_area' => 'career',
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => Carbon::parse('2025-10-01'),
                'tracking_type' => 'milestone',
                'period_identifier' => '2025',
                'parent_title' => 'Replace contracting income with own business', // Link to 3-year goal
                'milestones' => [
                    ['title' => 'Market research and competitor analysis', 'completed' => true, 'order' => 0],
                    ['title' => 'Define target audience and personas', 'completed' => true, 'order' => 1],
                    ['title' => 'Financial projections and pricing model', 'completed' => true, 'order' => 2],
                    ['title' => 'Go-to-market strategy and timeline', 'completed' => true, 'order' => 3],
                ],
            ],
            [
                'title' => 'Build business flow and user acquisition system',
                'description' => 'Create marketing funnel, landing pages, and customer acquisition strategy',
                'target_date' => '2025-11-15',
                'life_area' => 'career',
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => Carbon::parse('2025-11-12'),
                'tracking_type' => 'milestone',
                'period_identifier' => '2025',
                'parent_title' => 'Replace contracting income with own business', // Link to 3-year goal
            ],
            [
                'title' => 'Acquire and validate concept with first 5 paying customers',
                'description' => 'Launch LOGGD and get initial customer validation',
                'target_date' => '2025-12-31',
                'life_area' => 'career',
                'status' => 'active',
                'progress_percentage' => 45,
                'tracking_type' => 'metric',
                'metric_unit' => '#',
                'metric_start_value' => 0,
                'metric_target_value' => 5,
                'metric_current_value' => 0,
                'metric_decrease' => false,
                'period_identifier' => '2025',
                'parent_title' => 'Replace contracting income with own business', // Link to 3-year goal
            ],
        ];

        foreach ($yearlyGoals as $goalData) {
            $milestones = $goalData['milestones'] ?? null;
            $parentTitle = $goalData['parent_title'] ?? null;
            unset($goalData['milestones']);
            unset($goalData['parent_title']);

            // Set parent_goal_id if parent exists
            $parentGoalId = null;
            if ($parentTitle && isset($goalRefs[$parentTitle])) {
                $parentGoalId = $goalRefs[$parentTitle]->id;
            }

            $goal = Goal::firstOrCreate(
                ['user_id' => $user->id, 'title' => $goalData['title']],
                array_merge($goalData, [
                    'time_horizon' => 'yearly',
                    'parent_goal_id' => $parentGoalId,
                    'is_public' => true,
                    'started_at' => '2025-01-01',
                ])
            );

            // Store reference for quarterly goals
            $goalRefs[$goalData['title']] = $goal;

            // Add milestones if specified
            if ($milestones) {
                foreach ($milestones as $milestone) {
                    GoalMilestone::firstOrCreate(
                        ['goal_id' => $goal->id, 'title' => $milestone['title']],
                        $milestone
                    );
                }
            }
        }

        // Quarterly Goals (Q4 2025) - linked to yearly parents
        $quarterlyGoals = [
            [
                'title' => 'Have a validated business concept with clear execution path',
                'description' => 'Complete validation of LOGGD or alternative concept with clear go-to-market strategy',
                'target_date' => '2025-12-31',
                'life_area' => 'career',
                'tracking_type' => 'milestone',
                'progress_percentage' => 42,
                'period_identifier' => 'Q4_2025',
                'parent_title' => 'Complete detailed business plan for accountability app', // Link to yearly goal
                'milestones' => [
                    ['title' => 'Interview 10 potential users', 'completed' => true, 'order' => 0, 'target_date' => '2025-11-01'],
                    ['title' => 'Build MVP prototype', 'completed' => true, 'order' => 1, 'target_date' => '2025-11-15'],
                    ['title' => 'Test with 5 beta users', 'completed' => false, 'order' => 2, 'target_date' => '2025-12-01'],
                    ['title' => 'Finalize pricing and business model', 'completed' => false, 'order' => 3, 'target_date' => '2025-12-15'],
                    ['title' => 'Create go-to-market strategy', 'completed' => false, 'order' => 4, 'target_date' => '2025-12-31'],
                ],
            ],
            [
                'title' => 'Launch LOGGD MVP and get 5 active users',
                'description' => 'Ship minimum viable product and acquire first real users for feedback',
                'target_date' => '2025-12-01',
                'life_area' => 'career',
                'tracking_type' => 'metric',
                'metric_unit' => '#',
                'metric_start_value' => 0,
                'metric_target_value' => 5,
                'metric_current_value' => 0,
                'metric_decrease' => false,
                'progress_percentage' => 0,
                'period_identifier' => 'Q4_2025',
                'parent_title' => 'Acquire and validate concept with first 5 paying customers', // Link to yearly goal
            ],
            [
                'title' => 'Create 40 YouTube shorts for content strategy',
                'description' => 'Build content pipeline with 2 shorts per day for audience growth',
                'target_date' => '2025-12-31',
                'life_area' => 'career',
                'tracking_type' => 'metric',
                'metric_unit' => '#',
                'metric_start_value' => 0,
                'metric_target_value' => 40,
                'metric_current_value' => 14,
                'metric_decrease' => false,
                'progress_percentage' => 35,
                'period_identifier' => 'Q4_2025',
                'parent_title' => 'Build business flow and user acquisition system', // Link to yearly goal
            ],
        ];

        foreach ($quarterlyGoals as $goalData) {
            $milestones = $goalData['milestones'] ?? null;
            $parentTitle = $goalData['parent_title'] ?? null;
            unset($goalData['milestones']);
            unset($goalData['parent_title']);

            // Set parent_goal_id if parent exists
            $parentGoalId = null;
            if ($parentTitle && isset($goalRefs[$parentTitle])) {
                $parentGoalId = $goalRefs[$parentTitle]->id;
            }

            $goal = Goal::firstOrCreate(
                ['user_id' => $user->id, 'title' => $goalData['title']],
                array_merge($goalData, [
                    'time_horizon' => 'quarterly',
                    'parent_goal_id' => $parentGoalId,
                    'status' => 'active',
                    'is_public' => true,
                    'started_at' => '2025-10-01',
                ])
            );

            // Add milestones if specified
            if ($milestones) {
                foreach ($milestones as $milestone) {
                    GoalMilestone::firstOrCreate(
                        ['goal_id' => $goal->id, 'title' => $milestone['title']],
                        $milestone
                    );
                }
            }
        }

        $this->command->info('Goals seeded');
    }

    private function seedWeeklyReviews(User $user): void
    {
        $reviews = [
            // Week 9 (Nov 10-16) - Most recent
            [
                'week_start' => '2025-11-10',
                'biggest_win' => 'Started working on new SAAS and made good progress. Set a deadline for Dec 1st launch.',
                'biggest_challenge' => 'Had second thoughts about the direction. Need to stay focused and ship something.',
                'what_i_learned' => 'The initial hype phase is exciting but I need systems to push through when motivation drops. Having a deadline helps.',
                'vision_alignment' => 7,
                'next_week_focus' => "More features for the SAAS\n- Make UI responsive\n- Keep it simple and easy to use\n- Add social login\n- Don't overcomplicate\n\n19 days until launch deadline!",
                'total_check_ins' => 5,
                'avg_energy' => 6.8,
                'avg_productivity' => 7.2,
                'avg_mood' => 7.0,
            ],
            // Week 8 (Nov 3-9)
            [
                'week_start' => '2025-11-03',
                'biggest_win' => 'Realized I have a SAAS idea that I would actually use myself - personal growth tracking.',
                'biggest_challenge' => 'Still feeling like progress is zero. 59 days left and anxiety is building.',
                'what_i_learned' => 'Working on something I will use personally gives me better motivation and I understand the problem deeply.',
                'vision_alignment' => 6,
                'next_week_focus' => "Validate if this is the path I want to take\nIf yes - START BUILDING\nNo more second-guessing",
                'total_check_ins' => 4,
                'avg_energy' => 5.5,
                'avg_productivity' => 6.0,
                'avg_mood' => 6.5,
            ],
            // Week 7 (Oct 27-Nov 2)
            [
                'week_start' => '2025-10-27',
                'biggest_win' => 'Picked a topic for long-form video and have overall idea.',
                'biggest_challenge' => 'Really unproductive week. Only 2 shorts created. 65 days left feels urgent.',
                'what_i_learned' => 'I really need to find a way to force myself to work on goals. Current approach isn\'t working.',
                'vision_alignment' => 5,
                'next_week_focus' => "How can I force myself to work on my goals?\nThink of a system and start implementing it",
                'total_check_ins' => 3,
                'avg_energy' => 5.0,
                'avg_productivity' => 4.5,
                'avg_mood' => 5.5,
            ],
            // Week 6 (Oct 20-26)
            [
                'week_start' => '2025-10-20',
                'biggest_win' => 'Created 3 shorts plus 25 intros for the FREE AI series. Some productive outdoor work too.',
                'biggest_challenge' => 'Didn\'t work on long-form video script. Weekend was off but recovered Sunday.',
                'what_i_learned' => 'Need to focus on creating 2 shorts per day consistently. Can\'t rely on weekend catch-up.',
                'vision_alignment' => 6,
                'next_week_focus' => "Create 5 shorts x2\nStart working on the long-form video",
                'total_check_ins' => 6,
                'avg_energy' => 6.5,
                'avg_productivity' => 6.8,
                'avg_mood' => 7.0,
            ],
            // Week 5 (Oct 13-19) - Generated
            [
                'week_start' => '2025-10-13',
                'biggest_win' => 'Mapped out Q4 strategy and identified key milestones. Feeling more clarity.',
                'biggest_challenge' => 'Lost momentum mid-week. Hard to maintain energy after day job.',
                'what_i_learned' => 'Planning helps but execution is harder. Need better energy management.',
                'vision_alignment' => 6,
                'next_week_focus' => "Build consistent content creation habit\nShip 2 shorts minimum",
                'total_check_ins' => 5,
                'avg_energy' => 6.0,
                'avg_productivity' => 5.8,
                'avg_mood' => 6.2,
            ],
            // Week 4 (Oct 6-12) - Generated
            [
                'week_start' => '2025-10-06',
                'biggest_win' => 'Had breakthrough conversation about business model. New perspective on monetization.',
                'biggest_challenge' => 'Spent too much time researching, not enough building. Analysis paralysis.',
                'what_i_learned' => 'Research is important but execution matters more. Ship fast, learn fast.',
                'vision_alignment' => 7,
                'next_week_focus' => "Stop researching, start building\nSet clear weekly targets",
                'total_check_ins' => 6,
                'avg_energy' => 7.2,
                'avg_productivity' => 6.5,
                'avg_mood' => 7.0,
            ],
            // Week 3 (Sept 29-Oct 5) - Generated
            [
                'week_start' => '2025-09-29',
                'biggest_win' => 'Finalized business concept decision. Feels like real progress.',
                'biggest_challenge' => 'Balancing client work with own projects. Time management struggle.',
                'what_i_learned' => 'Need to block specific hours for own projects or they never happen.',
                'vision_alignment' => 6,
                'next_week_focus' => "Create time blocking system\nMornings for own projects",
                'total_check_ins' => 5,
                'avg_energy' => 6.5,
                'avg_productivity' => 7.0,
                'avg_mood' => 6.8,
            ],
            // Week 2 (Sept 22-28) - Generated
            [
                'week_start' => '2025-09-22',
                'biggest_win' => 'Completed competitor analysis. Know exactly what exists and where gaps are.',
                'biggest_challenge' => 'Feeling overwhelmed by how much good competition exists. Imposter syndrome.',
                'what_i_learned' => 'Competition validates the market. I can do this differently and better.',
                'vision_alignment' => 7,
                'next_week_focus' => "Define unique value prop\nWhat makes my approach different?",
                'total_check_ins' => 6,
                'avg_energy' => 6.8,
                'avg_productivity' => 7.5,
                'avg_mood' => 6.5,
            ],
        ];

        foreach ($reviews as $reviewData) {
            $startDate = Carbon::parse($reviewData['week_start']);
            $endDate = $startDate->copy()->addDays(6);

            WeeklyReview::firstOrCreate(
                ['user_id' => $user->id, 'week_start_date' => $startDate->format('Y-m-d')],
                [
                    'week_end_date' => $endDate->format('Y-m-d'),
                    'total_check_ins' => $reviewData['total_check_ins'],
                    'avg_energy' => $reviewData['avg_energy'],
                    'avg_productivity' => $reviewData['avg_productivity'],
                    'avg_mood' => $reviewData['avg_mood'],
                    'biggest_win' => $reviewData['biggest_win'],
                    'biggest_challenge' => $reviewData['biggest_challenge'],
                    'what_i_learned' => $reviewData['what_i_learned'],
                    'vision_alignment' => $reviewData['vision_alignment'],
                    'next_week_focus' => $reviewData['next_week_focus'],
                    'is_public' => false,
                ]
            );
        }

        $this->command->info('Weekly reviews seeded');
    }

    private function seedDailyCheckIns(User $user): void
    {
        // Get goals for linking
        $quarterlyGoals = Goal::where('user_id', $user->id)
            ->where('time_horizon', 'quarterly')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $monthlyGoals = Goal::where('user_id', $user->id)
            ->where('time_horizon', 'monthly')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $allWorkableGoals = array_merge($quarterlyGoals, $monthlyGoals);

        $checkIns = [
            // Day 39 - Nov 13 (today)
            [
                'date' => '2025-11-13',
                'yesterday_priority_completed' => true,
                'yesterday_review' => 'Yesterday was productive! Shipped multiple features and made good progress on LOGGD.',
                'today_priority' => 'Continue building momentum - work on habit integration and polish UI',
                'today_tasks' => ['Integrate habits in daily check-in', 'Fix streak calculation', 'Test new features'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 2),
                'goal_work_details' => 'Working on habit integration and improving the daily check-in experience',
                'day_reflection' => "✅ Great progress on LOGGD features\n✅ Fixed several bugs\n✅ Improved UI polish\n\n🤔 Still lots to do before Dec 1st deadline\n\n🙏 Grateful for the coding flow state today. Making real progress!",
                'mood' => 8,
            ],
            // Day 38 - Nov 12
            [
                'date' => '2025-11-12',
                'today_priority' => 'Focus on SAAS work - continue with the flow, keep building features',
                'today_tasks' => ['Continue LOGGD development', 'Ship responsive design updates'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 2),
                'goal_work_details' => 'Building LOGGD features - making UI responsive and adding social login',
                'day_reflection' => "✅ Reserved Airbnb for city break\n✅ Updated bed listing online\n✅ Good SAAS progress - multiple features shipped\n\n🤔 Still need to take some hours off work but not a full day. Scheduling conflicts.\n\n🙏 Grateful for the progress on LOGGD. Starting to see it come together. 18 days until deadline!",
                'mood' => 7,
            ],
            // Day 37 - Nov 11
            [
                'date' => '2025-11-11',
                'yesterday_priority_completed' => false,
                'yesterday_review' => 'Yesterday was unproductive overall. Did nothing on SAAS. Need to get back on track.',
                'today_priority' => 'Make up for lost day - focus on SAAS coding',
                'today_tasks' => ['Take time off work', 'Book city break', 'Update bed listing', 'SAAS coding'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 1),
                'goal_work_details' => 'Get back into LOGGD development after unproductive day',
                'day_reflection' => "✅ Got outdoor cushions stored away\n\n🤔 Yesterday was unproductive. Did nothing on SAAS. Need to get back on track.\n\n🙏 At least I got the cushions done. Small progress is still progress.",
                'mood' => 5,
            ],
            // Day 36 - Nov 10
            [
                'date' => '2025-11-10',
                'today_priority' => 'Keep SAAS momentum going',
                'today_tasks' => ['Store outdoor cushions', 'Take day off work', 'Update bed listing'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 2),
                'goal_work_details' => 'Continue building LOGGD features and maintain development momentum',
                'day_reflection' => "✅ Got haircut done finally\n✅ Vacuumed all leaves from yard\n✅ Went to Neamt market\n\n🤔 Decent progress overall but feeling the pressure of the deadline approaching.\n\n🙏 Motivated about the new app. The beginning phase energy is good.",
                'mood' => 7,
            ],
            // Day 35 - Nov 6
            [
                'date' => '2025-11-06',
                'today_priority' => 'Continue LOGGD work - just coding, building features',
                'today_tasks' => ['Haircut appointment', 'Code LOGGD features'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 2),
                'goal_work_details' => 'Full day of LOGGD development - building core features',
                'day_reflection' => "✅ Started working on LOGGD project\n✅ Used up all Claude tokens - that's how much I coded!\n\n🤔 Realized there's more work than expected, especially being picky about implementation. But still hyped.\n\n🙏 Excited about building an app I'll actually use daily for tracking my own progress.",
                'mood' => 8,
            ],
            // Day 34 - Nov 5
            [
                'date' => '2025-11-05',
                'today_priority' => 'Create todo list for SAAS and start actual development work',
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 1),
                'goal_work_details' => 'Plan out LOGGD features and start development',
                'day_reflection' => "✅ Called for haircut appointment\n✅ Had breakthrough on SAAS idea - personal growth tracking!\n\n🤔 Need to manage the initial hype and turn it into sustained action.\n\n🙏 Happy to have found a niche that helps my own personal growth. Being the main user is powerful.",
                'mood' => 8,
            ],
            // Day 33 - Nov 4
            [
                'date' => '2025-11-04',
                'today_priority' => 'Get back into goal work',
                'today_tasks' => ['Dentist appointment', 'Think about personal growth SAAS idea'],
                'goals_worked_on' => [],
                'day_reflection' => "✅ Dentist appointment completed\n✅ Called ANAF about taxes\n✅ Spoke with Florin about fence\n\n🤔 Did admin tasks but nothing goal-related. Need to refocus on quarterly target. Still thinking about that personal growth SAAS idea.\n\n🙏 Grateful for getting admin stuff out of the way. Clears mental space.",
                'mood' => 6,
            ],
            // Day 32 - Nov 3
            [
                'date' => '2025-11-03',
                'today_priority' => 'Focus on goal work in the morning when fresh',
                'today_tasks' => ['Dentist early', 'Work on YouTube content'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 1),
                'day_reflection' => "✅ Made progress on YouTube short script\n✅ Organized workspace - feels cleaner\n\n🤔 Hard to find energy after client work. Evening productivity is low.\n\n🙏 Grateful for the contracting income even though it takes time from own projects.",
                'mood' => 6,
            ],
            // Day 31 - Nov 2
            [
                'date' => '2025-11-02',
                'today_priority' => 'Create something tangible - ship a video or write code',
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 2),
                'day_reflection' => "✅ Finished client project milestone\n✅ Researched competitor products\n\n🤔 Spent too much time in research mode. Need to shift to build mode.\n\n🙏 Learning from others is valuable. Good competition validates the market.",
                'mood' => 6,
            ],
            // Day 30 - Nov 1
            [
                'date' => '2025-11-01',
                'today_priority' => 'Research competitors and map out strategy',
                'today_tasks' => ['Client work in morning', 'Research competitors in evening'],
                'goals_worked_on' => $this->getRandomGoals($allWorkableGoals, 1),
                'day_reflection' => "✅ Published one YouTube short\n✅ Mapped out November goals\n\n🤔 New month anxiety. 60 days left feels both long and short.\n\n🙏 Fresh start energy. November can be a productive month.",
                'mood' => 7,
            ],
        ];

        // Generate more days going back to Oct 1
        $additionalDays = $this->generateAdditionalDays($allWorkableGoals);
        $checkIns = array_merge($checkIns, $additionalDays);

        foreach ($checkIns as $checkInData) {
            DailyCheckIn::firstOrCreate(
                ['user_id' => $user->id, 'date' => $checkInData['date']],
                [
                    'yesterday_priority_completed' => $checkInData['yesterday_priority_completed'] ?? null,
                    'yesterday_review' => $checkInData['yesterday_review'] ?? null,
                    'today_priority' => $checkInData['today_priority'] ?? null,
                    'today_tasks' => $checkInData['today_tasks'] ?? [],
                    'goals_worked_on' => $checkInData['goals_worked_on'] ?? [],
                    'goal_work_details' => $checkInData['goal_work_details'] ?? null,
                    'day_reflection' => $checkInData['day_reflection'] ?? null,
                    'mood' => $checkInData['mood'] ?? null,
                    'is_public' => false,
                ]
            );
        }

        $this->command->info('Daily check-ins seeded');
    }

    private function generateAdditionalDays(array $allWorkableGoals): array
    {
        $days = [];
        $priorities = [
            'Work on YouTube content creation',
            'Focus on client project deliverables',
            'Research business ideas and competition',
            'Code features for side project',
            'Take care of admin and household tasks',
            'Review and plan quarterly goals',
            'Ship something small but complete',
            'Deep work on product development',
            'Review and respond to user feedback',
            'Plan next sprint priorities',
            'Refactor technical debt',
            'Write documentation',
            'Update marketing materials',
        ];

        $reflections = [
            "✅ Finished chapter of book I'm reading\n✅ Good workout session completed\n\n🤔 Low energy after long client day\n\n🙏 Grateful for supportive family",
            "✅ Made progress on side project\n✅ Productive client call\n\n🤔 Struggled to start working on own projects. Too much context switching.\n\n🙏 Thankful for the flexibility to work on own projects",
            "✅ Cleaned up codebase\n✅ Shipped a feature\n\n🤔 Distracted by social media. Need better focus.\n\n🙏 Appreciate having stable contract work",
            "✅ Had quality family time\n\n🤔 Felt overwhelmed by todo list. Hard to maintain focus in afternoon.\n\n🙏 Grateful for good health and energy",
            "✅ Fixed annoying bug\n✅ Learned new programming concept\n\n🤔 Perfectionism slowing me down\n\n🙏 Thankful for the opportunity to learn and build",
            "✅ Organized digital files\n✅ Made progress on YouTube script\n\n🤔 Unclear on next steps. Need to review goals.\n\n🙏 Appreciate the peaceful workspace",
            "✅ Published video content\n✅ Got positive feedback from users\n\n🤔 Imposter syndrome creeping in today\n\n🙏 Grateful for a supportive community",
            "✅ Morning run felt amazing\n✅ Productive deep work block\n\n🤔 Struggled with focus in afternoon\n\n🙏 Thankful for flexible work schedule",
            "✅ Connected with mentor\n✅ Got valuable feedback on project\n\n🤔 Feeling behind on timeline\n\n🙏 Grateful for people who challenge me to grow",
            "✅ Shipped small improvement\n✅ Users noticed and appreciated it\n\n🤔 Still lots to do but making progress\n\n🙏 Thankful for the journey, not just destination",
        ];

        $goalDetails = [
            'Working on MVP features and user flow',
            'Researching competition and market fit',
            'Building out core functionality',
            'Creating content for audience building',
            'Planning next milestone',
            'Refining user experience',
            'Testing with beta users',
            'Improving performance and reliability',
        ];

        // Generate days from Oct 1 to Oct 31
        for ($day = 1; $day <= 31; $day++) {
            $date = Carbon::parse("2025-10-{$day}")->format('Y-m-d');

            // Skip some days to be realistic (not every day has a check-in)
            if (rand(1, 10) > 7) {
                continue;
            }

            $workedOnGoals = rand(1, 10) > 4; // 60% chance worked on goals

            $days[] = [
                'date' => $date,
                'today_priority' => $priorities[array_rand($priorities)],
                'today_tasks' => [],
                'goals_worked_on' => $workedOnGoals ? $this->getRandomGoals($allWorkableGoals, rand(1, 2)) : [],
                'goal_work_details' => $workedOnGoals ? $goalDetails[array_rand($goalDetails)] : null,
                'day_reflection' => $reflections[array_rand($reflections)],
                'mood' => rand(5, 8),
            ];
        }

        return $days;
    }

    private function getRandomGoals(array $goalIds, int $count): array
    {
        if (empty($goalIds)) {
            return [];
        }

        shuffle($goalIds);

        return array_slice($goalIds, 0, min($count, count($goalIds)));
    }
}
