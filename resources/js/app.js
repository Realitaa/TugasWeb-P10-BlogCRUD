import 'viewerjs/dist/viewer.css';
import Viewer from 'viewerjs';

window.Viewer = Viewer;

export const viewerOptions = {
    inline: false,
    backdrop: true,
    button: true,
    focus: true,
    fullscreen: true,
    loading: true,
    loop: true,
    preload: true,
    keyboard: true,
    autoplay: false,
    movable: true,
    magnifier: false,
    navbar: true,
    navigation: false,
    rotatable: true,
    rotateOnGesture: true,
    rotateOnTouch: true,
    scalable: true,
    slideOnTouch: true,
    slideOnWheel: true,
    title: true,
    toggleOnDblclick: true,
    toolbar: false,
    tooltip: true,
    transition: true,
    zoomable: true,
    zoomOnGesture: true,
    zoomOnTouch: true,
    zoomOnWheel: true,
};

window.viewerOptions = viewerOptions;

export function initImageViewers() {
    document.querySelectorAll('[data-viewer]').forEach((el) => {
        if (el.dataset.viewerInitialised === 'true') return;
        el.dataset.viewerInitialised = 'true';
        new Viewer(el, viewerOptions);
    });
}

// Distinguish swiping from clicking on carousel images
let isPointerDown = false;
let isSwiping = false;
let startX = 0;
let startY = 0;

document.addEventListener(
    'pointerdown',
    (e) => {
        if (e.target.closest('[data-viewer] img')) {
            isPointerDown = true;
            startX = e.clientX;
            startY = e.clientY;
            isSwiping = false;
        }
    },
    true
);

document.addEventListener(
    'pointermove',
    (e) => {
        if (!isPointerDown) return;
        if (Math.abs(e.clientX - startX) > 10 || Math.abs(e.clientY - startY) > 10) {
            isSwiping = true;
        }
    },
    true
);

const resetPointer = () => {
    isPointerDown = false;
};
document.addEventListener('pointerup', resetPointer, true);
document.addEventListener('pointercancel', resetPointer, true);

document.addEventListener(
    'click',
    (e) => {
        if (isSwiping && e.target.closest('[data-viewer] img')) {
            e.preventDefault();
            e.stopPropagation();
            isSwiping = false;
        }
    },
    true
);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initImageViewers);
} else {
    initImageViewers();
}
