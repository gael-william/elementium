/* ==========================================================================
   Élémentium Studio — scène 3D du hero
   Une sphère de particules partagée en trois éléments comme le logo :
   Feu (haut gauche), Eau (droite), Terre (bas). Elle respire, suit la souris,
   et se disperse quand on fait défiler la page.
   ========================================================================== */
import * as THREE from 'three';

const canvas = document.querySelector('.hero__canvas');
const hero = document.querySelector('.hero');
const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

function supportsWebGL() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
    } catch (e) {
        return false;
    }
}

if (canvas && supportsWebGL()) init();

function init() {
    const isSmall = window.innerWidth < 768;
    const COUNT = isSmall ? 7000 : 16000;
    const RING = isSmall ? 1500 : 3500;

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: false, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);
    camera.position.set(0, 0, 7);

    const COLORS = {
        feu: [new THREE.Color('#ff8a1f'), new THREE.Color('#e8562a')],
        eau: [new THREE.Color('#3ba3e0'), new THREE.Color('#7fd0ff')],
        terre: [new THREE.Color('#7cb342'), new THREE.Color('#4f8a2a')],
    };

    // Couleur selon l'angle autour de l'axe de vue, avec une frontière légèrement bruitée.
    function elementColor(x, y, jitter) {
        let a = Math.atan2(y, x) * 180 / Math.PI + jitter; // -180..180
        if (a < -180) a += 360;
        if (a > 180) a -= 360;
        let pair;
        if (a >= 95 || a < -150) pair = COLORS.feu;
        else if (a >= -30) pair = COLORS.eau;
        else pair = COLORS.terre;
        return pair[0].clone().lerp(pair[1], Math.random());
    }

    /* -------------------------------------------------------- Sphère principale */
    function buildSphere() {
        const pos = new Float32Array(COUNT * 3);
        const col = new Float32Array(COUNT * 3);
        const rnd = new Float32Array(COUNT * 4);
        for (let i = 0; i < COUNT; i++) {
            // Répartition uniforme sur la sphère (spirale de Fibonacci) + un peu d'épaisseur.
            const t = (i + 0.5) / COUNT;
            const phi = Math.acos(1 - 2 * t);
            const theta = Math.PI * (1 + Math.sqrt(5)) * i;
            const r = 1 + (Math.random() - 0.5) * 0.08;
            const x = Math.sin(phi) * Math.cos(theta) * r;
            const y = Math.sin(phi) * Math.sin(theta) * r;
            const z = Math.cos(phi) * r;
            pos.set([x, y, z], i * 3);
            const c = elementColor(x, y, (Math.random() - 0.5) * 18);
            col.set([c.r, c.g, c.b], i * 3);
            rnd.set([Math.random(), Math.random(), Math.random(), Math.random()], i * 4);
        }
        const g = new THREE.BufferGeometry();
        g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        g.setAttribute('aColor', new THREE.BufferAttribute(col, 3));
        g.setAttribute('aRandom', new THREE.BufferAttribute(rnd, 4));
        return g;
    }

    /* -------------------------------------------------------- Anneau orbital */
    function buildRing() {
        const pos = new Float32Array(RING * 3);
        const col = new Float32Array(RING * 3);
        const rnd = new Float32Array(RING * 4);
        for (let i = 0; i < RING; i++) {
            const a = Math.random() * Math.PI * 2;
            const r = 1.75 + Math.pow(Math.random(), 2) * 0.9;
            const x = Math.cos(a) * r;
            const y = Math.sin(a) * r;
            const z = (Math.random() - 0.5) * 0.08;
            pos.set([x, y, z], i * 3);
            const c = elementColor(x, y, 0);
            col.set([c.r, c.g, c.b], i * 3);
            rnd.set([Math.random(), Math.random() * 0.6, Math.random(), Math.random()], i * 4);
        }
        const g = new THREE.BufferGeometry();
        g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        g.setAttribute('aColor', new THREE.BufferAttribute(col, 3));
        g.setAttribute('aRandom', new THREE.BufferAttribute(rnd, 4));
        return g;
    }

    const uniforms = {
        uTime: { value: 0 },
        uScroll: { value: 0 },
        uIntro: { value: reduceMotion ? 1 : 0 },
        uMouse: { value: new THREE.Vector3(0, 0, 10) },
        uPixelRatio: { value: renderer.getPixelRatio() },
        uSize: { value: isSmall ? 34 : 30 },
        uAlpha: { value: 1 },
    };

    const vertexShader = /* glsl */`
        uniform float uTime;
        uniform float uScroll;
        uniform float uIntro;
        uniform vec3 uMouse;
        uniform float uPixelRatio;
        uniform float uSize;
        uniform float uIsRing;
        attribute vec3 aColor;
        attribute vec4 aRandom;
        varying vec3 vColor;
        varying float vAlpha;

        vec3 rotateY(vec3 p, float a) { float c = cos(a), s = sin(a); return vec3(c * p.x + s * p.z, p.y, -s * p.x + c * p.z); }

        void main() {
            vec3 p = position;
            vec3 dir = normalize(position);

            if (uIsRing < 0.5) {
                // Respiration organique : bruit trigonométrique en couches.
                float n = sin(p.x * 3.1 + uTime * 0.7) * sin(p.y * 2.7 + uTime * 0.55) * sin(p.z * 3.4 + uTime * 0.45);
                n += 0.5 * sin(p.y * 6.0 - uTime * 1.1 + aRandom.x * 6.28);
                p = dir * (1.55 + n * 0.12);
            } else {
                float a = uTime * (0.08 + aRandom.z * 0.12);
                float c = cos(a), s = sin(a);
                p.xy = mat2(c, -s, s, c) * p.xy;
                p.z += sin(uTime + aRandom.x * 6.28) * 0.05;
            }

            // Dispersion au défilement : chaque particule s'éloigne en tourbillonnant.
            float burst = uScroll * (1.5 + aRandom.y * 4.5);
            p += dir * burst;
            p = rotateY(p, uScroll * (aRandom.z - 0.5) * 3.0);

            // Arrivée : les particules convergent depuis un nuage lointain.
            vec3 from = (aRandom.xyz - 0.5) * 22.0;
            float intro = smoothstep(0.0, 1.0, clamp(uIntro * 1.4 - aRandom.w * 0.4, 0.0, 1.0));
            p = mix(from, p, intro);

            vec4 world = modelMatrix * vec4(p, 1.0);

            // Répulsion douce autour du pointeur.
            vec3 toMouse = world.xyz - uMouse;
            float d = length(toMouse);
            world.xyz += normalize(toMouse) * smoothstep(1.4, 0.0, d) * 0.55;

            vec4 mv = viewMatrix * world;
            gl_Position = projectionMatrix * mv;
            float size = uSize * (0.35 + aRandom.y * 0.9) * (uIsRing > 0.5 ? 0.7 : 1.0);
            gl_PointSize = size * uPixelRatio / -mv.z;

            vColor = aColor;
            vAlpha = (0.55 + aRandom.x * 0.45) * (1.0 - uScroll * 0.85) * intro;
        }
    `;

    const fragmentShader = /* glsl */`
        uniform float uAlpha;
        varying vec3 vColor;
        varying float vAlpha;
        void main() {
            float d = length(gl_PointCoord - 0.5);
            float a = smoothstep(0.5, 0.05, d);
            if (a < 0.01) discard;
            gl_FragColor = vec4(vColor, a * vAlpha * uAlpha);
        }
    `;

    const makeMaterial = (isRing) => new THREE.ShaderMaterial({
        uniforms: { ...uniforms, uIsRing: { value: isRing ? 1 : 0 } },
        vertexShader,
        fragmentShader,
        transparent: true,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
    });

    const group = new THREE.Group();
    const sphere = new THREE.Points(buildSphere(), makeMaterial(false));
    const ring = new THREE.Points(buildRing(), makeMaterial(true));
    ring.rotation.x = 1.15;
    ring.rotation.y = -0.35;
    group.add(sphere, ring);
    scene.add(group);

    // Mode clair : le mélange additif disparaît sur fond crème, on passe en mélange normal.
    function applyTheme(theme) {
        const light = theme === 'light';
        [sphere, ring].forEach((p) => {
            p.material.blending = light ? THREE.NormalBlending : THREE.AdditiveBlending;
            p.material.needsUpdate = true;
        });
        uniforms.uAlpha.value = light ? 0.85 : 1;
    }
    applyTheme(document.documentElement.dataset.theme);
    window.addEventListener('el:theme', (e) => applyTheme(e.detail));

    /* -------------------------------------------------------- Dimensions & position */
    function resize() {
        const w = hero.clientWidth;
        const h = hero.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        // Sur grand écran, l'orbe se place à droite pour laisser le titre lisible.
        const wide = w > 900;
        group.position.x = wide ? 1.9 : 0;
        group.position.y = wide ? 0 : 0.6;
        group.scale.setScalar(wide ? 1 : 0.82);
        uniforms.uAlpha.value = (document.documentElement.dataset.theme === 'light' ? 0.85 : 1) * (wide ? 1 : 0.7);
    }
    resize();
    window.addEventListener('resize', resize);

    /* -------------------------------------------------------- Pointeur */
    const pointer = new THREE.Vector2(0, 0);
    const target = new THREE.Vector2(0, 0);
    const raycaster = new THREE.Raycaster();
    const plane = new THREE.Plane(new THREE.Vector3(0, 0, 1), 0);
    const mouseWorld = new THREE.Vector3(0, 0, 10);
    hero.addEventListener('pointermove', (e) => {
        const r = hero.getBoundingClientRect();
        target.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
        raycaster.setFromCamera(target, camera);
        raycaster.ray.intersectPlane(plane, mouseWorld);
    });
    hero.addEventListener('pointerleave', () => { target.set(0, 0); mouseWorld.set(0, 0, 10); });

    /* -------------------------------------------------------- Intro */
    const playIntro = () => {
        if (reduceMotion) return;
        if (window.gsap) window.gsap.to(uniforms.uIntro, { value: 1, duration: 2.6, ease: 'expo.out' });
        else uniforms.uIntro.value = 1;
    };
    window.addEventListener('el:intro', playIntro, { once: true });
    // Filet de sécurité si l'événement d'intro est déjà passé.
    setTimeout(() => { if (uniforms.uIntro.value === 0) playIntro(); }, 4500);

    /* -------------------------------------------------------- Boucle de rendu (seulement si visible) */
    let visible = true;
    new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }).observe(hero);

    const clock = new THREE.Clock();
    const smoothMouse = new THREE.Vector3(0, 0, 10);
    function frame() {
        requestAnimationFrame(frame);
        if (!visible || document.hidden) return;

        const t = clock.getElapsedTime();
        uniforms.uTime.value = reduceMotion ? 0 : t;
        uniforms.uScroll.value = Math.min(1, Math.max(0, window.scrollY / hero.clientHeight));

        pointer.lerp(target, 0.05);
        smoothMouse.lerp(mouseWorld, 0.12);
        uniforms.uMouse.value.copy(smoothMouse);

        if (!reduceMotion) {
            sphere.rotation.y = t * 0.08 + pointer.x * 0.5;
            sphere.rotation.x = -pointer.y * 0.35;
            ring.rotation.z = t * 0.03;
            group.rotation.z = Math.sin(t * 0.2) * 0.05;
            camera.position.x = pointer.x * 0.4;
            camera.position.y = pointer.y * 0.3;
            camera.lookAt(group.position.x * 0.6, 0, 0);
        }

        renderer.render(scene, camera);
    }
    frame();
}
