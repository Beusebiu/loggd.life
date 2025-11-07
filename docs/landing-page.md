# LOGGD LANDING PAGE - BUILD SPEC

## WHAT TO BUILD

A simple one-page landing site at `loggd.life` that converts visitors into signed-up users.

**Goal:** User understands what Loggd does in 10 seconds and clicks "Sign Up"

---

## DESIGN

**Style:** Minimal black/white/gray with green accent for CTAs

**Colors:**
- Background: White (`#FFFFFF`)
- Text: Black (`#000000`) and gray (`#666666`)
- Accent: Green (`#10B981`) for buttons
- Footer: Dark gray (`#1F2937`)

**Typography:**
- Font: Inter or system fonts
- Headlines: 48px (mobile: 32px), semibold
- Body: 16px, regular

---

## PAGE STRUCTURE

```
┌─────────────────────────────────┐
│ Navigation                       │
│ [Loggd]      [Login] [Sign Up]  │
├─────────────────────────────────┤
│ Hero Section                     │
│                                  │
│ Log your life.                   │
│ Watch yourself grow.             │
│                                  │
│ A flexible system to track       │
│ your life...                     │
│                                  │
│ [Get Started Free →]             │
│                                  │
│ [Screenshot of contribution      │
│  graph with activity]            │
├─────────────────────────────────┤
│ Features (4 blocks, 2x2 grid)   │
│                                  │
│ 📊 Visual Proof                  │
│ 📝 Log Your Way                  │
│ 🎥 Watch Yourself Grow           │
│ 🌐 Share Journey                 │
├─────────────────────────────────┤
│ Final CTA                        │
│ Ready to start tracking?         │
│ [Get Started Free →]             │
├─────────────────────────────────┤
│ Footer                           │
│ Links + © 2025 Loggd            │
└─────────────────────────────────┘
```

---

## CONTENT

### Navigation
- Logo: "Loggd" (text, left side)
- Right side: "Login" link + "Sign Up" button (green)

### Hero Section

**Headline:**  
"Log your life.  
Watch yourself grow."

**Subheadline:**  
"A flexible system to track your life. Daily logs, weekly reviews, video journals, and visual progress. Finally, a tracking system that adapts to you."

**CTA Button:**  
"Get Started Free →" (green, large, links to `/register`)

**Image:**  
Screenshot of contribution graph (GitHub-style) filled with activity

### Features Section

**Section Headline:**  
"Everything you need to track your growth"

**4 Features (2x2 grid):**

1. **📊 Visual proof of progress**  
   GitHub-style contribution graph shows your entire journey. See patterns, track streaks, and watch your life fill up with activity over time.

2. **📝 Log your way**  
   Daily check-ins, weekly reviews, quick notes, or video journals. Flexible templates that adapt to how you actually work, not how you "should" work.

3. **🎥 Watch yourself grow**  
   Record video journals and look back months later. See how far you've come. "One year ago today" memories show your actual transformation.

4. **🌐 Share your journey (optional)**  
   Public profile to build in public. Share your contribution graph, progress updates, and inspire others while staying accountable.

### Final CTA Section

**Headline:**  
"Ready to start tracking your life?"

**CTA Button:**  
"Get Started Free →" (green, links to `/register`)

**Background:** Black for contrast

### Footer

**Simple version (MVP):**
- "Loggd" text
- "© 2025 Loggd. All rights reserved."
- Links: Login | Sign Up | Privacy | Terms

---

## IMPLEMENTATION

### File Structure
```
resources/views/landing/
└── index.blade.php
```

### HTML Skeleton
```html
<!DOCTYPE html>
<html>
<head>
    <title>Loggd - Log your life. Watch yourself grow.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your personal growth OS. Track your life with daily logs, weekly reviews, video journals, and visual progress. Flexible. Beautiful. Built for how you actually work.">
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- Navigation -->
    <nav class="border-b">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
            <a href="/" class="text-2xl font-semibold">Loggd</a>
            <div>
                <a href="/login" class="text-gray-600 px-4">Login</a>
                <a href="/register" class="bg-green-500 text-white px-6 py-2 rounded-lg">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="py-20 text-center">
        <h1 class="text-6xl font-semibold mb-6">
            Log your life.<br>Watch yourself grow.
        </h1>
        <p class="text-2xl text-gray-600 mb-8">
            A flexible system to track your life. Daily logs, weekly reviews, video journals, and visual progress.
        </p>
        <a href="/register" class="inline-block bg-green-500 text-white px-8 py-4 rounded-lg text-lg">
            Get Started Free →
        </a>
        <div class="mt-16">
            <img src="/images/hero.png" alt="Contribution graph" class="rounded-lg shadow-2xl mx-auto">
        </div>
    </section>

    <!-- Features -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-semibold text-center mb-16">
                Everything you need to track your growth
            </h2>
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Feature 1 -->
                <div>
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-2xl font-semibold mb-3">Visual proof of progress</h3>
                    <p class="text-gray-600 text-lg">
                        GitHub-style contribution graph shows your entire journey. See patterns and watch your life fill up.
                    </p>
                </div>
                <!-- Repeat for other 3 features with updated copy -->
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="bg-black text-white py-20 text-center">
        <h2 class="text-4xl font-semibold mb-8">Ready to start tracking your life?</h2>
        <a href="/register" class="inline-block bg-green-500 px-8 py-4 rounded-lg text-lg">
            Get Started Free →
        </a>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 text-center">
        <p>© 2025 Loggd. All rights reserved.</p>
    </footer>
</body>
</html>
```

### CSS (Tailwind)
Use Tailwind classes. Main utility classes needed:
- Container: `max-w-7xl mx-auto px-4`
- Spacing: `py-20`, `mb-8`, `gap-12`
- Typography: `text-2xl`, `font-semibold`
- Colors: `bg-green-500`, `text-gray-600`
- Rounding: `rounded-lg`

### Responsive
- Desktop: 2-column grid for features
- Mobile: Stack everything vertically
- Breakpoint: `md:` for tablet/desktop (768px+)

---

## ASSETS NEEDED

**Required:**
- `hero.png` - Screenshot of contribution graph (1200x800px)
- `favicon.ico` - Favicon

**Optional:**
- Feature screenshots (can skip for MVP, just use emoji)

---

## ROUTES (Laravel)

```php
// In routes/web.php

// Landing page with smart redirect
Route::get('/', function () {
    return Auth::check() 
        ? redirect('/app') 
        : view('landing.index');
})->name('home');
```

---

## META TAGS

Add to `<head>`:
```html
<title>Loggd - Log your life. Watch yourself grow.</title>
<meta name="description" content="Your personal growth OS. Track your life with daily logs, weekly reviews, video journals, and visual progress. Flexible. Beautiful. Built for how you actually work.">

<!-- Social sharing -->
<meta property="og:title" content="Loggd - Your Personal Growth OS">
<meta property="og:description" content="A flexible system to track your life. Daily logs, weekly reviews, video journals, and visual progress.">
<meta property="og:image" content="https://loggd.life/images/og-image.png">
```

---

## BUILD STEPS

1. Create `resources/views/landing/index.blade.php`
2. Add Tailwind CSS styling
3. Create hero screenshot (contribution graph image)
4. Add meta tags for SEO
5. Test on mobile and desktop
6. Deploy

**Build time: 2-3 hours**