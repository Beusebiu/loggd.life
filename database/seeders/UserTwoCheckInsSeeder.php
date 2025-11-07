<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTwoCheckInsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get user ID 2
        $user = User::find(2);

        if (!$user) {
            $this->command->error('User ID 2 not found. Please create the user first.');
            return;
        }

        // Get template IDs
        $eulogyTemplate = Template::where('name', 'The Eulogy Method')->first();
        $bucketListTemplate = Template::where('name', 'The Bucket List')->first();
        $missionTemplate = Template::where('name', 'The Mission Prompt')->first();
        $successTemplate = Template::where('name', 'Definition of Success')->first();
        $odysseyTemplate = Template::where('name', 'Odyssey Plan')->first();
        $futureCalendarTemplate = Template::where('name', 'Future Calendar')->first();
        $threeYearTemplate = Template::where('name', '3-Year Goals')->first();
        $yearlyTemplate = Template::where('name', 'Yearly Goals')->first();
        $quarterlyTemplate = Template::where('name', 'Quarterly Goals')->first();
        $weeklyTemplate = Template::where('name', 'Weekly Review')->first();
        $dailyTemplate = Template::where('name', 'Daily Check-in')->first();

        // 1. The Eulogy Method - September 14, 2025 8:04 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $eulogyTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'eulogy' => 'It was a friendly guy, helped others, always cheerful, loving husband, charring father, present in the family life, wise',
                'legacy' => 'Teach my daughter to be independent, strong, to do what she wants in life, to be joyful, help my wife to be stronger.',
                'impact' => 'Honest, funny, happy',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 08:04:00',
            'updated_at' => '2025-09-14 08:04:00',
        ]);

        // 2. The Bucket List - September 14, 2025 8:13 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $bucketListTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'experiences' => 'Travel around the world, see a lot of beaches, take pictures in as many wonderful places as possible',
                'achievements' => 'Have a big modern house with garage, have 2 cars (one family and 1 sport car), have 1M euro liquid, be a successful person, with a respectable business that helps people in some area',
                'people' => 'Spend quality time with family, be present for my daughter\'s important moments',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 08:13:00',
            'updated_at' => '2025-09-14 08:13:00',
        ]);

        // 3. The Mission Prompt - September 14, 2025 8:20 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $missionTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'mission' => 'Help people to achieve more from their life, help them have a balanced life family/work time. The problem is that there are so many motivated people, that procrastinates, or don\'t have a plan in life so they waste a lot of time on unrelated things. To become a mentor for others sounds good, using my life as an example',
                'values' => 'Give life advices, help motivated people to do great things in their life',
                'principles' => 'If I could leave one lasting positive change in the world, it would be to help motivated people to do great things in their life',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 08:20:00',
            'updated_at' => '2025-09-14 08:20:00',
        ]);

        // 4. Definition of Success - September 14, 2025 10:02 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $successTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'personal_success' => 'Random walks, chill in random cities, e.g. Paris citybreak',
                'professional_success' => 'Running my own business, having flexible schedule, ability to delegate tasks',
                'relationships_success' => 'Being surrounded by my wife, having impact for my close ones',
                'fulfillment' => 'I will have no worries, freedom to travel with family, present for my daughter',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 10:02:00',
            'updated_at' => '2025-09-14 10:02:00',
        ]);

        // 5. Odyssey Plan - September 14, 2025 10:08 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $odysseyTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'plan_1' => "Ok, so now I'm 30, working as a developer/contractor, if I will continue this path for 5-10 years, I will have the house paid off, my daughter will be 6-11 years old, most likely I will have 1 more apartment, having a few weeks a year time off to travel, still running by the clock, the family usual guy. Nothing fancy, but I will have everything that a normal person has. So, is a normal life, but quite boring.",
                'plan_2' => "I will run a business, I will make my own time, I will have a more flexible schedule, that will allow my to travel a bit more, I will have the possibility of having a team so I can delegate tasks. I will be able to have grow more, and this will make me more energised. To be able to travel with my family on my daughter vacation, maybe camping",
                'plan_3' => "I will move to another country close to the beach, having a pool, owing an penthouse, running in the morning, making my own schedule, being able to have calls from the beach. That California lifestyle",
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 10:08:00',
            'updated_at' => '2025-09-14 10:08:00',
        ]);

        // 6. Future Calendar - September 14, 2025 10:23 AM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $futureCalendarTemplate->id,
            'type' => 'custom',
            'date' => '2025-09-14',
            'content' => [
                'one_year' => 'Building my business, starting to see traction, more flexible schedule',
                'three_years' => "Ideal Tuesday: Waking up at 7, getting coffee + morning walk, on the beach. At 8 start checking todo list, by 8:30 start work. Deep focus until 11:30. Then break, launch, gym, maybe power-nap, going to the beach (4h30min). At 16:00 back for more work, calls etc until 19:00. After 19 it's time off, dinner, family time.\n\nIdeal Sunday: Lazy day, no actual schedule, just waking up when I feel to, coffee, walking, mainly spending time with the family, without any work time",
                'five_years' => 'Successful business owner, traveling regularly with family, financially free',
                'ten_years' => 'Living the beach lifestyle, complete financial freedom, mentoring others',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-14 10:23:00',
            'updated_at' => '2025-09-14 10:23:00',
        ]);

        // 7. 3-Year Goals - September 15, 2025 9:21 PM
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $threeYearTemplate->id,
            'type' => 'yearly',
            'date' => '2025-09-15',
            'content' => [
                'intro' => "1. Pay off 69k euro mortgage completely by September 2028\n2. Replace contracting income with my own business by September 2028\n3. Design and organise dream garage setup by September 2028\n4. Purchase and customise a car project to create something personally meaningful by September 2028",
                'why' => 'Financial freedom, more time with family, pursuing my passions, building something meaningful',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-15 21:21:00',
            'updated_at' => '2025-09-15 21:21:00',
        ]);

        // 8. Yearly Goals - Current version
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $yearlyTemplate->id,
            'type' => 'yearly',
            'date' => '2025-01-01',
            'content' => [
                'goals' => "1. Complete detailed business plan for accountability app including market analysis, competition review, financial projections, and time investment requirements by October 1, 2025 (DONE)\n2. Goal 2A: Build business flow and user acquisition system by November 15, 2025 (DONE)\n3. Goal 3A: Acquire and validate concept with first 5 paying customers by December 31, 2025 (DONE)\n4. Goal 2B: Identify alternative business concept using same analysis framework by October 31, 2025\n5. Goal 3B: Complete initial validation of alternative concept by December 31, 2025",
                'metrics' => 'First 5 paying customers, validated business concept, clear execution path',
                'obstacles' => 'Staying consistent, avoiding starting too many things without finishing, maintaining motivation',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-01-01 00:00:00',
            'updated_at' => '2025-01-01 00:00:00',
        ]);

        // 9. Quarterly Goals - Q4 (Oct-Dec)
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $quarterlyTemplate->id,
            'type' => 'quarterly',
            'date' => '2025-10-01',
            'content' => [
                'main_target' => 'Have a validated business concept with clear execution path by Dec 31, 2025',
                'supporting_targets' => "- Complete business analysis with go/no-go decision by Oct 1, 2025\n- If proceeding: Build acquisition system by Nov 15, 2025\n- If proceeding: Acquire first 5 customers by Dec 31, 2025\n- If pivoting: Complete alternative validation by Dec 31, 2025",
                'resources_needed' => 'Accountability, consistency, focus on one thing at a time',
            ],
            'version' => 1,
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-10-01 00:00:00',
            'updated_at' => '2025-10-01 00:00:00',
        ]);

        // 10. Weekly Reviews (recurring)
        // Week 5
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $weeklyTemplate->id,
            'type' => 'weekly',
            'date' => '2025-09-13',
            'content' => [
                'quarterly_target' => 'Have a validated business concept with clear execution path by Dec 31, 2025 → 79 days until the end of the year. I\'m on track with YB, posting videos. It\'s slow, but I see the progress. I will need to think of a way of making money, soon.',
                'last_week_tasks' => "- Terminat video 2 de editat + extra B-Roll + postat ✅\n- Create canal nou pentru \"Work with Eusebiu\" ✅\n- Terminat video 1 pentru WWE si postat ✅\n- Filmat un short si postat ✅\n- Tunde cu trimerul marginile gazonului (again) ❌ (A plouat + musafiri)\n- Fixat placaj dressing ✅\n- Adus haine de la curatatorie ✅\n- Dus gunoiul extra in Dacia ❌\n- ➕ Curatat frunzele din curte\n- ➕ Am facut 2 shorturi (deci unul extra)",
                'next_week_goal' => "- Filmeaza un short\n- Gandestete la un nou video-long, script + audio + B-roll",
                'administrative' => "- Mers la BT nasa + mamaia\n- Bani marunti + cadouri",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-13 00:00:00',
            'updated_at' => '2025-09-13 00:00:00',
        ]);

        // Week 6
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $weeklyTemplate->id,
            'type' => 'weekly',
            'date' => '2025-09-20',
            'content' => [
                'quarterly_target' => 'Have a validated business concept with clear execution path by Dec 31, 2025 → 72 days until the end of the year. I\'m still creating content. It was an ok week. But over the weekend I was off, but I\'ve managed to create 2 shorts on Sunday. I need to focus on creating fast 2 shorts a day. Plus to focus to create the next long form video.',
                'last_week_tasks' => "- Filmeaza un short ✅\n- Gandestete la un nou video-long, script + audio + B-roll ❌\n- Mers la BT nasa + mamaia ✅\n- Bani marunti + cadouri ✅\n- ➕ I've created like 3 shorts last week\n- ➕ Some work outside like: trimmer, leaf blower\n- ➕ I've created 25 intro's for the Shorts FREE AI series",
                'next_week_goal' => "- Create 5 shorts x2\n- Start working on the Long form video",
                'administrative' => "- Ziua lu' Eusebiu",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-20 00:00:00',
            'updated_at' => '2025-09-20 00:00:00',
        ]);

        // Week 7
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $weeklyTemplate->id,
            'type' => 'weekly',
            'date' => '2025-09-27',
            'content' => [
                'quarterly_target' => 'Have a validated business concept with clear execution path by Dec 31, 2025 → 65 days..damn, we\'re getting really close. I really need to do something..In 2 months I need to have a clear path to work on 2026, a clear idea. TO VALIDATE!!',
                'last_week_tasks' => "- Create 5 shorts x2 ❌ (Just 2 shorts and no 2x. Really unproductive)\n- Start working on the Long form video ❌ (I've picked a topic, and an overall idea, but that's all)\n- Ziua lu' Eusebiu ✅",
                'next_week_goal' => "- How can I force myself to work on my goals? - think of a way and maybe start implementing it.",
                'administrative' => "- Botez nasa + biserica\n- Cadouri/ scos bani cash\n- Miercuri Stomato\n- Tuns, poate ?!\n- Cu ce te imbraci?",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-09-27 00:00:00',
            'updated_at' => '2025-09-27 00:00:00',
        ]);

        // Week 8
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $weeklyTemplate->id,
            'type' => 'weekly',
            'date' => '2025-10-03',
            'content' => [
                'quarterly_target' => 'Have a validated business concept with clear execution path by Dec 31, 2025 → 59 days..ok, we\'re getting closer, and the progress I feel that\'s 0. I have a new SAAS idea + content, but I need to start it. Plus creating more content..',
                'last_week_tasks' => "- How can I force myself to work on my goals? - think of a way and maybe start implementing it. (I had some ideas, but nothing to apply)\n- Botez nasa + biserica ✅\n- Cadouri/ scos bani cash ✅\n- Miercuri Stomato ✅\n- Tuns, poate ?! ❌\n- Cu ce te imbraci? ✅",
                'next_week_goal' => "- Now I do have an SAAS idea + content, but I need to start..So this week I need to be sure that this is the path that I want to take. If so let's do this! - 10min later I already have second thoughts..",
                'administrative' => "- Stomato Luni\n- Mers la Neamt?\n- Tuns!\n- Schimbat cauciucuri",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-10-03 00:00:00',
            'updated_at' => '2025-10-03 00:00:00',
        ]);

        // 11. Daily Check-ins (recurring)
        // Day 32
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $dailyTemplate->id,
            'type' => 'daily',
            'date' => '2025-11-03',
            'content' => [
                'yesterday_complete' => "✅ Bagaje Botosani\n✅ Haine\n✅ Dus gunoiul",
                'yesterday_analysis' => "Tasks are done. But I really need help and accountability with my goals. I start 10 things and I finish none. Maybe business is not for me?",
                'today_tasks' => "",
                'priority_task' => "Stomato de la 6:30",
                'goal_work_today' => "Yes",
                'goal_work_specific' => "I have no idea what to do..how to get motivated? I even think if business is for me? I have a problem to stay consistent.",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-11-03 00:00:00',
            'updated_at' => '2025-11-03 00:00:00',
        ]);

        // Day 33
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $dailyTemplate->id,
            'type' => 'daily',
            'date' => '2025-11-04',
            'content' => [
                'yesterday_complete' => "✅ Stomato de la 6:30\n➕ sunat la ANAF\n➕ suna Florin cu gardul",
                'yesterday_analysis' => "I did the admin tasks and more, but nothing goal related",
                'today_tasks' => "",
                'priority_task' => "Call Haircut",
                'goal_work_today' => "Yes",
                'goal_work_specific' => "I'm still thinking on that SAAS for personal growth..hm, I need to get back in the game, because I DO NOTHING IN TERMS OF GOALS..",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-11-04 00:00:00',
            'updated_at' => '2025-11-04 00:00:00',
        ]);

        // Day 34
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $dailyTemplate->id,
            'type' => 'daily',
            'date' => '2025-11-05',
            'content' => [
                'yesterday_complete' => "✅ Call Haircut\n✅ I'm still thinking on that SAAS for personal growth (Now at least I have an idea to start with a SAAS, that I also use, it will be nice to see the overtime progress, I like this idea.)",
                'yesterday_analysis' => "Interesting day yesterday.. I did thought a bit on that SAAS idea, and now I have the initial hype. But this is a niche that may also help me for my personal growth, so maybe I should start working on this, because I will be the main initial user of it.",
                'today_tasks' => "",
                'priority_task' => "Nothing to do today",
                'goal_work_today' => "Yes",
                'goal_work_specific' => "Create a todo list for that SAAS and start working on it",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-11-05 00:00:00',
            'updated_at' => '2025-11-05 00:00:00',
        ]);

        // Day 35
        CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $dailyTemplate->id,
            'type' => 'daily',
            'date' => '2025-11-06',
            'content' => [
                'yesterday_complete' => "✅ Create a todo list for that SAAS and start working on it (Good progress here, I've started the work)",
                'yesterday_analysis' => "Yesterday I've started the project, until I've finished Claude tokens - Now I've realized that is a bit of work there, especially if I'm picky about what I want to implement, but I'm still hyped about this app, to track my notes/progress, etc.",
                'today_tasks' => "",
                'priority_task' => "TUNS",
                'goal_work_today' => "Yes",
                'goal_work_specific' => "Continue the work on the LOGGD - not much to say, is just coding",
            ],
            'is_active' => true,
            'is_public' => false,
            'created_at' => '2025-11-06 00:00:00',
            'updated_at' => '2025-11-06 00:00:00',
        ]);

        $this->command->info('User 2 check-ins seeded successfully!');
    }
}
