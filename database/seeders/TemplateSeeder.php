<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Vision Templates
            [
                'name' => 'The Eulogy Method',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '📜',
                'is_system' => true,
                'order' => 1,
                'structure' => [
                    ['id' => 'eulogy', 'label' => 'What would you want people to say at your eulogy?', 'type' => 'textarea', 'rows' => 8],
                    ['id' => 'legacy', 'label' => 'What legacy do you want to leave?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'impact', 'label' => 'What impact do you want to have on the world?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'The Bucket List',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '🪣',
                'is_system' => true,
                'order' => 2,
                'structure' => [
                    ['id' => 'experiences', 'label' => 'Experiences I want to have', 'type' => 'textarea', 'rows' => 8, 'placeholder' => 'List experiences, travels, adventures...'],
                    ['id' => 'achievements', 'label' => 'Things I want to achieve', 'type' => 'textarea', 'rows' => 8, 'placeholder' => 'List accomplishments, milestones...'],
                    ['id' => 'people', 'label' => 'People I want to meet or spend time with', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'The Mission Prompt',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '🎯',
                'is_system' => true,
                'order' => 3,
                'structure' => [
                    ['id' => 'mission', 'label' => 'What is your personal mission statement?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'values', 'label' => 'What are your core values?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'principles', 'label' => 'What principles guide your decisions?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'Definition of Success',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '🏆',
                'is_system' => true,
                'order' => 4,
                'structure' => [
                    ['id' => 'personal_success', 'label' => 'What does personal success mean to you?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'professional_success', 'label' => 'What does professional success look like?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'relationships_success', 'label' => 'What does success in relationships mean?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'fulfillment', 'label' => 'What would make you feel truly fulfilled?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'Odyssey Plan',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '🗺️',
                'is_system' => true,
                'order' => 5,
                'structure' => [
                    ['id' => 'plan_1', 'label' => 'Life Plan 1: Current Path', 'type' => 'textarea', 'rows' => 6, 'placeholder' => 'What does your life look like if you continue on your current path?'],
                    ['id' => 'plan_2', 'label' => 'Life Plan 2: Alternative Path', 'type' => 'textarea', 'rows' => 6, 'placeholder' => 'What if your current path disappeared? What would you do?'],
                    ['id' => 'plan_3', 'label' => 'Life Plan 3: Wild Card', 'type' => 'textarea', 'rows' => 6, 'placeholder' => 'If money and image were no concern, what would you do?'],
                ],
            ],
            [
                'name' => 'Future Calendar',
                'category' => 'vision',
                'behavior' => 'versioned',
                'period' => 'once',
                'icon' => '📅',
                'is_system' => true,
                'order' => 6,
                'structure' => [
                    ['id' => 'one_year', 'label' => 'Where do you want to be in 1 year?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'three_years', 'label' => 'Where do you want to be in 3 years?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'five_years', 'label' => 'Where do you want to be in 5 years?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'ten_years', 'label' => 'Where do you want to be in 10 years?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],

            // Goal Templates
            [
                'name' => '3-Year Goals',
                'category' => 'goal',
                'behavior' => 'versioned',
                'period' => '3-year',
                'icon' => '🎯',
                'is_system' => true,
                'order' => 7,
                'structure' => [
                    ['id' => 'intro', 'label' => 'In 3 years from now I want to...', 'type' => 'textarea', 'rows' => 10, 'placeholder' => 'List your 3-year goals with specific deadlines...'],
                    ['id' => 'why', 'label' => 'Why are these goals important to you?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'Yearly Goals',
                'category' => 'goal',
                'behavior' => 'versioned',
                'period' => 'yearly',
                'icon' => '📆',
                'is_system' => true,
                'order' => 8,
                'structure' => [
                    ['id' => 'goals', 'label' => 'What are your goals for this year?', 'type' => 'textarea', 'rows' => 12, 'placeholder' => 'List your yearly goals with deadlines...'],
                    ['id' => 'metrics', 'label' => 'How will you measure success?', 'type' => 'textarea', 'rows' => 6],
                    ['id' => 'obstacles', 'label' => 'What potential obstacles do you foresee?', 'type' => 'textarea', 'rows' => 6],
                ],
            ],
            [
                'name' => 'Quarterly Goals',
                'category' => 'goal',
                'behavior' => 'versioned',
                'period' => 'quarterly',
                'icon' => '📊',
                'is_system' => true,
                'order' => 9,
                'structure' => [
                    ['id' => 'main_target', 'label' => 'Main Target for this Quarter', 'type' => 'textarea', 'rows' => 4],
                    ['id' => 'supporting_targets', 'label' => 'Supporting Targets', 'type' => 'textarea', 'rows' => 8, 'placeholder' => 'List specific milestones and deadlines...'],
                    ['id' => 'resources_needed', 'label' => 'Resources or support needed', 'type' => 'textarea', 'rows' => 4],
                ],
            ],

            // Review Templates
            [
                'name' => 'Weekly Review',
                'category' => 'review',
                'behavior' => 'recurring',
                'period' => 'weekly',
                'icon' => '📋',
                'is_system' => true,
                'order' => 10,
                'structure' => [
                    ['id' => 'quarterly_target', 'label' => 'Quarterly Target (Remind)', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'Your main goal for the quarter, and how do you feel about it overall'],
                    ['id' => 'last_week_tasks', 'label' => 'Last Week\'s Tasks (Reflect)', 'type' => 'textarea', 'rows' => 6, 'placeholder' => 'Task 1: Status, obstacles, learning...'],
                    ['id' => 'next_week_goal', 'label' => 'Next Week\'s Goal Work (Plan)', 'type' => 'textarea', 'rows' => 6, 'placeholder' => '2-3 specific outcomes that advance your quarterly target'],
                    ['id' => 'administrative', 'label' => 'Administrative Tasks (Plan)', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'Administrative or operational items that need attention'],
                ],
            ],
            [
                'name' => 'Daily Check-in',
                'category' => 'review',
                'behavior' => 'recurring',
                'period' => 'daily',
                'icon' => '✅',
                'is_system' => true,
                'order' => 11,
                'structure' => [
                    ['id' => 'yesterday_complete', 'label' => 'Did I complete what I planned yesterday? (Yes/No + brief note)', 'type' => 'textarea', 'rows' => 3],
                    ['id' => 'yesterday_analysis', 'label' => 'What prevented completion or went better than expected?', 'type' => 'textarea', 'rows' => 4],
                    ['id' => 'today_tasks', 'label' => 'General tasks for today', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'List 3-5 items'],
                    ['id' => 'priority_task', 'label' => 'Priority task (The one thing that must get done)', 'type' => 'textarea', 'rows' => 2],
                    ['id' => 'goal_work_today', 'label' => 'Will I work on my quarterly goal today? (Yes/No)', 'type' => 'select', 'options' => ['Yes', 'No']],
                    ['id' => 'goal_work_specific', 'label' => 'If yes: What specific work will I do?', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'Be specific - "research competitors" not "work on business"'],
                ],
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
