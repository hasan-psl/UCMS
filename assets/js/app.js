// UCMS Core JS - routing helpers, API, auth, simple UI behavior

const API_BASE = '/backend/php';

async function fetchJSON(path, options = {}) {
  const response = await fetch(path, {
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    ...options,
  });
  if (!response.ok) {
    const text = await response.text();
    throw new Error(text || `Request failed: ${response.status}`);
  }
  return await response.json();
}

// Auth helpers
async function checkAuth() {
  try {
    const me = await fetchJSON(`${API_BASE}/auth.php?action=check`);
    return me?.user || null;
  } catch {
    return null;
  }
}

async function login(email, password) {
  return fetchJSON(`${API_BASE}/auth.php?action=login`, {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  });
}

async function logout() {
  await fetchJSON(`${API_BASE}/auth.php?action=logout`, { method: 'POST' });
  window.location.href = '/login.html';
}

// Page utilities
function setActiveNav() {
  const links = document.querySelectorAll('.nav-link');
  const path = window.location.pathname.split('/').pop() || 'index.html';
  links.forEach((a) => {
    const href = a.getAttribute('href');
    if (href && href.endsWith(path)) a.classList.add('active');
  });
}

function smoothRedirect(url, delayMs = 300) {
  const ms = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : delayMs;
  setTimeout(() => { window.location.href = url; }, ms);
}

// --- MODERN MODAL INIT (MutationObserver + no duplicates) ---
function initDashboardModals() {
  const modal = document.getElementById('detailModal');
  if (!modal) return;
  const closeBtn = modal.querySelector('.modal-close');
  function hide() { UCMS.closeDetailModal(); }

  // Just in case, remove any previous listeners to avoid duplicates
  closeBtn.removeEventListener('click', hide);
  closeBtn.addEventListener('click', hide);
  modal.removeEventListener('click', modal._modalBgHandler||(()=>{}));
  modal._modalBgHandler = (e) => { if (e.target === modal) hide(); };
  modal.addEventListener('click', modal._modalBgHandler);
  document.removeEventListener('keydown', modal._modalEscHandler||(()=>{}));
  modal._modalEscHandler = (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) hide(); };
  document.addEventListener('keydown', modal._modalEscHandler);

  // Remove old listeners from rows before rebinding
  function removeRowListeners(sel, oldHandlerProp) {
    document.querySelectorAll(sel).forEach(row => {
      if (row[oldHandlerProp]) row.removeEventListener('click', row[oldHandlerProp]);
    });
  }
  function addRowListeners(sel, idAttr, type, urlBase, oldHandlerProp) {
    document.querySelectorAll(sel).forEach(row => {
      // Remove if previously attached
      if (row[oldHandlerProp]) row.removeEventListener('click', row[oldHandlerProp]);
      const handler = async () => {
        const id = row.dataset[idAttr];
        if (!id) return;
        try {
          const data = await fetchJSON(`${urlBase}${encodeURIComponent(id)}`);
          showDetailModal(type, data);
        } catch (e) {
          console.error(`Failed to load ${type}`, e);
        }
      };
      row.addEventListener('click', handler);
      row[oldHandlerProp] = handler; // So we can remove next time!
    });
  }
  removeRowListeners('.club-row', '_clubModalHandler');
  removeRowListeners('.event-row', '_eventModalHandler');
  addRowListeners('.club-row', 'clubId', 'club', '/backend/php/clubs.php?action=get&id=', '_clubModalHandler');
  addRowListeners('.event-row', 'eventId', 'event', '/backend/php/events.php?action=get&id=', '_eventModalHandler');

  // Log for debug
  console.log('[initDashboardModals] Modal interactions initialized at', new Date().toISOString());
}

