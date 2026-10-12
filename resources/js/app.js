import './bootstrap';

const root = document.documentElement;
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const menuButton = document.querySelector('#menu-toggle');
const menu = document.querySelector('#primary-menu');

function closeMenu() {
    menuButton?.setAttribute('aria-expanded', 'false');
    menu?.classList.add('hidden');
}

menuButton?.addEventListener('click', () => {
    const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
    menuButton.setAttribute('aria-expanded', String(!isOpen));
    menu?.classList.toggle('hidden', isOpen);
});

menu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));

const themeButton = document.querySelector('#theme-toggle');

function updateThemeLabel() {
    const isDark = root.classList.contains('dark');
    themeButton?.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} theme`);
    if (themeButton) themeButton.textContent = isDark ? 'Light' : 'Dark';
}

themeButton?.addEventListener('click', () => {
    const isDark = root.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeLabel();
    window.dispatchEvent(new CustomEvent('portfolio:theme-change'));
});

updateThemeLabel();

const typewriter = document.querySelector('[data-typewriter]');

if (typewriter) {
    const phrase = typewriter.dataset.typewriter?.trim() ?? '';
    if (prefersReducedMotion.matches || !phrase) {
        typewriter.textContent = phrase;
    } else {
        let index = 0;
        const typeNextCharacter = () => {
            index += 1;
            typewriter.textContent = phrase.slice(0, index);
            if (index < phrase.length) window.setTimeout(typeNextCharacter, 70);
        };
        window.setTimeout(typeNextCharacter, 350);
    }
}

const revealItems = document.querySelectorAll('.reveal');
if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
} else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12 });
    revealItems.forEach((item) => revealObserver.observe(item));
}

document.querySelectorAll('[data-tilt-card]').forEach((card) => {
    card.addEventListener('pointermove', (event) => {
        if (prefersReducedMotion.matches || event.pointerType === 'touch') return;
        const bounds = card.getBoundingClientRect();
        const x = (event.clientX - bounds.left) / bounds.width;
        const y = (event.clientY - bounds.top) / bounds.height;
        card.style.transform = `rotateY(${(x - 0.5) * 12}deg) rotateX(${(0.5 - y) * 12}deg)`;
        card.style.setProperty('--pointer-x', `${x * 100}%`);
        card.style.setProperty('--pointer-y', `${y * 100}%`);
    });
    card.addEventListener('pointerleave', () => { card.style.transform = ''; });
});

const canvas = document.querySelector('#ambient-scene');
async function initializeAmbientScene(canvasElement) {
    try {
        const THREE = await import('three');
        const renderer = new THREE.WebGLRenderer({ canvas: canvasElement, alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, 1, 0.1, 100);
        camera.position.z = 7;

        const knotMaterial = new THREE.MeshBasicMaterial({ color: 0x7c5cff, wireframe: true, transparent: true, opacity: 0.55 });
        const knot = new THREE.Mesh(new THREE.TorusKnotGeometry(1.6, 0.45, 140, 16), knotMaterial);
        const shell = new THREE.Mesh(new THREE.IcosahedronGeometry(3.2, 1), new THREE.MeshBasicMaterial({ color: 0x00e5ff, wireframe: true, transparent: true, opacity: 0.12 }));
        const group = new THREE.Group();
        group.add(knot, shell);
        scene.add(group);

        const positions = new Float32Array(650 * 3);
        for (let index = 0; index < positions.length; index += 1) positions[index] = (Math.random() - 0.5) * 22;
        const particleGeometry = new THREE.BufferGeometry();
        particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const particleMaterial = new THREE.PointsMaterial({ color: 0xff3d9a, size: 0.04, transparent: true, opacity: 0.8 });
        const particles = new THREE.Points(particleGeometry, particleMaterial);
        scene.add(particles);

        let pointerX = 0;
        let pointerY = 0;
        let scrollProgress = 0;
        const syncColors = () => {
            const light = !root.classList.contains('dark');
            particleMaterial.color.set(light ? 0x7c5cff : 0xff3d9a);
            knotMaterial.color.set(light ? 0x5b3df5 : 0x7c5cff);
        };
        const resize = () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            group.position.x = window.innerWidth < 768 ? 0 : 2.4;
        };

        window.addEventListener('pointermove', (event) => {
            pointerX = event.clientX / window.innerWidth - 0.5;
            pointerY = event.clientY / window.innerHeight - 0.5;
        }, { passive: true });
        window.addEventListener('scroll', () => { scrollProgress = window.scrollY / window.innerHeight; }, { passive: true });
        window.addEventListener('resize', resize);
        window.addEventListener('portfolio:theme-change', syncColors);
        syncColors();
        resize();

        const animate = () => {
            knot.rotation.x += 0.004;
            knot.rotation.y += 0.006;
            shell.rotation.y -= 0.002;
            group.rotation.y += (pointerX * 0.8 - group.rotation.y) * 0.04;
            group.rotation.x += (pointerY * 0.8 - group.rotation.x) * 0.04;
            group.position.y = -scrollProgress * 1.2;
            group.scale.setScalar(Math.max(0.4, 1 - scrollProgress * 0.25));
            particles.rotation.y += 0.0004;
            particles.position.y = scrollProgress * 0.8;
            renderer.render(scene, camera);
            window.requestAnimationFrame(animate);
        };
        animate();
    } catch {
        canvasElement.hidden = true;
    }
}

if (canvas instanceof HTMLCanvasElement && !prefersReducedMotion.matches) initializeAmbientScene(canvas);
