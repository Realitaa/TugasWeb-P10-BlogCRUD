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

export function initPostSearchCommandPalette() {
    const palette = document.querySelector('[data-bw-command-palette][data-name="post-search-palette"]');
    if (!palette || palette.dataset.searchInitialised === 'true') return;
    palette.dataset.searchInitialised = 'true';

    const group = palette.querySelector('[data-bw-command-palette-group][data-group-name="posts"]');
    const itemsContainer = group?.querySelector('.bw-command-palette-group-items') || palette.querySelector('[data-bw-command-palette-list]');
    const emptyEl = palette.querySelector('[data-bw-command-palette-empty]');
    const input = palette.querySelector('[data-bw-command-palette-input]');
    if (!itemsContainer || !emptyEl || !input) return;

    let debounceTimer = null;
    let abortController = null;

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const clearResults = () => {
        itemsContainer.innerHTML = '';
        if (group) {
            group.hidden = true;
            group.style.display = 'none';
        }
        emptyEl.hidden = true;
        emptyEl.style.display = 'none';
    };

    // Ensure results and empty state are hidden initially
    clearResults();

    const fetchPosts = (query) => {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        fetch(`/posts/search?q=${encodeURIComponent(query)}`, {
            signal: abortController.signal,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error('Search failed');
                return res.json();
            })
            .then((posts) => {
                if (window.setCommandPaletteLoading) {
                    window.setCommandPaletteLoading('post-search-palette', false);
                }
                itemsContainer.innerHTML = '';

                if (!posts || posts.length === 0) {
                    if (group) {
                        group.hidden = true;
                        group.style.display = 'none';
                    }
                    emptyEl.hidden = false;
                    emptyEl.style.display = 'flex';
                    return;
                }

                emptyEl.hidden = true;
                emptyEl.style.display = 'none';
                if (group) {
                    group.hidden = false;
                    group.removeAttribute('hidden');
                    group.classList.remove('hidden');
                    group.style.display = 'block';
                }

                posts.forEach((post) => {
                    const item = document.createElement('a');
                    item.className = 'bw-command-palette-item group/item';
                    item.id = `bw-post-item-${post.id}`;
                    item.role = 'option';
                    item.setAttribute('data-bw-command-palette-item', '');
                    item.setAttribute('data-item-name', post.id);
                    item.setAttribute('data-keywords', `${query} ${post.title} ${post.body}`.toLowerCase());
                    item.setAttribute('aria-selected', 'false');
                    item.setAttribute('tabindex', '-1');
                    item.href = post.url;

                    item.innerHTML = `
                        <span class="bw-command-palette-item-icon" aria-hidden="true">
                            <svg class="size-5! text-gray-400 group-hover/item:text-primary-600 dark:group-hover/item:text-primary-400 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </span>
                        <span class="bw-command-palette-item-copy min-w-0 flex-1">
                            <span class="bw-command-palette-item-label font-medium">${escapeHtml(post.title)}</span>
                            <span class="bw-command-palette-item-description line-clamp-2 text-xs text-gray-500 dark:text-dark-300 mt-0.5">${escapeHtml(post.body)}</span>
                        </span>
                        <svg class="size-4! text-gray-300 dark:text-dark-500 group-hover/item:text-gray-500 dark:group-hover/item:text-dark-300 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    `;

                    item.addEventListener('click', () => {
                        window.location.href = post.url;
                    });

                    itemsContainer.appendChild(item);
                });

                // Highlight the first result
                const first = itemsContainer.querySelector('[data-bw-command-palette-item]');
                if (first) {
                    first.setAttribute('data-highlighted', 'true');
                    first.setAttribute('aria-selected', 'true');
                    input.setAttribute('aria-activedescendant', first.id);
                }
            })
            .catch((err) => {
                if (err.name === 'AbortError') return;
                console.error(err);
                if (window.setCommandPaletteLoading) {
                    window.setCommandPaletteLoading('post-search-palette', false);
                }
            });
    };

    const handleQuery = (rawQuery) => {
        const query = rawQuery.trim();
        clearTimeout(debounceTimer);

        if (!query) {
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            if (window.setCommandPaletteLoading) {
                window.setCommandPaletteLoading('post-search-palette', false);
            }
            clearResults();
        } else {
            emptyEl.hidden = true;
            emptyEl.style.display = 'none';
            if (window.setCommandPaletteLoading) {
                window.setCommandPaletteLoading('post-search-palette', true);
            }
            debounceTimer = setTimeout(() => {
                fetchPosts(query);
            }, 250);
        }
    };

    palette.addEventListener('bladewind:command-palette:opened', () => {
        clearResults();
        if (window.setCommandPaletteLoading) {
            window.setCommandPaletteLoading('post-search-palette', false);
        }
        requestAnimationFrame(() => {
            clearResults();
        });
    });

    palette.addEventListener('bladewind:command-palette:closed', () => {
        if (abortController) {
            abortController.abort();
            abortController = null;
        }
        clearTimeout(debounceTimer);
        clearResults();
    });

    palette.addEventListener('bladewind:command-palette:search', (e) => {
        handleQuery(e.detail?.query || '');
    });

    input.addEventListener('input', () => {
        if (!input.value.trim()) {
            handleQuery('');
        }
    });
}

function initApp() {
    initImageViewers();
    initPostSearchCommandPalette();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