// --- Setup MutationObservers after DOMContentLoaded ---
document.addEventListener('DOMContentLoaded', () => {
  setActiveNav();
  initFeaturedEventsCarousel();
  initMemberModalHandlers();
  initDashboardModals();

  // MutationObservers for dynamic table contents
  function observeTableRows(tblSelector) {
    const tbl = document.querySelector(tblSelector);
    if (!tbl) return;
    const observer = new MutationObserver(() => {
      initDashboardModals();
    });
    observer.observe(tbl, { childList: true });
  }
  observeTableRows('#tblClubs tbody');
  observeTableRows('#tblEvents tbody');
});

// Expose for inline handlers if needed
window.UCMS = {
  fetchJSON,
  checkAuth,
  login,
  logout,
  smoothRedirect,
  showMemberModal,
  closeModal,
  openMember,
  showDetailModal,
  closeDetailModal,
};

// Featured Events Carousel
async function initFeaturedEventsCarousel() {
  const viewport = document.getElementById('featuredEvents');
  if (!viewport) return;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const track = viewport.querySelector('.events-track');
  try {
    const data = await fetchJSON('/backend/php/events.php');
    const events = (data.events || []).slice(0, 8);
    if (!events.length) return;

    // Render cards
    track.innerHTML = events.map((ev) => {
      const img = ev.image_url || 'https://images.unsplash.com/photo-1519455953755-af066f52f1ea?auto=format&fit=crop&w=1200&q=60';
      const date = ev.start_time ? new Date(ev.start_time).toLocaleDateString() : '';
      const desc = ev.description ? String(ev.description).slice(0, 120) : '';
      return `
        <article class="event-card">
          <div class="event-media" style="background-image:url('${img}')"></div>
          <div class="event-body">
            <h3 class="event-title">${ev.title}</h3>
            <div class="event-meta">${date}${ev.location ? ' · ' + ev.location : ''}</div>
            <div class="card-meta">${desc}</div>
          </div>
        </article>`;
    }).join('');

    if (reduce) return; // no auto slide for reduced motion

    // Clone for seamless loop
    const initialCards = Array.from(track.children);
    initialCards.forEach((c) => track.appendChild(c.cloneNode(true)));

    let offset = 0;
    let rafId = null;
    let lastTs = 0;
    let paused = false;
    const gap = 22; // matches CSS gap

    function cardWidth() {
      const first = track.querySelector('.event-card');
      return first ? first.getBoundingClientRect().width : 300;
    }

    function step(ts) {
      if (paused) { rafId = requestAnimationFrame(step); return; }
      if (!lastTs) lastTs = ts;
      const dt = ts - lastTs;
      lastTs = ts;

      // pixels per second
      const speed = 40; // gentle
      offset += (speed * dt) / 1000;

      const cw = cardWidth() + gap;
      if (offset >= cw) {
        // move first card to end and adjust offset
        track.appendChild(track.firstElementChild);
        offset -= cw;
      }
      track.style.transform = `translateX(${-offset}px)`;
      rafId = requestAnimationFrame(step);
    }

    viewport.addEventListener('mouseenter', () => { paused = true; });
    viewport.addEventListener('mouseleave', () => { paused = false; });
    window.addEventListener('visibilitychange', () => { paused = document.hidden; });

    rafId = requestAnimationFrame(step);
  } catch (e) {
    // fail silently for homepage
    console.warn('Featured events failed to load', e);
  }
}

// Members modal logic
function initMemberModalHandlers() {
  const modal = document.getElementById('memberModal');
  if (!modal) return;
  const closeBtn = modal.querySelector('.modal-close');
  function hide() { UCMS.closeModal(); }
  closeBtn.addEventListener('click', hide);
  modal.addEventListener('click', (e) => { if (e.target === modal) hide(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) hide(); });
}

async function openMember(cardEl) {
  const id = cardEl?.dataset?.memberId;
  if (!id) return;
  const data = await fetchJSON(`/backend/php/members.php?action=get&id=${encodeURIComponent(id)}`);
  showMemberModal(data, cardEl);
}

