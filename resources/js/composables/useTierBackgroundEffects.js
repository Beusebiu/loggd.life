import { onMounted, onUnmounted } from 'vue';

/**
 * Advanced tier background effects using Canvas and modern techniques
 * Each tier gets progressively more impressive visual effects
 */
export function useTierBackgroundEffects(tier, containerRef) {
  let animationId = null;
  let canvas = null;
  let ctx = null;
  let particles = [];
  let blobs = [];
  let time = 0;

  // Particle class for floating particles
  class Particle {
    constructor(canvas, color) {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.size = Math.random() * 3 + 1;
      this.speedX = Math.random() * 0.5 - 0.25;
      this.speedY = Math.random() * 0.5 - 0.25;
      this.color = color;
      this.opacity = Math.random() * 0.5 + 0.2;
    }

    update(canvas) {
      this.x += this.speedX;
      this.y += this.speedY;

      if (this.x > canvas.width) this.x = 0;
      if (this.x < 0) this.x = canvas.width;
      if (this.y > canvas.height) this.y = 0;
      if (this.y < 0) this.y = canvas.height;
    }

    draw(ctx) {
      ctx.fillStyle = this.color;
      ctx.globalAlpha = this.opacity;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
      ctx.globalAlpha = 1;
    }
  }

  // Morphing blob class
  class MorphingBlob {
    constructor(canvas, x, y, radius, color) {
      this.x = x;
      this.y = y;
      this.baseRadius = radius;
      this.color = color;
      this.points = 8;
      this.angleOffset = 0;
      this.radiusVariation = radius * 0.3;
    }

    update(time) {
      this.angleOffset = time * 0.001;
    }

    draw(ctx, time) {
      ctx.beginPath();

      for (let i = 0; i <= this.points; i++) {
        const angle = (i / this.points) * Math.PI * 2 + this.angleOffset;
        const radiusVariation = Math.sin(time * 0.002 + i) * this.radiusVariation;
        const radius = this.baseRadius + radiusVariation;

        const x = this.x + Math.cos(angle) * radius;
        const y = this.y + Math.sin(angle) * radius;

        if (i === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      }

      ctx.closePath();
      ctx.fillStyle = this.color;
      ctx.filter = 'blur(40px)';
      ctx.fill();
      ctx.filter = 'none';
    }
  }

  // Initialize canvas-based effects
  const initCanvasEffects = () => {
    if (!containerRef.value) return;

    // Create canvas element
    canvas = document.createElement('canvas');
    canvas.style.position = 'absolute';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '0';

    const rect = containerRef.value.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;

    containerRef.value.style.position = 'relative';
    containerRef.value.insertBefore(canvas, containerRef.value.firstChild);

    ctx = canvas.getContext('2d');

    // Initialize effects based on tier
    if (tier >= 6 && tier < 10) {
      // Tier 6-9: Floating particles
      const particleCount = tier >= 10 ? 50 : 30;
      const colors = tier >= 28
        ? ['rgba(59, 130, 246, 0.6)', 'rgba(139, 92, 246, 0.6)', 'rgba(6, 182, 212, 0.6)']
        : ['rgba(251, 191, 36, 0.6)', 'rgba(245, 158, 11, 0.6)'];

      for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle(canvas, colors[Math.floor(Math.random() * colors.length)]));
      }
    }

    if (tier >= 10 && tier < 28) {
      // Tier 10-27: Morphing blobs
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.2, canvas.height * 0.3, 80, 'rgba(251, 191, 36, 0.15)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.8, canvas.height * 0.7, 100, 'rgba(245, 158, 11, 0.15)'));
    }

    if (tier >= 28 && tier < 57) {
      // Tier 28-56: Multiple morphing blobs with energy
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.3, canvas.height * 0.4, 120, 'rgba(59, 130, 246, 0.2)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.7, canvas.height * 0.6, 140, 'rgba(139, 92, 246, 0.2)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.5, canvas.height * 0.5, 100, 'rgba(6, 182, 212, 0.15)'));
    }

    if (tier >= 57) {
      // Tier 57+: Cosmic effects with multiple blobs and particles
      const particleCount = 80;
      const colors = ['rgba(251, 191, 36, 0.7)', 'rgba(217, 70, 239, 0.7)', 'rgba(168, 85, 247, 0.7)', 'rgba(34, 211, 238, 0.7)'];

      for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle(canvas, colors[Math.floor(Math.random() * colors.length)]));
      }

      blobs.push(new MorphingBlob(canvas, canvas.width * 0.2, canvas.height * 0.3, 150, 'rgba(251, 191, 36, 0.15)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.8, canvas.height * 0.7, 180, 'rgba(217, 70, 239, 0.15)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.5, canvas.height * 0.2, 120, 'rgba(168, 85, 247, 0.15)'));
      blobs.push(new MorphingBlob(canvas, canvas.width * 0.6, canvas.height * 0.8, 140, 'rgba(34, 211, 238, 0.15)'));
    }

    animate();
  };

  // Animation loop
  const animate = () => {
    if (!ctx || !canvas) return;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    time++;

    // Draw and update blobs
    blobs.forEach(blob => {
      blob.update(time);
      blob.draw(ctx, time);
    });

    // Draw and update particles
    particles.forEach(particle => {
      particle.update(canvas);
      particle.draw(ctx);
    });

    animationId = requestAnimationFrame(animate);
  };

  onMounted(() => {
    if (tier >= 6) {
      setTimeout(initCanvasEffects, 100); // Delay to ensure container is mounted
    }
  });

  onUnmounted(() => {
    if (animationId) {
      cancelAnimationFrame(animationId);
    }
    if (canvas && canvas.parentNode) {
      canvas.parentNode.removeChild(canvas);
    }
    particles = [];
    blobs = [];
  });

  return {};
}
