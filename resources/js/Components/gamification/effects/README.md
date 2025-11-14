# Tier Background Effects - Modular System

Each tier has its own unique visual effect component. This modular structure makes it easy to:
- Add new effects
- Edit existing effects
- Remove effects
- Swap effects between tiers

## Tier Effect Mapping

| Tier | Level Range | Effect Name | Component |
|------|-------------|-------------|-----------|
| 1    | 1-2         | None | - |
| 2    | 3-5         | Floating Dots | FloatingDots.vue |
| 3    | 6-9         | Snow Fall | SnowFall.vue |
| 4    | 10-14       | Fireflies | Fireflies.vue |
| 5    | 15-20       | Confetti | Confetti.vue |
| 6    | 21-27       | Golden Sparkles | GoldenSparkles.vue |
| 7    | 28-35       | Energy Particles | EnergyParticles.vue |
| 8    | 36-45       | Aurora Lights | AuroraLights.vue |
| 9    | 46-56       | Shooting Stars | ShootingStars.vue |
| 10   | 57-69       | Cosmic Storm | CosmicStorm.vue |
| 11   | 70-84       | Nebula | Nebula.vue |
| 12   | 85-100      | Reality Warp | RealityWarp.vue |

## How to Add/Edit/Remove Effects

### Add a new effect:
1. Create a new Vue component in this directory
2. Import it in TierBackgroundEffects.vue
3. Add it to the tier mapping

### Edit an effect:
1. Open the component file for that tier
2. Modify the animations/visuals
3. Save - hot reload will update

### Remove an effect:
1. Remove the import from TierBackgroundEffects.vue
2. Remove from tier mapping
3. Optionally delete the component file

### Swap effects between tiers:
1. Just change the tier mapping in TierBackgroundEffects.vue