let lastFocusedEl = null;
function showMemberModal(data, triggerEl) {
  const modal = document.getElementById('memberModal');
  if (!modal) return;
  lastFocusedEl = triggerEl || document.activeElement;
  document.getElementById('modalName').textContent = data.name || '';
  document.getElementById('modalStudentId').textContent = data.student_id || '';
  document.getElementById('modalClub').textContent = data.club_name || '';
  document.getElementById('modalPosition').textContent = data.position || '';
  document.getElementById('modalEmail').textContent = data.email || '';
  document.getElementById('modalContact').textContent = data.contact_no || '';
  document.getElementById('modalJoin').textContent = data.join_date || '';
  modal.classList.remove('hidden');
  modal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
  // focus trap start
  const focusables = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
  const first = focusables[0];
  const last = focusables[focusables.length - 1];
  function trap(e) {
    if (e.key !== 'Tab') return;
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }
  modal.addEventListener('keydown', trap);
  (first || modal).focus();
  modal._trap = trap;
}

function closeModal() {
  const modal = document.getElementById('memberModal');
  if (!modal || modal.classList.contains('hidden')) return;
  modal.classList.add('modal-closing');
  setTimeout(() => {
    modal.classList.remove('modal-closing');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (modal._trap) modal.removeEventListener('keydown', modal._trap);
    if (lastFocusedEl && typeof lastFocusedEl.focus === 'function') lastFocusedEl.focus();
    lastFocusedEl = null;
  }, 180);
}

// Dashboard detail modals (clubs/events)
let lastFocusedDetailEl = null;
function showDetailModal(type, data) {
  const modal = document.getElementById('detailModal');
  const content = document.getElementById('modalContent');
  if (!modal || !content) return;
  lastFocusedDetailEl = document.activeElement;
  modal.classList.remove('hidden');
  modal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';

  if (type === 'club') {
    content.innerHTML = `
      <h2 style="margin:0 0 12px;color:#fff;">${escapeHtml(data.club_name || '')}</h2>
      <p><strong>Description:</strong> ${escapeHtml(data.description || '')}</p>
      <p><strong>Established:</strong> ${escapeHtml(data.establishment_date || '')}</p>
      <p><strong>Email:</strong> ${escapeHtml(data.email || '')}</p>
      <p><strong>Faculty Advisor:</strong> ${escapeHtml(data.faculty_advisor_name || '')} (${escapeHtml(data.faculty_advisor_email || '')})</p>
      <p><strong>Created at:</strong> ${escapeHtml(data.created_at || '')}</p>
    `;
  } else if (type === 'event') {
    const dateStr = data.event_date ? new Date(data.event_date).toLocaleString() : '';
    content.innerHTML = `
      <h2 style="margin:0 0 12px;color:#fff;">${escapeHtml(data.event_name || '')}</h2>
      <p><strong>Date:</strong> ${escapeHtml(dateStr)}</p>
      <p><strong>Venue:</strong> ${escapeHtml(data.venue || '')}</p>
      <p><strong>Organiser Club:</strong> ${escapeHtml(data.organiser_club_name || '')}</p>
      <p><strong>Description:</strong> ${escapeHtml(data.description || '')}</p>
    `;
  }

  // Focus trap
  const focusables = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
  const first = focusables[0];
  const last = focusables[focusables.length - 1];
  function trap(e) {
    if (e.key !== 'Tab') return;
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }
  modal.addEventListener('keydown', trap);
  (first || modal).focus();
  modal._trap = trap;
}

function closeDetailModal() {
  const modal = document.getElementById('detailModal');
  if (!modal || modal.classList.contains('hidden')) return;
  modal.classList.add('modal-closing');
  setTimeout(() => {
    modal.classList.remove('modal-closing');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (modal._trap) modal.removeEventListener('keydown', modal._trap);
    if (lastFocusedDetailEl && typeof lastFocusedDetailEl.focus === 'function') lastFocusedDetailEl.focus();
    lastFocusedDetailEl = null;
  }, 180);
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}


