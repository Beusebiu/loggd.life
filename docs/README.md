# LOGGD - Personal Growth Tracking Platform

## PROJECT OVERVIEW

**Product Name:** Loggd  
**Domain:** loggd.life  
**Tagline:** "Log your life. Watch yourself grow."  
**Target User:** Solo builders, remote workers, and self-trackers who already track their lives but get no visual or emotional payoff from their data

## CORE CONCEPT

A flexible personal growth OS that combines daily logging, visual progress tracking (GitHub-style contribution graph), habit tracking, video journaling, and optional social accountability. The system adapts to the user's workflow.

**Key Differentiator:** Combines flexible progress tracking + visual contribution graph + video journaling + habit checkboxes + customizable review structures + optional public profiles in one minimal interface.

---

## DESIGN SYSTEM

### VISUAL STYLE
**Aesthetic:** Minimal, social-media inspired layout  
**Philosophy:** Clean, fast, unobtrusive - focus on content not chrome

**Color Palette:**
- **Primary Background:** #FFFFFF (white)
- **Secondary Background:** #F5F5F5 (light gray)
- **Text Primary:** #000000 (black)
- **Text Secondary:** #666666 (medium gray)
- **Borders/Dividers:** #E0E0E0 (light gray)
- **Accent Colors** (used sparingly):
  - Green: #10B981 (success, contribution graph, streaks)
  - Blue: #3B82F6 (links, actions)
  - Red: #EF4444 (delete, warnings)
  - Yellow: #F59E0B (highlights, pending)

**Typography:**
- Font: Inter or SF Pro (system fonts for performance)
- Sizes: 14px (body), 16px (comfortable reading), 12px (meta/secondary)
- Weights: Regular (400), Medium (500), Semibold (600)
- Line height: 1.5 for body text

**Layout Principles:**
- Social media feed style (Instagram/Twitter inspiration)
- Single column on mobile, 2-3 columns on desktop
- Generous white space
- Card-based design for entries
- Sticky header and navigation
- Bottom navigation on mobile

