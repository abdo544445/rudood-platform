<!-- Global Command Palette Modal (Cmd + K / Ctrl + K) -->
<div class="modal fade" id="commandPaletteModal" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 620px;">
    <div class="modal-content" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(212, 175, 55, 0.35); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
      
      <!-- Search Input Header -->
      <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
        <i class="bi bi-search text-gold fs-5"></i>
        <input type="text" id="commandPaletteInput" class="form-control bg-transparent text-white border-0 shadow-none fs-6" placeholder="اكتب للبحث السريع في الصفحات والمتاجر والإجراءات... (أو اضغط ESC للإغلاق)" autofocus>
        <span class="badge bg-dark border border-secondary border-opacity-50 text-white-50 px-2 py-1 fs-8">ESC</span>
      </div>

      <!-- Search Results Container -->
      <div class="p-2" id="commandPaletteResults" style="max-height: 380px; overflow-y: auto;">
        <!-- Loaded dynamically via JS -->
      </div>

      <!-- Footer Help Hints -->
      <div class="p-2 px-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center text-white-50 fs-8">
        <div>
          <span class="me-2"><kbd class="bg-dark border border-secondary text-white-50 px-1">↑</kbd> <kbd class="bg-dark border border-secondary text-white-50 px-1">↓</kbd> للتنقل</span>
          <span><kbd class="bg-dark border border-secondary text-white-50 px-1">↵</kbd> للاختيار</span>
        </div>
        <div class="text-gold fw-bold">
          <i class="bi bi-lightning-charge-fill me-1"></i> موجه الأوامر الذكي
        </div>
      </div>

    </div>
  </div>
</div>

<style>
.palette-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-radius: 10px;
  color: #fff;
  text-decoration: none;
  transition: all 0.15s ease;
  margin-bottom: 2px;
}
.palette-item:hover, .palette-item.selected {
  background: rgba(212, 175, 55, 0.15);
  border-right: 3px solid #d4af37;
  color: #fff;
}
.palette-item i {
  color: #d4af37;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  let modalEl = document.getElementById('commandPaletteModal');
  if (!modalEl) return;
  
  let paletteModal = new bootstrap.Modal(modalEl);
  let inputEl = document.getElementById('commandPaletteInput');
  let resultsEl = document.getElementById('commandPaletteResults');
  let selectedIndex = 0;
  let currentItems = [];

  // Global Keyboard Listener: Cmd+K or Ctrl+K
  document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      paletteModal.show();
      setTimeout(() => inputEl.focus(), 150);
      fetchResults('');
    }
  });

  // Modal Shown Auto-Focus
  modalEl.addEventListener('shown.bs.modal', () => {
    inputEl.focus();
    fetchResults(inputEl.value);
  });

  // Live Search with Debounce
  let debounceTimer;
  inputEl.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      fetchResults(e.target.value);
    }, 120);
  });

  // Arrow Key Navigation in Palette
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (currentItems.length > 0) {
        selectedIndex = (selectedIndex + 1) % currentItems.length;
        renderItems();
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (currentItems.length > 0) {
        selectedIndex = (selectedIndex - 1 + currentItems.length) % currentItems.length;
        renderItems();
      }
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (currentItems[selectedIndex]) {
        window.location.href = currentItems[selectedIndex].url;
      }
    }
  });

  function fetchResults(query) {
    fetch(`/api/command-palette/search?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        currentItems = data.results || [];
        selectedIndex = 0;
        renderItems();
      })
      .catch(() => {
        resultsEl.innerHTML = '<div class="text-center text-white-50 py-3 fs-7">تعذر جلب النتائج</div>';
      });
  }

  function renderItems() {
    if (currentItems.length === 0) {
      resultsEl.innerHTML = '<div class="text-center text-white-50 py-4 fs-7"><i class="bi bi-search d-block fs-3 mb-2 opacity-50"></i>لا توجد نتائج مطابقة</div>';
      return;
    }

    resultsEl.innerHTML = currentItems.map((item, idx) => `
      <a href="${item.url}" class="palette-item ${idx === selectedIndex ? 'selected' : ''}" data-idx="${idx}">
        <div class="d-flex align-items-center gap-3">
          <div style="width:32px; height:32px; border-radius:8px; background:rgba(212,175,55,0.15); display:flex; align-items:center; justify-content:center;">
            <i class="bi ${item.icon} fs-6"></i>
          </div>
          <div>
            <div class="fw-bold fs-7 text-white">${item.title}</div>
            <div class="text-white-50 fs-8">${item.subtitle}</div>
          </div>
        </div>
        <span class="badge bg-secondary bg-opacity-25 text-white-50 fs-8 border border-secondary border-opacity-25">${item.badge || 'انتقال'}</span>
      </a>
    `).join('');

    // Click selection
    document.querySelectorAll('.palette-item').forEach(el => {
      el.addEventListener('mouseenter', () => {
        selectedIndex = parseInt(el.getAttribute('data-idx'));
        document.querySelectorAll('.palette-item').forEach(i => i.classList.remove('selected'));
        el.classList.add('selected');
      });
    });
  }
});
</script>
