import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['image', 'accent', 'content', 'label', 'title', 'lead'];
    static values = {
        parallaxSpeed: { type: Number, default: 0.15 },
        mouseSpeed: { type: Number, default: 30 }
    }

    connect() {
        // Simple event listeners for better compatibility
        this.scrollHandler = this.onScroll.bind(this);
        this.mouseHandler = this.onMouseMove.bind(this);

        window.addEventListener('scroll', this.scrollHandler, { passive: true });
        window.addEventListener('mousemove', this.mouseHandler, { passive: true });

        // Initial state for reveal animations
        this.prepareReveal();

        // Minor delay to ensure better animation start
        setTimeout(() => this.reveal(), 100);
    }

    disconnect() {
        window.removeEventListener('scroll', this.scrollHandler);
        window.removeEventListener('mousemove', this.mouseHandler);
    }

    // Parallax on Scroll
    onScroll() {
        const scrollY = window.scrollY;

        if (this.hasImageTarget) {
            // Very subtle parallax
            this.imageTarget.style.transform = `translate3d(0, ${scrollY * this.parallaxSpeedValue}px, 0)`;
        }

        if (this.hasAccentTarget) {
            this.accentTargets.forEach((accent, index) => {
                const speed = (index + 1) * 0.1;
                accent.style.transform = `translate3d(0, ${scrollY * speed}px, 0) rotate(${30 + scrollY * 0.02}deg)`;
            });
        }
    }

    // Mouse Tracking (Subtle movement)
    onMouseMove(event) {
        const mouseX = event.clientX;
        const mouseY = event.clientY;
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;

        const moveX = (mouseX - centerX) / centerX;
        const moveY = (mouseY - centerY) / centerY;

        if (this.hasAccentTarget) {
            this.accentTargets.forEach((accent, index) => {
                const factor = (index + 1) * this.mouseSpeedValue;
                // Add subtle offset to the accent shapes
                accent.style.translate = `${moveX * factor}px ${moveY * factor}px`;
            });
        }
    }

    prepareReveal() {
        const items = [this.labelTarget, this.titleTarget, this.leadTarget].filter(t => t);
        items.forEach(item => {
            // No need to set opacity/transform as they are already in HTML
            item.style.transition = 'opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1), transform 1.2s cubic-bezier(0.16, 1, 0.3, 1)';
        });
    }

    reveal() {
        const targets = [
            { target: this.labelTarget, delay: 0 },
            { target: this.titleTarget, delay: 150 },
            { target: this.leadTarget, delay: 300 }
        ];

        targets.forEach(({ target, delay }) => {
            if (target) {
                setTimeout(() => {
                    target.style.cssText += 'opacity: 1 !important; transform: translateY(0) !important;';
                }, delay);
            }
        });
    }
}
