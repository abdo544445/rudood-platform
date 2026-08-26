<style>
:root {
  --gold: #d4af37;
  --gold-dark: #aa820a;
  --gold-soft: rgba(212,175,55,0.12);
  --bg-dark: #0b0f19;
  --card-bg: rgba(255,255,255,0.035);
  --card-border: rgba(212,175,55,0.2);
  --surface: rgba(15,23,42,0.7);
  --text-main: #ffffff;
  --text-muted: rgba(255,255,255,0.78);
  --font: 'Cairo', sans-serif;
  --radius: 12px;
  --sidebar-w: 255px;
  /* semantic status colors */
  --ok: #2ecc71; 
  --warn: #fbbf24; 
  --err: #ef4444;
}

/* High-Density Compact Enterprise Scale (Zoomed-Out Feel) */
html {
  font-size: 14.2px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  scroll-behavior: smooth;
}

@media (min-width: 1200px) {
  body {
    zoom: 0.94;
  }
}

/* Global Selection Highlight */
::selection {
  background: #d4af37 !important;
  color: #0b0f19 !important;
}
::-moz-selection {
  background: #d4af37 !important;
  color: #0b0f19 !important;
}

body {
  font-family: var(--font);
  background-color: var(--bg-dark);
  color: var(--text-main);
  letter-spacing: -0.1px;
}

/* High Contrast Global Text Helpers */
.text-muted, .text-secondary {
  color: rgba(255, 255, 255, 0.78) !important;
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.82) !important;
}

.form-label, label {
  color: #ffffff !important;
  font-weight: 600;
  font-size: 0.85rem;
  margin-bottom: 4px;
}

.form-control, .form-select {
  font-size: 0.88rem !important;
  padding: 0.45rem 0.85rem !important;
  border-radius: 8px !important;
}

.btn {
  font-size: 0.88rem !important;
  border-radius: 8px !important;
}

.btn-sm {
  font-size: 0.78rem !important;
  padding: 0.25rem 0.7rem !important;
}

/* Navigation Tabs Styling */
.nav-tabs {
  border-bottom: 2px solid rgba(212, 175, 55, 0.25) !important;
  gap: 6px;
}

.nav-tabs .nav-link {
  color: rgba(255, 255, 255, 0.8) !important;
  background: rgba(15, 23, 42, 0.6) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  padding: 7px 14px !important;
  font-size: 0.86rem !important;
  transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
  color: #d4af37 !important;
  background: rgba(212, 175, 55, 0.12) !important;
  border-color: rgba(212, 175, 55, 0.4) !important;
}

.nav-tabs .nav-link.active {
  color: #0b0f19 !important;
  background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important;
  border: 1px solid #d4af37 !important;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3) !important;
}

.nav-tabs .nav-link.active i {
  color: #0b0f19 !important;
}

.sidebar {
  width: var(--sidebar-w);
  height: 100vh;
  position: fixed;
  top: 0;
  right: 0;
  background: linear-gradient(180deg, rgba(15,23,42,0.96) 0%, var(--bg-dark) 100%);
  backdrop-filter: blur(16px);
  border-left: 1px solid var(--card-border);
  z-index: 1000;
  transition: all 0.3s ease;
  overflow-y: auto;
}

.nav-section-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gold);
  letter-spacing: 0.5px;
  margin-top: 1rem;
  margin-bottom: 0.35rem;
  padding-right: 0.65rem;
  opacity: 0.9;
}

.sidebar .nav-link {
  color: var(--text-muted) !important;
  border-bottom: none !important;
  padding: 0.55rem 0.85rem !important;
  border-radius: 8px;
  margin-bottom: 3px;
  font-weight: 600;
  font-size: 0.88rem;
  transition: all 0.2s ease;
}

.sidebar .nav-link.active {
  color: #fff !important;
  background: linear-gradient(90deg, var(--gold-dark) 0%, var(--gold-soft) 100%) !important;
  box-shadow: 0 3px 10px var(--gold-soft);
}

.sidebar .nav-link:hover:not(.active) {
  background: rgba(255, 255, 255, 0.05);
  color: #fff !important;
}

.main-content {
  margin-right: var(--sidebar-w);
  padding: 1.35rem 1.75rem;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.card-custom, .stat-card {
  background: var(--card-bg) !important;
  backdrop-filter: blur(12px);
  border: 1px solid var(--card-border) !important;
  border-radius: var(--radius);
  color: var(--text-main) !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

.table-dark-custom, .custom-dark-table {
  width: 100%;
  border-collapse: collapse;
  color: var(--text-main) !important;
  background: transparent !important;
  font-size: 0.88rem;
}

.table-dark-custom th, .custom-dark-table th {
  background: var(--gold-soft) !important;
  color: var(--gold) !important;
  padding: 10px 14px;
  border-bottom: 1px solid rgba(212, 175, 55, 0.25);
  text-align: right;
  font-size: 0.8rem;
  font-weight: 700;
}

.table-dark-custom td, .custom-dark-table td {
  background: var(--surface) !important;
  color: var(--text-main) !important;
  padding: 10px 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.table-dark-custom tr:hover td, .custom-dark-table tr:hover td {
  background: rgba(212, 175, 55, 0.08) !important;
}

/* Common Gold elements */
.text-gold { color: var(--gold) !important; }
.bg-gold { background-color: var(--gold) !important; color: var(--bg-dark) !important; }
.btn-gold { 
    background-color: var(--gold) !important; 
    color: var(--bg-dark) !important;
    font-weight: bold;
}
.btn-gold:hover { background-color: var(--gold-dark) !important; color: #fff !important; }

.icon-box-dash, .stat-card-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: var(--gold-soft) !important;
  color: var(--gold) !important;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

/* ──────────────────────────────────────────────────────────────────────────
   Dark Theme Bootstrap Pagination
   ────────────────────────────────────────────────────────────────────────── */
.pagination {
  margin-bottom: 0;
  gap: 4px;
  align-items: center;
}

.pagination .page-item .page-link {
  background: rgba(15, 23, 42, 0.8) !important;
  border: 1px solid rgba(212, 175, 55, 0.25) !important;
  color: rgba(255, 255, 255, 0.85) !important;
  border-radius: 8px !important;
  font-size: 0.85rem !important;
  padding: 6px 12px !important;
  min-width: 36px;
  text-align: center;
  transition: all 0.2s ease;
  box-shadow: none !important;
}

.pagination .page-item .page-link:hover {
  background: rgba(212, 175, 55, 0.18) !important;
  color: #d4af37 !important;
  border-color: #d4af37 !important;
}

.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important;
  color: #0b0f19 !important;
  font-weight: 800 !important;
  border-color: #d4af37 !important;
  box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3) !important;
}

.pagination .page-item.disabled .page-link {
  background: rgba(255, 255, 255, 0.03) !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
  color: rgba(255, 255, 255, 0.3) !important;
}

/* Guard against raw unconstrained SVGs inside pagination nav */
nav[role="navigation"] svg, .pagination svg {
  max-width: 14px !important;
  max-height: 14px !important;
  display: inline-block !important;
  vertical-align: middle !important;
}
</style>