**Component Style:**
- Subtle shadows (0-4px blur, very light)
- 8px border radius on cards
- 4px border radius on buttons/inputs
- No heavy borders (1px max)
- Hover states: subtle gray background (#F9FAFB)

---

## APPLICATION STRUCTURE & ROUTING

### URL STRUCTURE

**Public URLs (not logged in):**
- `loggd.life` - Landing page (marketing homepage)
- `loggd.life/login` - Login page
- `loggd.life/register` - Sign up page
- `loggd.life/@username` - Public user profiles (anyone can view)

**App URLs (logged in, auth required):**
- `loggd.life/app` - Main dashboard (redirects here after login)
- `loggd.life/app/habits` - Habits tracking page
- `loggd.life/app/check-ins` - Check-ins page (daily, weekly, etc.)
- `loggd.life/app/timeline` - Timeline feed view
- `loggd.life/app/stats` - Stats and analytics page
- `loggd.life/app/settings` - User settings

**Smart Redirect Logic:**
- User visits `loggd.life` while logged in → Auto-redirect to `/app`
- User visits `loggd.life/app` without login → Redirect to `/login`
- After login → Redirect to `/app` (dashboard)

### ROUTE IMPLEMENTATION (Laravel)

```php
// Public routes
Route::get('/', function () {
    // Smart redirect: logged in users go to app, others see landing
    return Auth::check() ? redirect('/app') : view('landing.index');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public profiles (anyone can view)
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile.public');

// Protected app routes (requires authentication)
Route::middleware(['auth'])->prefix('app')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/habits', [HabitsController::class, 'index'])->name('habits');
    Route::get('/check-ins', [CheckInsController::class, 'index'])->name('check-ins');
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
});

// API routes (in routes/api.php)
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    // All API endpoints here
});
```

### FILE STRUCTURE

```
resources/
├── views/
│   ├── landing/
│   │   └── index.blade.php          # Marketing homepage (public)
│   ├── auth/
│   │   ├── login.blade.php          # Login form
│   │   └── register.blade.php       # Registration form
│   ├── app/
│   │   ├── layout.blade.php         # App shell (nav, sidebar, footer)
│   │   ├── dashboard.blade.php      # Main dashboard
│   │   ├── habits.blade.php         # Habits page
│   │   ├── check-ins.blade.php      # Check-ins page
│   │   ├── timeline.blade.php       # Timeline feed
│   │   ├── stats.blade.php          # Stats page
│   │   └── settings.blade.php       # Settings page
│   └── profiles/
│       └── show.blade.php           # Public profile view
│
├── js/
│   ├── landing/
│   │   └── LandingPage.vue          # Landing page components
│   ├── app/
│   │   ├── App.vue                  # App shell component
│   │   ├── Dashboard.vue            # Dashboard components
│   │   ├── Habits.vue               # Habit tracking interface
│   │   ├── CheckIns.vue             # Check-ins interface
│   │   ├── Timeline.vue             # Timeline feed
│   │   ├── Stats.vue                # Stats visualization
│   │   └── components/
│   │       ├── QuickCapture.vue     # Quick capture input
│   │       ├── ContributionGraph.vue # GitHub-style graph
│   │       └── HabitCheckbox.vue    # Habit checkbox component
│   └── profiles/
│       └── PublicProfile.vue        # Public profile components
```

### NAVIGATION STRUCTURE

**Landing Page Navigation (Not Logged In):**
```
+----------------------------------------------------------+
|  [Loggd Logo]                         [Login] [Sign Up]  |
+----------------------------------------------------------+
```

**App Navigation (Logged In):**
```
+----------------------------------------------------------+
|  [Loggd Logo] Dashboard Habits Check-ins Timeline Stats  |
|                                              [@username ▼]|
|                                               ├─ Settings |
|                                               ├─ My Profile|
|                                               └─ Logout   |
+----------------------------------------------------------+
```

**Mobile Navigation (Bottom Bar):**
```
+---------------------------+
|  🏠    ✓    ✏️   📊   👤  |
| Home Habits Write Stats Me|
+---------------------------+
```

### LANDING PAGE (Simple MVP Version)

**Content Structure:**
- Hero section with tagline: "Log your life. Watch yourself grow."
- Screenshot/demo of contribution graph
- 3-4 key features with icons:
  - Visual progress tracking
  - Flexible habit tracking
  - Video journaling
  - Public profiles (optional)
- Simple sign-up form or CTA button
- Footer with links

**Design:** Minimal black/white/gray aesthetic matching app design

**Goal:** Get users to understand the value proposition in 10 seconds and click "Sign Up"

**Build Time:** 2-3 hours (don't overcomplicate)

### USER PROFILE URLS

**Each user gets a public profile URL:**
- Format: `loggd.life/@username`
- Example: `loggd.life/@eusebiu`

**Public Profile Features:**
- Contribution graph (always visible if profile is public)
- Bio/description
- Public entries only (user controls visibility per entry)
- Public videos (if user makes them public)
- Basic stats (total entries, streak - if user enables)

**Privacy Controls:**
- Profile public/private toggle (Settings → Profile)
- Per-entry public/private toggle
- Default: All entries private
- User must explicitly enable public profile and make entries public

**Future Enhancement (Phase 2):**
- Custom subdomains: `username.loggd.life` (requires wildcard DNS setup)

---

## MVP FEATURES (Phase 1 - Launch in 2-3 months)

### 1. USER AUTHENTICATION
- Email/password signup and login
- OAuth (Google, GitHub optional)
- Password reset flow
- Email verification

### 2. DAILY LOGGING SYSTEM

**Quick Capture Bar** (Primary Interface)
- Sticky input bar at top of dashboard
- Type anything: task completed, thought, note, goal update
- Auto-categorize with simple tags or manual selection
- Keyboard shortcut: Cmd/Ctrl + K
- Supports: Text, images (upload/paste), quick video upload

**Log Entry Types:**
- **Task completed** - "Shipped feature X"
- **Habit tracked** - "Went to gym"
- **Goal progress** - "Made progress on Q1 goal"
- **Journal entry** - Freeform thought/reflection
- **Video log** - Upload video with tags

**Entry Metadata:**
- Timestamp (defaults to now, can edit)
- Category/tags (custom, user-defined)
- Optional notes/description
- Public/private toggle

### 3. HABIT TRACKING SYSTEM

**Core Concept:**
Simple checkbox-based habit tracking with date columns. Users can quickly mark habits as done for any date.

**Habit List View (Main Interface):**
```
+--------------------------------------------------------+
| Habits                                [+ New Habit]    |
+--------------------------------------------------------+
| Habit Name        | Mon | Tue | Wed | Thu | Fri | ... |
+--------------------------------------------------------+
| 🏋️ Gym            | ✓   | ✓   | -   | ✓   | -   | ... |
| 📝 Write 500 words | ✓   | -   | ✓   | ✓   | ✓   | ... |
| 💻 Code side proj  | -   | ✓   | ✓   | -   | ✓   | ... |
+--------------------------------------------------------+
```

**Features:**

**1. Create/Edit Habits:**
- Click "+ New Habit" button
- Modal appears:
  - Habit name (required)
  - Icon/emoji (optional)
  - Tracking frequency: Daily, Weekdays, Weekends, Custom days
  - Notes/description field (optional detail column)
  - Color tag (optional)
  - Start date (defaults to today)
- Edit: Click habit name to edit
- Archive: Mark habit as "Not currently tracking" (hides from active view but keeps history)

**2. Checkbox System:**
- Click any checkbox to mark habit as done for that day
- States: 
  - Empty (not done)
  - ✓ (done)
  - Optional: - (skipped/not applicable)
- Can check/uncheck past dates (edit history)
- Can check future dates (pre-log)
- Visual: Simple checkmark, no animation needed

**3. Date Navigation:**
- Default view: Current week (7 columns)
- Arrow buttons to navigate previous/next weeks
- Quick jump to "Today"
- Option to view: Week, Month (calendar style), Year (compact)

**4. Detail Column (Optional):**
- Clicking a checked habit opens detail panel/modal
- Shows: Date, Time marked, Optional note
- User can add quick note: "Great workout!" or "Felt tired but pushed through"
- Notes visible on hover or in expanded view

**5. Habit Status:**
- **Active**: Currently tracking (shows in main list)
- **Archived**: No longer tracking (hidden by default, can view in "Archived" tab)
- Can reactivate archived habits

**6. Stats Per Habit:**
- Current streak (consecutive days completed)
- Longest streak
- Total completions
- Completion rate (% of days since start date)
- Shown when hovering over habit name or in detail view

**7. Mobile View:**
- Horizontal scroll for date columns
- Sticky habit name column
- Larger touch targets for checkboxes
- Swipe to see more dates

**Database Schema for Habits:**

**habits table:**
- id, user_id
- name (string)
- emoji/icon (string, nullable)
- frequency (enum: daily, weekdays, weekends, custom)
- custom_days (JSON, nullable - for custom frequency like Mon/Wed/Fri)
- description (text, nullable)
- color (hex, nullable)
- start_date (date)
- status (enum: active, archived)
- created_at, updated_at

**habit_checks table:**
- id, habit_id, user_id
- date (date)
- checked (boolean)
- note (text, nullable)
- checked_at (timestamp)
- created_at, updated_at
- UNIQUE constraint on (habit_id, date)

**API Endpoints for Habits:**
- GET /api/habits (returns active habits)
- GET /api/habits?status=archived (returns archived)
- POST /api/habits (create new habit)
- PUT /api/habits/{id} (update habit)
- PATCH /api/habits/{id}/archive (archive habit)
- PATCH /api/habits/{id}/reactivate (reactivate archived habit)
- GET /api/habits/{id}/checks?from=2025-10-01&to=2025-10-31 (get checks for date range)
- POST /api/habits/{id}/check (toggle check for specific date)
- PUT /api/habits/{id}/checks/{date}/note (add/update note for specific check)
- GET /api/habits/{id}/stats (get streak and completion stats)

### 4. VISUAL PROGRESS SYSTEM

**GitHub-Style Contribution Graph**
- Year view: 365-day grid (7 rows x 52 columns)
- Color intensity based on activity:
  - No activity: light gray
  - 1-2 activities: light green
  - 3-5 activities: medium green
  - 6+ activities: dark green
- Hover tooltip: "3 activities: Coded 2hrs, Gym, Wrote 500 words"
- Click square → shows day detail modal with all entries
- Always visible at top of dashboard

**Stats Dashboard** (Right sidebar or separate page)
- Current streak (consecutive days with activity)
- Longest streak
- Total activities this year
- Total activities by category
- Weekly/monthly activity trends
- "Best day" metrics (most productive day)

### 5. FLEXIBLE CHECK-IN SYSTEM

**Check-In Types:**
- Daily Check-in
- Weekly Review
- Monthly Reflection  
- Quarterly Goals
- Yearly Overview
- Custom (user-defined)

**Easy Writing Interface:**

**Daily Check-in Template (Default):**
```
Daily Check-in for [Date]

What did I accomplish today?
[Text area]

How do I feel?
[Text area]

What's the priority for tomorrow?
[Text area]

[Save Check-in Button]
```

**Weekly Review Template (Default):**
```
Weekly Review for [Week of Date]

This week's highlights:
[Text area]

What went well?
[Text area]

What didn't go well?
[Text area]

Key learnings:
[Text area]

Priorities for next week:
[Text area]

[Save Review Button]
```

**Features:**
- Auto-saves drafts every 30 seconds
- Can customize templates (add/remove/edit prompts)
- Can create custom check-in types (e.g., "Project Review", "Health Check")
- Templates saved per user
- Optional: Pull in relevant data (habit completion rate for week, activity count)
- Markdown support for formatting

**Quick Access:**
- Button in navigation: "Daily Check-in"
- Shows today's check-in if exists, otherwise shows empty template
- Calendar picker to view/edit past check-ins
- Can mark check-in as public/private

**Database Schema:**

**check_ins table:**
- id, user_id
- type (enum: daily, weekly, monthly, quarterly, yearly, custom)
- date (date - the date/week/month it represents)
- content (JSON - flexible structure for different templates)
- is_public (boolean)
- created_at, updated_at

**check_in_templates table:**
- id, user_id
- type (matches check_in types)
- name (string - for custom types)
- prompts (JSON array - list of question prompts)
- is_default (boolean)
- created_at, updated_at

**API Endpoints:**
- GET /api/check-ins?type=daily&date=2025-10-15
- POST /api/check-ins (create/update check-in)
- GET /api/check-ins/{id}
- DELETE /api/check-ins/{id}
- GET /api/check-in-templates?type=weekly
- POST /api/check-in-templates (create custom template)
- PUT /api/check-in-templates/{id}

### 6. VIDEO JOURNALING

**Video Upload:**
- Drag-and-drop or click to upload
- Max length: 5 minutes (MVP), 2 minutes ideal
- Supported formats: MP4, MOV, WebM
- Video compression on upload

**Video Organization:**
- Tag-based system: #buildinpublic, #mood, #progress, #reflection
- Create custom tags
- Filter videos by tag
- Timeline view of all videos

**Video Memories:**
- "On this day" feature: Shows videos from 1 week, 1 month, 1 year ago
- Displayed on dashboard as notification
- Can disable if user doesn't want

**Video Storage:**
- Store in AWS S3 or DigitalOcean Spaces
- Generate thumbnails automatically
- Stream videos (don't force download)

### 7. TIMELINE VIEW

**Main Timeline Interface:**
- Chronological feed of all entries (newest first)
- Filter by:
  - Date range
  - Category/tags
  - Entry type (task, habit, video, journal)
  - Public/private
- Search functionality
- Infinite scroll or pagination

**Entry Display:**
- Show timestamp, category badge, content preview
- Expand to see full content
- Edit/delete options
- Quick toggle public/private

### 8. PUBLIC PROFILE PAGES

**Profile URL Structure:**
- username.loggd.app or loggd.app/username
- Custom subdomain for paid users

**Public Profile Includes:**
- User's contribution graph (always public if profile is public)
- Bio/description (optional)
- Public entries only (user controls what's public)
- Public videos with playback
- Stats: Total public activities, current streak

**Privacy Controls:**
- Toggle entire profile public/private
- Toggle individual entries public/private
- Default new entries to private
- Warning when making entry public

### 9. CATEGORIES & TAGS

**System:**
- User creates custom categories/tags
- Assign colors to categories
- Used for:
  - Organizing logs
  - Filtering timeline
  - Visual breakdown in stats
  - Video organization

**Suggested Default Categories:**
- 💻 Work/Code
- 📝 Writing
- 🏋️ Fitness
- 📚 Learning
- 🎯 Goals
- 💭 Reflection
- 🎥 Build in Public

Users can modify, delete, or create entirely new ones.

### 10. SETTINGS & PREFERENCES

**Account Settings:**
- Change email/password
- Delete account
- Export data (JSON format)

**Display Preferences:**
- Dark/light mode
- Date format
- Time zone
- Default privacy for new entries

**Notification Settings:**
- Daily reminder to log (time configurable)
- Weekly review reminder
- Monthly reflection reminder
- Email notifications on/off

---

## TECHNICAL SPECIFICATIONS

### TECH STACK

**Backend:**
- PHP 8.2+ with Laravel 11
- MySQL or PostgreSQL database
- RESTful API architecture
- Queue system for video processing (Laravel Queues)

**Frontend:**
- Vue.js 3 (Composition API)
- Vite for build tooling
- Tailwind CSS for styling
- Inertia.js for seamless SPA experience (optional but recommended)

**Video Storage:**
- AWS S3 or DigitalOcean Spaces
- FFmpeg for video processing/compression
- Cloudflare or CDN for video streaming

**Hosting:**
- DigitalOcean or AWS
- Redis for caching and queues
- Nginx web server

### DATABASE SCHEMA (Core Tables)

**users**
- id, name, email, password
- username (unique)
- profile_public (boolean)
- bio (text, nullable)
- timezone
- created_at, updated_at

**entries**
- id, user_id
- type (enum: task, habit, journal, video, goal_update)
- content (text)
- date (date) - can be different from created_at
- is_public (boolean, default false)
- created_at, updated_at

**entry_tags**
- id, entry_id
- tag_name
- tag_color

**categories**
- id, user_id
- name
- color (hex)
- icon (optional)

**videos**
- id, user_id, entry_id
- file_path (S3 URL)
- thumbnail_path
- duration (seconds)
- file_size
- processing_status (pending, completed, failed)
- created_at

**goals**
- id, user_id
- title
- description
- timeframe (enum: yearly, quarterly, monthly)
- target_date
- progress_percentage (0-100)
- status (active, completed, abandoned)
- created_at, updated_at

**reviews**
- id, user_id
- type (enum: weekly, monthly, quarterly, yearly, custom)
- date
- content (JSON - flexible structure for custom prompts/answers)
- created_at, updated_at

### API ENDPOINTS (Core)

**Authentication:**
- POST /api/register
- POST /api/login
- POST /api/logout
- POST /api/password/reset

**Entries:**
- GET /api/entries (with filters: date_from, date_to, type, tags, is_public)
- POST /api/entries
- GET /api/entries/{id}
- PUT /api/entries/{id}
- DELETE /api/entries/{id}
- PATCH /api/entries/{id}/toggle-public

**Stats:**
- GET /api/stats/contribution-graph?year=2025
- GET /api/stats/streaks
- GET /api/stats/summary (totals, breakdown by category)

**Videos:**
- POST /api/videos/upload (with entry_id)
- GET /api/videos/{id}/stream
- GET /api/videos?tags=buildinpublic

**Goals:**
- GET /api/goals
- POST /api/goals
- PUT /api/goals/{id}
- PATCH /api/goals/{id}/progress

**Reviews:**
- GET /api/reviews?type=weekly
- POST /api/reviews
- GET /api/reviews/{id}
- PUT /api/reviews/{id}

**Public Profile:**
- GET /api/public/{username}
- GET /api/public/{username}/entries

---

## USER INTERFACE PRINCIPLES

### DESIGN PHILOSOPHY
- **Minimal and clean** - Like Notion's simplicity
- **Visual first** - Graphs and charts prominent
- **Fast capture** - Logging should take 10 seconds max
- **No overwhelming features** - Show what's needed, hide complexity
- **Mobile-responsive** - Works on phones (most logging happens on mobile)

### KEY UI COMPONENTS

**Dashboard Layout:**
```
+----------------------------------------------------------+
|  Loggd    [Habits] [Check-ins] [Timeline]  [@user] [+]  |
+----------------------------------------------------------+
|                                                          |
|  [Contribution Graph - GitHub Style]                     |
|  ████████████████████████████████████                    |
|  Current streak: 15 days 🔥                              |
|                                                          |
+----------------------------------------------------------+
|                                                          |
|  Quick Capture                                           |
|  ┌────────────────────────────────────────────────┐     |
|  │ What did you do today?            📝 🖼️ 🎥    │     |
|  └────────────────────────────────────────────────┘     |
|                                                          |
+----------------------------------------------------------+
|                              |                           |
|  [Timeline - Social Feed]    |  [Quick Stats]            |
|                              |                           |
|  ┌──────────────────────┐   |  🔥 15 day streak         |
|  │ @user · 2h ago       │   |  📊 245 entries           |
|  │ Shipped new feature  │   |  ✓ 12/15 habits this week |
|  │ #work #buildinpublic │   |                           |
|  └──────────────────────┘   |  [View All Stats →]       |
|                              |                           |
|  ┌──────────────────────┐   |                           |
|  │ @user · 5h ago       │   |                           |
|  │ [Video thumbnail]    │   |                           |
|  │ Daily check-in       │   |                           |
|  └──────────────────────┘   |                           |
|                              |                           |
+----------------------------------------------------------+
```

**Mobile Layout (Bottom Navigation):**
```
+---------------------------+
|  Loggd            [@user] |
+---------------------------+
|                           |
|  [Contribution Graph]     |
|  (horizontal scroll)      |
|                           |
+---------------------------+
|                           |
|  [Quick Capture]          |
|                           |
+---------------------------+
|                           |
|  [Timeline Feed]          |
|  (infinite scroll)        |
|                           |
+---------------------------+
|  🏠  ✓  ✏️  📊  👤        |
| Home Habits Write Stats Me|
+---------------------------+
```

---

## USER FLOWS

### ONBOARDING FLOW
1. Landing page with demo (show contribution graph in action)
2. Sign up (email + password)
3. Email verification
4. Welcome screen: "Let's set up your tracking system"
5. Pick categories (show suggested, allow custom)
6. Set daily reminder time
7. Quick tutorial: "Add your first log entry"
8. Dashboard with empty state: "Start logging your first day"

### DAILY LOGGING FLOW
1. User opens dashboard
2. Sees contribution graph + today highlighted
3. Sees quick capture bar
4. Types entry: "Shipped new feature"
5. Selects category: Work
6. Clicks add or hits Enter
7. Entry appears in timeline
8. Today's square updates in contribution graph
9. Stats update (streak continues, activity count increases)

### WEEKLY REVIEW FLOW
1. Sunday evening, notification: "Time for weekly review"
2. User clicks notification or goes to Reviews section
3. Sees auto-generated summary: "This week you logged 12 work activities, 5 fitness sessions"
4. Prompted with review questions
5. Answers questions (pre-filled with context where possible)
6. Saves review
7. Can see past reviews in timeline

### VIDEO JOURNALING FLOW
1. User wants to record build-in-public update
2. Clicks video icon in quick capture bar
3. Records or uploads video (max 5 min)
4. Adds tags: #buildinpublic
5. Toggles public/private
6. Uploads (processing indicator)
7. Video appears in timeline with thumbnail
8. Can filter timeline to see all #buildinpublic videos

### PUBLIC PROFILE SHARING FLOW
1. User wants to share their progress
2. Goes to Settings → Profile
3. Toggles "Make profile public"
4. Selects which entries to make public
5. Customizes bio
6. Gets shareable link: username.loggd.app
7. Shares on Twitter/LinkedIn
8. Others can view contribution graph + public entries

---

## PHASE 2 FEATURES (Post-MVP)

### AI ASSISTANT
- Pattern detection: "You always lose motivation on Fridays"
- Smart suggestions: "Based on your Q1 goals, you're 60% behind schedule"
- Automated insights in weekly reviews
- Predictive reminders: "Last month you stopped logging around week 3"
- Integration with OpenAI API

### ENHANCED SOCIAL FEATURES
- Follow other users
- Like/comment on public entries
- Accountability groups (5-10 people)
- Shared challenges
- Leaderboards (opt-in)

### INTEGRATIONS
- GitHub (auto-pull commits as work entries)
- Strava (auto-log workouts)
- Notion (export reviews to Notion)
- Google Calendar (sync appointments as entries)
- Zapier/Make.com (general automation)

### ADVANCED VIDEO FEATURES
- AI transcription of videos
- Search within video transcripts
- Auto-generated video compilations (year in review)
- Video playlists by tag
- Video editing tools (trim, add text)

### MOBILE APPS
- Native iOS app
- Native Android app
- Push notifications
- Offline mode with sync

### TEAMS/PREMIUM FEATURES
- Team workspaces
- Shared contribution graphs
- Team goals and reviews
- Admin dashboard for teams
- White-label options
- Custom domains for profiles

---

## TECHNICAL CONSIDERATIONS

### PERFORMANCE
- Cache contribution graph data (regenerate nightly)
- Lazy load timeline entries (pagination or infinite scroll)
- Compress images on upload
- Use CDN for static assets and videos
- Index database properly (user_id, date, is_public)

### SECURITY
- HTTPS everywhere
- Rate limiting on API endpoints
- Input validation and sanitization
- SQL injection protection (use Eloquent ORM)
- XSS protection
- CSRF tokens
- File upload validation (size, type, malware scanning)
- Secure video URLs (signed URLs for S3)

### VIDEO STORAGE COSTS
- Estimate: €0.023/GB storage + €0.09/GB bandwidth on S3
- At 1000 users with 50MB video/month = 50GB = ~€5/month storage
- Compression: Target 10MB for 2-min video
- Consider video length limits in free tier

### SCALABILITY
- Use Laravel Horizon for queue management
- Background jobs for:
  - Video processing
  - Email sending
  - Stats recalculation
  - Export generation
- Database indexing for common queries
- Consider read replicas if traffic grows

---

## DEVELOPMENT PHASES

### Phase 1: MVP Foundation (Weeks 1-4)
- Setup: Laravel project, database, basic configuration
- Build: Simple landing page (2-3 hours)
- Build: Authentication system (Breeze or Jetstream)
- Build: Route structure with smart redirects
- Build: Entry logging system and basic timeline
- Build: Habit tracking system with checkbox interface
- Build: Contribution graph (static data first, then real calculations)
- Deploy: Staging environment

### Phase 2: Core Features (Weeks 5-8)
- Build: Categories and tags system
- Build: Stats dashboard with real calculations
- Build: Check-in system (daily, weekly templates)
- Build: Video upload and basic storage
- Test: User testing with 5-10 beta users

### Phase 3: Polish & Launch (Weeks 9-12)
- Build: Video timeline and memories feature
- Build: Public profiles
- Build: Mobile-responsive design
- Deploy: Production launch
- Documentation and onboarding flow

### Phase 4: Post-Launch (Month 4+)
- Iterate based on user feedback
- Fix bugs and performance issues
- Build Phase 2 features (AI, integrations)
- Marketing and growth

---

## DESIGN ASSETS NEEDED

- Logo (simple, memorable)
- Favicon
- Contribution graph color palette
- Category icons (work, fitness, learning, etc.)
- Empty states (no entries yet, no videos yet)
- Loading states (video processing, uploading)
- Error states
- Email templates (welcome, verification, reminders)
- Social share cards (Open Graph images)
- Landing page mockups

---

## OPEN QUESTIONS TO RESOLVE DURING BUILD

1. Should video be optional or core to the experience?
2. Do we auto-create weekly/monthly reviews or wait for user action?
3. How do we handle timezone differences for "daily" tracking?
4. Should streaks continue if user logs backdated entries?
5. What's the minimum viable public profile (just graph or more)?
6. Do we need native apps for MVP or is web enough?
7. Should we support team accounts from day 1 or wait?
8. What's our content moderation policy for public profiles?

---

## GETTING STARTED

### Development Setup
```bash
# Clone repository
git clone [repo-url]

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development servers
php artisan serve
npm run dev
```

### First Steps for AI Agent
1. Setup Laravel project with authentication (Breeze or Jetstream)
2. Create simple landing page at root route with sign-up CTA
3. Implement smart redirect logic (logged in → /app, logged out → landing)
4. Setup route structure (/app/* for authenticated routes, /@username for profiles)
5. Create database migrations for core tables (users, entries, habits, habit_checks, check_ins)
6. Build Entry model and API endpoints for logging
7. Build Habit model and checkbox system with API endpoints
8. Create Vue.js components:
   - Landing page component (simple hero + features)
   - App dashboard with quick capture component
   - Habit tracking table with date columns
   - Timeline feed (social media style)
   - Contribution graph visualization
9. Style everything with Tailwind CSS using black/white/gray minimal design
10. Test complete user journey: landing → signup → dashboard → log entry → check habit

---

## ADDITIONAL NOTES

**Development Principles:**
- Build for yourself first (dogfood the product)
- Ship fast, iterate based on real usage
- Don't over-engineer (MVP should work, not be perfect)
- Test with real users early (beta at week 6)
- Mobile experience is critical (test on phone daily)

**Common Pitfalls to Avoid:**
- Over-complicated onboarding
- Too many features at launch
- Slow video upload/processing
- Confusing privacy settings
- Rigid tracking structures (defeats purpose of flexibility)
- Building for everyone instead of specific user
- Neglecting mobile experience

---

This README is a living document. Update as decisions are made and features evolve.