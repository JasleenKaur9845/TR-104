<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BackupVault — Centralised Backup Monitor</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ============================================================
   DESIGN SYSTEM
   ============================================================ */
:root {
  --bg:        #0d0f14;
  --bg2:       #13161e;
  --bg3:       #1a1e2a;
  --border:    #252a38;
  --border2:   #2e3448;
  --text:      #c8cfe0;
  --text-dim:  #5a647a;
  --text-mid:  #8892a4;
  --accent:    #3b82f6;
  --accent2:   #60a5fa;
  --green:     #22c55e;
  --green-dim: #166534;
  --red:       #ef4444;
  --red-dim:   #7f1d1d;
  --yellow:    #f59e0b;
  --sidebar-w: 220px;
  --font-mono: 'IBM Plex Mono', monospace;
  --font-sans: 'IBM Plex Sans', sans-serif;
}

/* Light mode overrides */
body.light-mode {
  --bg:        #f0f2f7;
  --bg2:       #ffffff;
  --bg3:       #e8ebf2;
  --border:    #d1d7e4;
  --border2:   #b8c0d4;
  --text:      #1a2035;
  --text-dim:  #8892a4;
  --text-mid:  #4a5568;
  --accent:    #2563eb;
  --accent2:   #3b82f6;
  --green:     #16a34a;
  --green-dim: #bbf7d0;
  --red:       #dc2626;
  --red-dim:   #fecaca;
  --yellow:    #d97706;
}

/* Global reset + smooth theme transitions */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0; padding: 0;
  transition: background-color 0.25s ease, border-color 0.25s ease, color 0.15s ease;
}

html, body {
  height: 100%;
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-sans);
  font-size: 14px;
  line-height: 1.6;
  overflow: hidden;
}

/* ── Layout ── */
.app  { display: flex; height: 100vh; }
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

/* ── Sidebar ── */
.sidebar {
  width: var(--sidebar-w);
  min-width: var(--sidebar-w);
  background: var(--bg2);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.sidebar-brand {
  padding: 20px 18px 16px;
  border-bottom: 1px solid var(--border);
}
.logo-row  { display: flex; align-items: center; gap: 10px; }
.logo-icon {
  width: 32px; height: 32px;
  background: var(--accent);
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; flex-shrink: 0;
}
.logo-text { font-family: var(--font-mono); font-size: 13px; font-weight: 600; color: var(--text); letter-spacing: .04em; }
.logo-sub  { font-size: 10px; color: var(--text-dim); font-family: var(--font-mono); margin-top: 2px; letter-spacing: .06em; text-transform: uppercase; }
.sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
.nav-section-label {
  font-family: var(--font-mono); font-size: 9px; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--text-dim); padding: 10px 18px 4px;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 18px; cursor: pointer;
  font-size: 13px; font-weight: 500; color: var(--text-mid);
  border-left: 2px solid transparent; user-select: none;
}
.nav-item:hover  { background: var(--bg3); color: var(--text); }
.nav-item.active { background: rgba(59,130,246,.10); color: var(--accent2); border-left-color: var(--accent); }
.nav-icon  { font-size: 15px; width: 18px; text-align: center; }
.nav-badge {
  margin-left: auto; background: var(--red); color: #fff;
  font-family: var(--font-mono); font-size: 9px; font-weight: 600;
  padding: 1px 5px; border-radius: 8px; min-width: 16px; text-align: center;
}
.sidebar-footer { border-top: 1px solid var(--border); padding: 14px 18px; }
.status-dot { display: flex; align-items: center; gap: 8px; font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); }
.dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); box-shadow: 0 0 6px var(--green); }

/* ── Topbar ── */
.topbar {
  height: 52px; background: var(--bg2);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center;
  padding: 0 24px; gap: 12px; flex-shrink: 0;
}
.topbar-title { font-family: var(--font-mono); font-size: 12px; font-weight: 600; color: var(--text); letter-spacing: .06em; text-transform: uppercase; }
.topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
.topbar-time  { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); }
.topbar-sep   { color: var(--border2); }
.topbar-user  { font-family: var(--font-mono); font-size: 11px; color: var(--text-mid); }
.theme-toggle {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px;
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: 6px; cursor: pointer; font-size: 14px;
  color: var(--text-mid); padding: 0; line-height: 1;
}
.theme-toggle:hover { background: var(--border2); color: var(--text); transform: scale(1.1); }
.btn-logout {
  font-family: var(--font-mono); font-size: 11px;
  color: var(--red); background: transparent;
  border: 1px solid var(--red-dim); border-radius: 4px;
  padding: 4px 10px; cursor: pointer; text-decoration: none;
  display: inline-flex; align-items: center;
}
.btn-logout:hover { background: rgba(239,68,68,.1); }

/* ── Content area ── */
.content {
  flex: 1; overflow-y: auto;
  padding: 24px;
  display: flex; flex-direction: column; gap: 20px;
}
.page { display: none; flex-direction: column; gap: 20px; }
.page.active { display: flex; }

/* ── Stats row ── */
.stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
.stat-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: 8px; padding: 16px 18px;
  display: flex; flex-direction: column; gap: 4px;
}
.stat-label { font-family: var(--font-mono); font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: .12em; color: var(--text-dim); }
.stat-value { font-family: var(--font-mono); font-size: 28px; font-weight: 600; line-height: 1.1; }
.stat-card.total   .stat-value { color: var(--accent2); }
.stat-card.running .stat-value { color: var(--yellow); }
.stat-card.success .stat-value { color: var(--green); }
.stat-card.failed  .stat-value { color: var(--red); }

/* ── Form card ── */
.form-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
.form-card-title { font-family: var(--font-mono); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--text-dim); margin-bottom: 16px; }
.form-row  { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-label { font-family: var(--font-mono); font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: .08em; color: var(--text-dim); }
.form-input {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: 5px; padding: 8px 10px;
  font-family: var(--font-mono); font-size: 12px; color: var(--text);
  outline: none; width: 100%;
}
.form-input:focus { border-color: var(--accent); }
.form-input::placeholder { color: var(--text-dim); }

/* Select dropdown — same look as form-input */
.form-select {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: 5px; padding: 8px 10px;
  font-family: var(--font-mono); font-size: 12px; color: var(--text);
  outline: none; width: 100%;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%235a647a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  padding-right: 28px;
}
.form-select:focus { border-color: var(--accent); }
.form-select option { background: var(--bg2); color: var(--text); }
.form-hint { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-top: 4px; }

/* ── Buttons ── */
.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px; border: none; border-radius: 5px;
  font-family: var(--font-mono); font-size: 12px; font-weight: 600;
  cursor: pointer; white-space: nowrap;
}
.btn-primary { background: var(--accent); color: #fff; }
.btn-primary:hover { background: #2563eb; transform: translateY(-1px); }
.btn-primary:disabled { opacity: .5; cursor: not-allowed; transform: none; }
.btn-danger  { background: var(--red); color: #fff; }
.btn-danger:hover  { background: #dc2626; }
.btn-ghost   { background: var(--bg3); color: var(--text-mid); border: 1px solid var(--border); }
.btn-ghost:hover { background: var(--border2); color: var(--text); }
.btn-sm { padding: 5px 10px; font-size: 11px; }

/* ── Feedback ── */
.form-feedback { margin-top: 10px; font-family: var(--font-mono); font-size: 11px; padding: 8px 12px; border-radius: 4px; display: none; }
.form-feedback.success { background: rgba(34,197,94,.1); border: 1px solid var(--green-dim); color: var(--green); display: block; }
.form-feedback.error   { background: rgba(239,68,68,.1);  border: 1px solid var(--red-dim);   color: var(--red);   display: block; }

/* ── Table card ── */
.table-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: 8px;
  overflow: visible;   /* IMPORTANT: allows tooltip to escape */
  flex: 1;
}
.table-toolbar {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 16px; border-bottom: 1px solid var(--border);
  flex-wrap: wrap;
  /* Keep rounded corners on toolbar */
  border-radius: 8px 8px 0 0;
  background: var(--bg2);
}
.table-toolbar-title { font-family: var(--font-mono); font-size: 11px; color: var(--text-mid); font-weight: 600; text-transform: uppercase; letter-spacing: .08em; }
.filter-btns { display: flex; gap: 4px; margin-left: auto; }
.filter-btn {
  padding: 4px 10px; border-radius: 4px; border: 1px solid var(--border);
  background: transparent; font-family: var(--font-mono); font-size: 10px;
  font-weight: 600; color: var(--text-dim); cursor: pointer;
  text-transform: uppercase; letter-spacing: .06em;
}
.filter-btn:hover { background: var(--bg3); color: var(--text); }
.filter-btn.active          { background: var(--accent); border-color: var(--accent); color: #fff; }
.filter-btn.running.active  { background: var(--yellow); border-color: var(--yellow); color: #000; }
.filter-btn.success.active  { background: var(--green);  border-color: var(--green);  color: #000; }
.filter-btn.failed.active   { background: var(--red);    border-color: var(--red); }
.auto-refresh-toggle {
  font-family: var(--font-mono); font-size: 10px;
  padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border);
  background: transparent; cursor: pointer; color: var(--text-dim);
}
.auto-refresh-toggle.on { color: var(--green); border-color: var(--green-dim); }

/* Table scroll — only clip horizontally, never vertically (tooltip must escape) */
.table-scroll { overflow-x: auto; overflow-y: visible; }

table { width: 100%; border-collapse: collapse; }
thead th {
  font-family: var(--font-mono); font-size: 9px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .1em; color: var(--text-dim);
  padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border);
  background: var(--bg2); white-space: nowrap;
}
tbody tr { border-bottom: 1px solid var(--border); }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: rgba(128,128,128,.04); }
tbody td { padding: 10px 14px; font-family: var(--font-mono); font-size: 12px; color: var(--text-mid); vertical-align: middle; }
.td-ip   { color: var(--text) !important; }
.td-path { color: var(--text-dim) !important; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.td-time { font-size: 11px; color: var(--text-dim); white-space: nowrap; }

/* Serial number column — fixed narrow width */
.td-serial {
  font-family: var(--font-mono); font-size: 11px; color: var(--text-dim);
  width: 42px; min-width: 42px; max-width: 42px;
  text-align: right; padding-right: 8px !important; white-space: nowrap;
}
thead th:first-child { width: 42px; min-width: 42px; text-align: right; }

/* ── Status badges ── */
.badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 9px; border-radius: 4px;
  font-family: var(--font-mono); font-size: 10px; font-weight: 600;
  letter-spacing: .06em; text-transform: uppercase;
}
.badge-running { background: rgba(245,158,11,.15); color: var(--yellow); border: 1px solid rgba(245,158,11,.3); }
.badge-success { background: rgba(34,197,94,.12);  color: var(--green);  border: 1px solid rgba(34,197,94,.25); }
.badge-failed  { background: rgba(239,68,68,.12);  color: var(--red);    border: 1px solid rgba(239,68,68,.25); }
.badge-pulse   { width: 5px; height: 5px; border-radius: 50%; background: currentColor; animation: pulse 1.2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.3;} }

/* ── Progress bar ── */
.prog-wrap { width: 82px; }
.prog-bar  { height: 4px; background: var(--border); border-radius: 2px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 2px; }
.prog-fill-running { background: var(--yellow); }
.prog-fill-success { background: var(--green); }
.prog-fill-failed  { background: var(--red); }
.prog-text { font-size: 10px; color: var(--text-dim); margin-top: 3px; }

/* ── Info icon ── */
.info-icon {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--bg3); border: 1px solid var(--border);
  color: var(--text-dim); font-size: 11px; cursor: pointer;
  position: relative; user-select: none;
}
.info-icon:hover  { background: var(--accent); color: #fff; border-color: var(--accent); }
.info-icon:active { transform: scale(.92); }

/* ── Job Detail Modal — opened on ℹ click ── */
#job-modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9000;
  background: rgba(0, 0, 0, .65);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity .2s ease;
}
#job-modal-overlay.open {
  opacity: 1;
  pointer-events: all;
}
#job-modal {
  background: var(--bg2);
  border: 1px solid var(--border2);
  border-radius: 12px;
  width: 100%;
  max-width: 620px;
  margin: 24px;
  box-shadow: 0 24px 64px rgba(0, 0, 0, .7);
  transform: translateY(16px) scale(.97);
  transition: transform .2s ease;
  overflow: hidden;
}
#job-modal-overlay.open #job-modal {
  transform: translateY(0) scale(1);
}

/* Modal header */
#job-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px 17px;
  background: var(--bg3);
  border-bottom: 1px solid var(--border);
}
#job-modal-title {
  font-family: var(--font-mono);
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .1em;
  color: var(--text-mid);
}
#job-modal-close {
  display: inline-flex; align-items: center; justify-content: center;
  width: 30px; height: 30px; border-radius: 50%;
  background: transparent; border: 1px solid var(--border);
  color: var(--text-dim); font-size: 16px; cursor: pointer;
  line-height: 1; padding: 0;
  transition: background .12s, color .12s, border-color .12s;
}
#job-modal-close:hover {
  background: var(--red-dim); border-color: var(--red);
  color: var(--red);
}

/* Modal body rows */
#job-modal-body { padding: 6px 0 10px; }
.jm-row {
  display: flex;
  align-items: flex-start;
  padding: 14px 24px;
  border-bottom: 1px solid var(--border);
  gap: 20px;
}
.jm-row:last-child { border-bottom: none; }
.jm-label {
  font-family: var(--font-mono);
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: var(--text-dim);
  width: 130px;
  flex-shrink: 0;
  padding-top: 2px;
}
.jm-value {
  font-family: var(--font-mono);
  font-size: 13px;
  color: var(--text);
  word-break: break-all;
  line-height: 1.6;
  flex: 1;
}
.jm-value.dim   { color: var(--text-mid); font-size: 12px; }
.jm-value.mono  { letter-spacing: .02em; }

/* Modal footer */
#job-modal-footer {
  padding: 14px 24px;
  background: var(--bg3);
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: flex-end;
}

/* Inline .tooltip span — unused, hidden */
.tooltip { display: none !important; }

/* Tooltip inner layout — reused inside modal body */
.tt-header {
  background: var(--bg3); border-bottom: 1px solid var(--border);
  padding: 7px 12px; font-size: 9px; font-weight: 600;
  letter-spacing: .1em; text-transform: uppercase; color: var(--text-dim);
}
.tt-row {
  display: flex; flex-direction: column; gap: 2px;
  padding: 6px 12px; border-bottom: 1px solid var(--border);
}
.tt-row:last-child { border-bottom: none; }
.tt-label { font-size: 9px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--text-dim); }
.tt-value { font-size: 11px; color: var(--text); word-break: break-all; line-height: 1.4; }
.tt-reason .tt-value { color: var(--text-mid); font-size: 10px; line-height: 1.5; white-space: pre-wrap; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 48px 24px; font-family: var(--font-mono); }
.empty-state .empty-icon  { font-size: 32px; margin-bottom: 12px; opacity: .4; }
.empty-state .empty-title { font-size: 13px; color: var(--text-mid); margin-bottom: 6px; }
.empty-state .empty-sub   { font-size: 11px; color: var(--text-dim); }

/* ── Section header ── */
.section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 4px; }
.section-title  { font-family: var(--font-mono); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--text-mid); }
.section-line   { flex: 1; height: 1px; background: var(--border); }
.section-meta   { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); }

/* ── Logs toolbar ── */
.logs-toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.search-input {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: 5px; padding: 7px 12px;
  font-family: var(--font-mono); font-size: 12px; color: var(--text);
  outline: none; width: 220px;
}
.search-input:focus { border-color: var(--accent); }
.search-input::placeholder { color: var(--text-dim); }

/* ── Pagination ── */
.pagination { display: flex; gap: 4px; align-items: center; font-family: var(--font-mono); font-size: 11px; }
.page-btn {
  padding: 4px 9px; background: var(--bg3); border: 1px solid var(--border);
  color: var(--text-mid); border-radius: 4px; cursor: pointer;
  font-family: var(--font-mono); font-size: 11px;
}
.page-btn:hover    { background: var(--border2); color: var(--text); }
.page-btn.active   { background: var(--accent); border-color: var(--accent); color: #fff; }
.page-btn:disabled { opacity: .3; cursor: default; }

/* ── Servers page ── */
.two-col { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
.server-form-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
.server-form-card .form-group { margin-bottom: 12px; }
.server-list-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
.server-row {
  display: flex; align-items: center; padding: 12px 16px;
  border-bottom: 1px solid var(--border); gap: 12px;
}
.server-row:last-child { border-bottom: none; }
.server-row:hover { background: rgba(128,128,128,.04); }
.server-ip   { font-family: var(--font-mono); font-size: 13px; color: var(--text); font-weight: 500; }
.server-name { font-family: var(--font-sans); font-size: 12px; color: var(--text-mid); }
.server-date { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-left: auto; }
.server-del-btn {
  background: transparent; border: 1px solid transparent;
  color: var(--text-dim); font-size: 13px; cursor: pointer;
  padding: 4px 6px; border-radius: 4px;
}
.server-del-btn:hover { background: rgba(239,68,68,.12); color: var(--red); border-color: var(--red-dim); }

/* ── Settings page ── */
.settings-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 28px 32px; width: 100%; }
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
.settings-divider { height: 1px; background: var(--border); margin: 24px 0; }
.settings-section-title { font-family: var(--font-mono); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--text-dim); margin-bottom: 18px; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .stats-row { grid-template-columns: 1fr 1fr; }
  .form-row  { grid-template-columns: 1fr; }
  .two-col   { grid-template-columns: 1fr; }
}
@media (max-width: 620px) {
  .sidebar { width: 52px; min-width: 52px; }
  .sidebar-brand .logo-text, .sidebar-brand .logo-sub,
  .nav-item span:not(.nav-icon), .nav-badge, .nav-section-label { display: none; }
  .nav-item { padding: 10px; justify-content: center; }
  .sidebar-brand { padding: 12px 10px; }
}

/* ── Scrollbar ── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
</style>
</head>
<body>

<div class="app">

  <!-- ═══════════════ SIDEBAR ═══════════════ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="logo-row">
        <div class="logo-icon">🗄️</div>
        <div>
          <div class="logo-text">BackupVault</div>
          <div class="logo-sub">Monitor</div>
        </div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main</div>
      <div class="nav-item active" data-page="dashboard"><span class="nav-icon">⬛</span><span>Dashboard</span></div>
      <div class="nav-item" data-page="servers"><span class="nav-icon">🖥</span><span>Servers</span></div>
      <div class="nav-item" data-page="logs"><span class="nav-icon">📋</span><span>Logs</span><span class="nav-badge" id="running-badge" style="display:none">0</span></div>
      <div class="nav-section-label" style="margin-top:8px">System</div>
      <div class="nav-item" data-page="settings"><span class="nav-icon">⚙</span><span>Settings</span></div>
    </nav>
    <div class="sidebar-footer">
      <div class="status-dot"><span class="dot"></span><span>System Online</span></div>
    </div>
  </aside>

  <!-- ═══════════════ MAIN ═══════════════ -->
  <div class="main">
    <header class="topbar">
      <span class="topbar-title" id="page-title">Dashboard</span>
      <div class="topbar-right">
        <span class="topbar-time" id="clock">--:--:--</span>
        <span class="topbar-sep">|</span>
        <button class="theme-toggle" id="theme-toggle" title="Toggle light/dark mode"><span id="theme-icon">☀</span></button>
        <span class="topbar-sep">|</span>
        <span class="topbar-user">👤 <?= htmlspecialchars($admin_username) ?></span>
        <a class="btn-logout" href="logout.php" onclick="return confirm('Log out?')">⏻ Logout</a>
      </div>
    </header>

    <div class="content">

      <!-- ═══ DASHBOARD ═══ -->
      <div class="page active" id="page-dashboard">

        <!-- Stats -->
        <div class="stats-row">
          <div class="stat-card total">  <div class="stat-label">Total Jobs</div>  <div class="stat-value" id="stat-total">—</div>  </div>
          <div class="stat-card running"><div class="stat-label">Running</div>     <div class="stat-value" id="stat-running">—</div></div>
          <div class="stat-card success"><div class="stat-label">Successful</div>  <div class="stat-value" id="stat-success">—</div></div>
          <div class="stat-card failed"> <div class="stat-label">Failed</div>      <div class="stat-value" id="stat-failed">—</div> </div>
        </div>

        <!-- Trigger Backup form -->
        <div class="form-card">
          <div class="form-card-title">▶ Trigger Backup</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Source Server</label>
              <select class="form-select" id="f-src-ip">
                <option value="">— select source —</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Destination Server</label>
              <select class="form-select" id="f-dst-ip">
                <option value="">— select destination —</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Source Path</label>
              <input class="form-input" id="f-src-path" type="text" placeholder="/var/www/html" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="form-label">Destination Path</label>
              <input class="form-input" id="f-dst-path" type="text" placeholder="/backups/www" autocomplete="off">
            </div>
            <button class="btn btn-primary" id="btn-start-backup">▶ Start Backup</button>
          </div>
          <div class="form-feedback" id="backup-feedback"></div>
        </div>

        <!-- Jobs table -->
        <div class="table-card">
          <div class="table-toolbar">
            <span class="table-toolbar-title">Backup Jobs</span>
            <div class="filter-btns">
              <button class="filter-btn active" data-filter="">All</button>
              <button class="filter-btn running" data-filter="RUNNING">Running</button>
              <button class="filter-btn success" data-filter="SUCCESS">Success</button>
              <button class="filter-btn failed"  data-filter="FAILED">Failed</button>
            </div>
            <button class="auto-refresh-toggle on" id="refresh-toggle">↻ 2s</button>
          </div>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Source IP</th>
                  <th>Destination IP</th>
                  <th>Status</th>
                  <th>Progress</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody id="jobs-tbody">
                <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">⏳</div><div class="empty-title">Loading...</div></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </div><!-- /dashboard -->

      <!-- ═══ SERVERS ═══ -->
      <div class="page" id="page-servers">
        <div class="section-header">
          <span class="section-title">Authorized Servers</span>
          <span class="section-line"></span>
          <span class="section-meta" id="server-count">0 servers</span>
        </div>
        <div class="two-col">
          <div class="server-form-card">
            <div class="form-card-title">＋ Register New Server</div>
            <div class="form-group">
              <label class="form-label">Server IP Address</label>
              <input class="form-input" id="s-ip" type="text" placeholder="192.168.1.50" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="form-label">Server Name / Label</label>
              <input class="form-input" id="s-name" type="text" placeholder="Web Server 01" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="form-label">SSH Username</label>
              <input class="form-input" id="s-ssh" type="text" placeholder="ubuntu" autocomplete="off">
              <span class="form-hint">Linux user on this server used for SSH/SCP (e.g. ubuntu, root, deploy)</span>
            </div>
            <button class="btn btn-primary" id="btn-add-server" style="margin-top:8px">＋ Add Server</button>
            <div class="form-feedback" id="server-feedback"></div>
          </div>
          <div class="server-list-card">
            <div class="table-toolbar"><span class="table-toolbar-title">Registered Servers</span></div>
            <div id="server-list-body"><div class="empty-state"><div class="empty-icon">🖥</div><div class="empty-title">Loading...</div></div></div>
          </div>
        </div>
      </div><!-- /servers -->

      <!-- ═══ LOGS ═══ -->
      <div class="page" id="page-logs">
        <div class="section-header">
          <span class="section-title">Full Backup Log</span>
          <span class="section-line"></span>
          <span class="section-meta" id="log-total-label">—</span>
        </div>
        <div class="logs-toolbar">
          <input class="search-input" id="log-search" type="text" placeholder="Search IP or path...">
          <div class="filter-btns">
            <button class="filter-btn active" data-logfilter="">All</button>
            <button class="filter-btn running" data-logfilter="RUNNING">Running</button>
            <button class="filter-btn success" data-logfilter="SUCCESS">Success</button>
            <button class="filter-btn failed"  data-logfilter="FAILED">Failed</button>
          </div>
          <button class="btn btn-ghost btn-sm" id="btn-export-csv">⬇ Export CSV</button>
        </div>
        <div class="table-card">
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Source IP</th>
                  <th>Destination IP</th>
                  <th>Source Path</th>
                  <th>Status</th>
                  <th>Progress</th>
                  <th>Reason</th>
                  <th>Timestamp</th>
                </tr>
              </thead>
              <tbody id="log-tbody">
                <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Loading logs...</div></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="pagination" id="pagination"></div>
      </div><!-- /logs -->

      <!-- ═══ SETTINGS ═══ -->
      <div class="page" id="page-settings">
        <div class="section-header">
          <span class="section-title">Settings</span>
          <span class="section-line"></span>
        </div>

        <div class="settings-card">

          <!-- Two-column grid: left = config fields, right = change password -->
          <div class="settings-grid">

            <!-- LEFT: Auto Refresh + Dashboard IP -->
            <div>
              <div class="settings-section-title">⏱ Dashboard Preferences</div>
              <div class="form-group">
                <label class="form-label">Auto-Refresh Interval (seconds)</label>
                <input class="form-input" id="set-refresh" type="number" value="2" min="2" max="60">
                <span class="form-hint">Min: 2s &nbsp;|&nbsp; Max: 60s &nbsp;|&nbsp; Dashboard polls the server at this rate</span>
              </div>

              <div class="settings-divider"></div>

              <div class="settings-section-title">🌐 System Configuration</div>
              <div class="form-group">
                <label class="form-label">Dashboard Server IP</label>
                <input class="form-input" id="set-dashboard-ip" type="text" placeholder="192.168.1.10" autocomplete="off">
                <span class="form-hint">IP address of this machine on the local network</span>
              </div>
            </div>

            <!-- RIGHT: Change Password -->
            <div>
              <div class="settings-section-title">🔒 Change Password</div>
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input class="form-input" id="set-cur-pass" type="password" placeholder="••••••••">
              </div>
              <div class="form-group" style="margin-top:14px">
                <label class="form-label">New Password</label>
                <input class="form-input" id="set-new-pass" type="password" placeholder="••••••••">
              </div>
              <div class="form-group" style="margin-top:14px">
                <label class="form-label">Confirm New Password</label>
                <input class="form-input" id="set-cnf-pass" type="password" placeholder="••••••••">
              </div>
            </div>

          </div><!-- /settings-grid -->

          <div class="settings-divider"></div>

          <div style="display:flex; align-items:center; gap:12px;">
            <button class="btn btn-primary" id="btn-save-settings">💾 Save Settings</button>
            <div class="form-feedback" id="settings-feedback" style="margin-top:0; flex:1;"></div>
          </div>

        </div>
      </div><!-- /settings -->

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<!-- ── Job Detail Modal (opened on ℹ click) ── -->
<div id="job-modal-overlay">
  <div id="job-modal">
    <div id="job-modal-header">
      <span id="job-modal-title">Job Details</span>
      <button id="job-modal-close" title="Close">✕</button>
    </div>
    <div id="job-modal-body">
      <!-- Rows injected by JS -->
    </div>
    <div id="job-modal-footer">
      <button class="btn btn-ghost btn-sm" id="job-modal-close-btn">Close</button>
    </div>
  </div>
</div>

<script>
// ============================================================
//  BackupVault — Frontend JavaScript
// ============================================================

// ── Navigation ──────────────────────────────────────────────
const navItems   = document.querySelectorAll('.nav-item');
const pages      = document.querySelectorAll('.page');
const pageTitle  = document.getElementById('page-title');
const pageTitles = { dashboard:'Dashboard', servers:'Servers', logs:'Logs', settings:'Settings' };

function showPage(name) {
  navItems.forEach(n => n.classList.toggle('active', n.dataset.page === name));
  pages.forEach(p => p.classList.toggle('active', p.id === 'page-' + name));
  pageTitle.textContent = pageTitles[name] || name;
  if (name === 'servers')  loadServers();
  if (name === 'logs')     loadLogs();
  if (name === 'settings') loadSettings();
}
navItems.forEach(n => n.addEventListener('click', () => showPage(n.dataset.page)));

// ── Clock ────────────────────────────────────────────────────
function updateClock() { document.getElementById('clock').textContent = new Date().toLocaleTimeString(); }
setInterval(updateClock, 1000);
updateClock();

// ── Job Detail Modal Engine ───────────────────────────────────
// Opens a centered modal on ℹ icon CLICK (not hover).
// Uses event delegation — works for dynamically rendered rows.
(function initJobModal() {
  const overlay  = document.getElementById('job-modal-overlay');
  const modalEl  = document.getElementById('job-modal');
  const titleEl  = document.getElementById('job-modal-title');
  const bodyEl   = document.getElementById('job-modal-body');
  const closeBtn = document.getElementById('job-modal-close');
  const closeBtnFooter = document.getElementById('job-modal-close-btn');

  function openModal(icon) {
    const id     = icon.dataset.id     || '—';
    const src    = icon.dataset.src    || '—';
    const dst    = icon.dataset.dst    || '—';
    const ts     = icon.dataset.ts     || '—';
    const reason = icon.dataset.reason || 'No message';
    const status = icon.dataset.status || '';
    const pct    = icon.dataset.pct    || '';

    titleEl.textContent = `Job #${id} — Details`;

    // Build status badge inline for the modal
    const statusColor = { RUNNING: 'var(--yellow)', SUCCESS: 'var(--green)', FAILED: 'var(--red)' }[status] || 'var(--text-mid)';
    const statusStyle = status ? `style="color:${statusColor};font-weight:600;"` : '';

    bodyEl.innerHTML = `
      <div class="jm-row">
        <span class="jm-label">Job ID</span>
        <span class="jm-value mono">#${id}</span>
      </div>
      <div class="jm-row">
        <span class="jm-label">Status</span>
        <span class="jm-value" ${statusStyle}>${status || '—'}${pct ? ' &nbsp;·&nbsp; ' + pct + '%' : ''}</span>
      </div>
      <div class="jm-row">
        <span class="jm-label">Source Path</span>
        <span class="jm-value mono">${src}</span>
      </div>
      <div class="jm-row">
        <span class="jm-label">Destination Path</span>
        <span class="jm-value mono">${dst}</span>
      </div>
      <div class="jm-row">
        <span class="jm-label">Timestamp</span>
        <span class="jm-value dim">${ts}</span>
      </div>
      <div class="jm-row">
        <span class="jm-label">Message</span>
        <span class="jm-value dim">${reason}</span>
      </div>`;

    overlay.classList.add('open');
    document.body.style.overflow = 'hidden'; // prevent background scroll
  }

  function closeModal() {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Click on ℹ icon → open modal
  document.addEventListener('click', e => {
    const icon = e.target.closest('.info-icon');
    if (icon) {
      e.stopPropagation();
      openModal(icon);
    }
  });

  // Close on X button, footer Close button, overlay background click, Escape key
  closeBtn.addEventListener('click', closeModal);
  closeBtnFooter.addEventListener('click', closeModal);
  overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
})();

// ── Helpers ──────────────────────────────────────────────────
function statusBadge(status) {
  const cls   = status.toLowerCase();
  const pulse = status === 'RUNNING' ? '<span class="badge-pulse"></span>' : '';
  return `<span class="badge badge-${cls}">${pulse}${status}</span>`;
}

function progressCell(status, pct) {
  return `<div class="prog-wrap">
    <div class="prog-bar"><div class="prog-fill prog-fill-${status.toLowerCase()}" style="width:${pct}%"></div></div>
    <div class="prog-text">${pct}%</div>
  </div>`;
}

function infoIcon(job) {
  const src    = (job.source_path      || '—').replace(/"/g, '&quot;');
  const dst    = (job.destination_path || '—').replace(/"/g, '&quot;');
  const ts     = fmtDate(job.created_at).replace(/"/g, '&quot;');
  const reason = (job.reason           || 'No message').replace(/"/g, '&quot;');
  return `<span class="info-icon"
    data-id="${job.id}"
    data-src="${src}"
    data-dst="${dst}"
    data-ts="${ts}"
    data-reason="${reason}"
    data-status="${job.status || ''}"
    data-pct="${job.percentage ?? ''}"
    title="Click to view job details"
  >ℹ<span class="tooltip"></span></span>`;
}

function fmtDate(str) {
  if (!str) return '—';
  return new Date(str.replace(' ', 'T')).toLocaleString();
}

function showFeedback(el, msg, type) {
  el.textContent = msg;
  el.className = 'form-feedback ' + type;
  setTimeout(() => { el.className = 'form-feedback'; }, 5000);
}

// ── Dashboard ────────────────────────────────────────────────
let dashFilter  = '';
let autoRefresh = true;
let refreshTimer = null;
let refreshSecs  = 2;
let isFetching   = false;

// Build a dashboard table row — uses index+1 for serial number
function buildJobRow(job, index) {
  return `<tr>
    <td class="td-serial">${index + 1}</td>
    <td class="td-ip">${job.source_ip}</td>
    <td class="td-ip">${job.destination_ip}</td>
    <td>${statusBadge(job.status)}</td>
    <td>${progressCell(job.status, job.percentage)}</td>
    <td>${infoIcon(job)}</td>
  </tr>`;
}

function loadDashboard() {
  // Only fetch when dashboard page is actually visible
  if (!document.getElementById('page-dashboard').classList.contains('active')) return;
  if (isFetching) return;
  isFetching = true;
  fetch(`api_backups.php?limit=50${dashFilter ? '&status=' + dashFilter : ''}`, {
    credentials: 'same-origin'   // always send session cookie
  })
    .then(r => {
      if (r.status === 401 || r.redirected) {
        // Session expired — redirect to login
        window.location.href = 'login.php';
        return null;
      }
      return r.json();
    })
    .then(data => {
      if (!data) return;
      // Update stats
      document.getElementById('stat-total').textContent   = data.stats.total   || 0;
      document.getElementById('stat-running').textContent = data.stats.running  || 0;
      document.getElementById('stat-success').textContent = data.stats.success  || 0;
      document.getElementById('stat-failed').textContent  = data.stats.failed   || 0;

      // Running badge on sidebar
      const runCount = parseInt(data.stats.running) || 0;
      const badge    = document.getElementById('running-badge');
      badge.textContent    = runCount;
      badge.style.display  = runCount > 0 ? '' : 'none';

      // Populate table
      const tbody = document.getElementById('jobs-tbody');
      if (!data.jobs || data.jobs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state">
          <div class="empty-icon">📭</div>
          <div class="empty-title">No backup jobs found</div>
          <div class="empty-sub">Trigger a backup above to get started</div>
        </div></td></tr>`;
      } else {
        tbody.innerHTML = data.jobs.map((job, i) => buildJobRow(job, i)).join('');
      }
    })
    .catch(() => {
      document.getElementById('jobs-tbody').innerHTML =
        `<tr><td colspan="6"><div class="empty-state">
          <div class="empty-icon">⚠</div>
          <div class="empty-title">Failed to load jobs</div>
          <div class="empty-sub">Check server connection and refresh</div>
        </div></td></tr>`;
    })
    .finally(() => { isFetching = false; });
}

// Filter buttons
document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn[data-filter]').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    dashFilter = this.dataset.filter;
    loadDashboard();
  });
});

// Auto-refresh toggle — click to pause/resume
document.getElementById('refresh-toggle').addEventListener('click', function() {
  autoRefresh = !autoRefresh;
  if (!autoRefresh) clearInterval(refreshTimer);
  scheduleRefresh(); // handles label + timer for both on and off
});

function scheduleRefresh() {
  clearInterval(refreshTimer);
  const btn = document.getElementById('refresh-toggle');
  if (autoRefresh) {
    btn.textContent = `↻ ${refreshSecs}s`;
    btn.classList.add('on');
    refreshTimer = setInterval(() => {
      // Flash the button briefly on each tick so user can see it's live
      btn.style.opacity = '0.45';
      setTimeout(() => { btn.style.opacity = ''; }, 200);
      loadDashboard();
    }, refreshSecs * 1000);
  } else {
    btn.textContent = '↻ Off';
    btn.classList.remove('on');
  }
}

loadDashboard();
// Load settings first so refreshSecs is set from DB before scheduling
// This ensures the correct interval is used from the first tick
loadSettings().finally(() => {
  scheduleRefresh();
});

// Load servers on startup so dashboard dropdowns are populated immediately
loadServers();

// ── Trigger Backup ───────────────────────────────────────────
// Dropdowns are populated from the servers table.
// The selected value is the server IP; the php backend looks up
// ssh_username from the DB using that IP — no manual user entry.
document.getElementById('btn-start-backup').addEventListener('click', function() {
  const srcSel  = document.getElementById('f-src-ip');
  const dstSel  = document.getElementById('f-dst-ip');
  const srcIp   = srcSel.value;
  const dstIp   = dstSel.value;
  const srcPath = document.getElementById('f-src-path').value.trim();
  const dstPath = document.getElementById('f-dst-path').value.trim();
  const fb      = document.getElementById('backup-feedback');

  // Validate all fields
  if (!srcIp) {
    showFeedback(fb, '⚠ Please select a source server.', 'error'); return;
  }
  if (!dstIp) {
    showFeedback(fb, '⚠ Please select a destination server.', 'error'); return;
  }
  if (srcIp === dstIp) {
    showFeedback(fb, '✗ Source and destination cannot be the same server.', 'error'); return;
  }
  if (!srcPath) {
    showFeedback(fb, '⚠ Source path is required.', 'error'); return;
  }
  if (!dstPath) {
    showFeedback(fb, '⚠ Destination path is required.', 'error'); return;
  }

  this.disabled    = true;
  this.textContent = '⏳ Starting...';

  fetch('start_backup.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `source_ip=${encodeURIComponent(srcIp)}`
        + `&destination_ip=${encodeURIComponent(dstIp)}`
        + `&source_path=${encodeURIComponent(srcPath)}`
        + `&destination_path=${encodeURIComponent(dstPath)}`,
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showFeedback(fb, `✓ Backup started! ${data.ssh_username}@${srcIp}:${srcPath} → ${data.dest_username}@${dstIp}:${dstPath}`, 'success');
      // Reset form
      srcSel.value = '';
      dstSel.value = '';
      document.getElementById('f-src-path').value = '';
      document.getElementById('f-dst-path').value = '';
      loadDashboard();
    } else {
      showFeedback(fb, '✗ ' + (data.error || 'Failed to start backup'), 'error');
    }
  })
  .catch(() => showFeedback(fb, '✗ Network error. Check server.', 'error'))
  .finally(() => { this.disabled = false; this.textContent = '▶ Start Backup'; });
});

// ── Servers ──────────────────────────────────────────────────

// Populates the two dropdowns on the dashboard with server options from DB
function populateServerDropdowns(servers) {
  const srcSel = document.getElementById('f-src-ip');
  const dstSel = document.getElementById('f-dst-ip');

  // Save current selection so we can restore it after repopulating
  const prevSrc = srcSel.value;
  const prevDst = dstSel.value;

  const blankOpt = '<option value="">— select server —</option>';
  const options  = servers.map(s =>
    `<option value="${s.server_ip}" data-ssh="${s.ssh_username}">${s.server_name} (${s.server_ip})</option>`
  ).join('');

  srcSel.innerHTML = blankOpt + options;
  dstSel.innerHTML = blankOpt + options;

  // Restore previous selection if still valid
  if (prevSrc) srcSel.value = prevSrc;
  if (prevDst) dstSel.value = prevDst;
}

function loadServers() {
  fetch('api_servers.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      const body = document.getElementById('server-list-body');
      document.getElementById('server-count').textContent = (data.servers?.length || 0) + ' servers';

      // Always repopulate dropdowns whenever server list changes
      populateServerDropdowns(data.servers || []);

      if (!data.servers || data.servers.length === 0) {
        body.innerHTML = `<div class="empty-state">
          <div class="empty-icon">🖥</div>
          <div class="empty-title">No servers registered</div>
          <div class="empty-sub">Add your first authorized server</div>
        </div>`;
        return;
      }

      body.innerHTML = data.servers.map(s => `
        <div class="server-row">
          <div>
            <div class="server-ip">${s.server_ip}</div>
            <div class="server-name">${s.server_name}
              <span style="font-family:var(--font-mono);font-size:10px;color:var(--accent2);margin-left:6px;">
                ssh: ${s.ssh_username}
              </span>
            </div>
          </div>
          <div class="server-date">${fmtDate(s.created_at)}</div>
          <button class="server-del-btn" data-id="${s.id}" title="Remove">✕</button>
        </div>`).join('');

      body.querySelectorAll('.server-del-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          if (!confirm('Remove this server? Backups from this IP will be rejected.')) return;
          fetch('api_servers.php', {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: parseInt(this.dataset.id) }),
          }).then(r => r.json()).then(d => { if (d.success) loadServers(); else alert(d.error); });
        });
      });
    });
}

document.getElementById('btn-add-server').addEventListener('click', function() {
  const ip   = document.getElementById('s-ip').value.trim();
  const name = document.getElementById('s-name').value.trim();
  const ssh  = document.getElementById('s-ssh').value.trim();
  const fb   = document.getElementById('server-feedback');

  if (!ip || !name || !ssh) { showFeedback(fb, '⚠ All three fields are required.', 'error'); return; }
  if (!/^[a-zA-Z0-9_\-]{1,64}$/.test(ssh)) {
    showFeedback(fb, '✗ SSH username: only letters, numbers, dash, underscore allowed.', 'error'); return;
  }

  fetch('api_servers.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ server_ip: ip, server_name: name, ssh_username: ssh }),
  }).then(r => r.json()).then(d => {
    if (d.success) {
      showFeedback(fb, '✓ Server added', 'success');
      document.getElementById('s-ip').value   = '';
      document.getElementById('s-name').value = '';
      document.getElementById('s-ssh').value  = '';
      loadServers();
    } else {
      showFeedback(fb, '✗ ' + (d.error || 'Failed'), 'error');
    }
  });
});

// ── Logs ─────────────────────────────────────────────────────
let logPage = 1, logFilter = '', logSearch = '', logDebounce;

function loadLogs() {
  const url = `api_logs.php?page=${logPage}&limit=20&status=${logFilter}&search=${encodeURIComponent(logSearch)}`;
  fetch(url)
    .then(r => r.json())
    .then(data => {
      document.getElementById('log-total-label').textContent = data.total + ' records';
      const tbody = document.getElementById('log-tbody');

      if (!data.logs || data.logs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">
          <div class="empty-icon">📭</div><div class="empty-title">No logs found</div>
        </div></td></tr>`;
        return;
      }

      // Serial uses page-aware offset: page 1 → 1–20, page 2 → 21–40, etc.
      tbody.innerHTML = data.logs.map((job, i) => {
        const serial = (logPage - 1) * 20 + i + 1;
        return `<tr>
          <td class="td-serial">${serial}</td>
          <td class="td-ip">${job.source_ip}</td>
          <td class="td-ip">${job.destination_ip}</td>
          <td class="td-path" title="${job.source_path}">${job.source_path}</td>
          <td>${statusBadge(job.status)}</td>
          <td>${progressCell(job.status, job.percentage)}</td>
          <td style="font-size:11px;color:var(--text-mid);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
              title="${(job.reason||'').replace(/"/g,'&quot;')}">${job.reason || '—'}</td>
          <td class="td-time">${fmtDate(job.created_at)}</td>
        </tr>`;
      }).join('');

      // Pagination
      const pg  = document.getElementById('pagination');
      const tot = data.total_pages;
      if (tot <= 1) { pg.innerHTML = ''; return; }
      let html = `<button class="page-btn" onclick="goPage(${logPage-1})" ${logPage<=1?'disabled':''}>‹</button>`;
      for (let i = 1; i <= tot; i++) {
        if (i === 1 || i === tot || Math.abs(i - logPage) <= 2) {
          html += `<button class="page-btn ${i===logPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
        } else if (Math.abs(i - logPage) === 3) {
          html += `<span style="padding:0 4px;color:var(--text-dim)">…</span>`;
        }
      }
      html += `<button class="page-btn" onclick="goPage(${logPage+1})" ${logPage>=tot?'disabled':''}>›</button>`;
      pg.innerHTML = html;
    });
}

function goPage(p) { logPage = p; loadLogs(); }

document.querySelectorAll('.filter-btn[data-logfilter]').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn[data-logfilter]').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    logFilter = this.dataset.logfilter;
    logPage   = 1;
    loadLogs();
  });
});

document.getElementById('log-search').addEventListener('input', function() {
  clearTimeout(logDebounce);
  logDebounce = setTimeout(() => { logSearch = this.value.trim(); logPage = 1; loadLogs(); }, 350);
});

// ── Export CSV ───────────────────────────────────────────────
document.getElementById('btn-export-csv').addEventListener('click', function() {
  const params = new URLSearchParams({ status: logFilter, search: logSearch, limit: 9999 });
  const a = document.createElement('a');
  a.href     = 'export_csv.php?' + params.toString();
  a.download = 'backup_logs_' + new Date().toISOString().slice(0,10) + '.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});

// ── Settings ─────────────────────────────────────────────────

// Load current settings from DB and populate fields
function loadSettings() {
  return fetch('api_settings.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      const s = data.settings || {};
      // Apply auto-refresh interval from DB — updates the live timer
      if (s.auto_refresh_interval) {
        const saved = parseInt(s.auto_refresh_interval);
        if (saved >= 2 && saved <= 60) {
          refreshSecs = saved;
        }
      }
      // Populate the settings input field with DB value
      document.getElementById('set-refresh').value = refreshSecs;
      if (s.dashboard_ip) document.getElementById('set-dashboard-ip').value = s.dashboard_ip;
    })
    .catch(() => {/* silently fail — fields keep their default values */});
}

document.getElementById('btn-save-settings').addEventListener('click', function() {
  const fb          = document.getElementById('settings-feedback');
  const interval    = parseInt(document.getElementById('set-refresh').value) || 2;
  const dashboardIp = document.getElementById('set-dashboard-ip').value.trim();

  // Validate Dashboard IP if provided
  if (dashboardIp && !/^(\d{1,3}\.){3}\d{1,3}$/.test(dashboardIp)) {
    showFeedback(fb, '✗ Dashboard IP must be a valid IPv4 address', 'error'); return;
  }

  // Update refresh interval locally and reschedule
  refreshSecs = Math.max(2, Math.min(60, interval));
  scheduleRefresh();

  // Build payload
  const payload = { auto_refresh_interval: refreshSecs };
  if (dashboardIp) payload.dashboard_ip = dashboardIp;

  // Handle password change if fields are filled
  const cur = document.getElementById('set-cur-pass').value.trim();
  const nw  = document.getElementById('set-new-pass').value.trim();
  const cnf = document.getElementById('set-cnf-pass').value.trim();

  const saveSystemSettings = () =>
    fetch('api_settings.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    }).then(r => r.json());

  if (cur || nw || cnf) {
    if (!cur || !nw || !cnf) { showFeedback(fb, '⚠ Fill all three password fields', 'error'); return; }
    if (nw !== cnf)           { showFeedback(fb, '✗ New passwords do not match', 'error');     return; }
    if (nw.length < 6)        { showFeedback(fb, '✗ Minimum 6 characters required', 'error'); return; }

    saveSystemSettings()
      .then(d => {
        if (!d.success) { showFeedback(fb, '✗ ' + (d.error || 'Settings save failed'), 'error'); return Promise.reject(); }
        return fetch('change_password.php', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ current_password: cur, new_password: nw, confirm_password: cnf }),
        });
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showFeedback(fb, '✓ Settings saved & password updated', 'success');
          ['set-cur-pass','set-new-pass','set-cnf-pass'].forEach(id => document.getElementById(id).value = '');
        } else {
          showFeedback(fb, '✗ ' + (d.error || 'Password change failed'), 'error');
        }
      })
      .catch(() => {/* handled above */});
  } else {
    saveSystemSettings()
      .then(d => {
        if (d.success) {
          showFeedback(fb, `✓ Settings saved (refresh: ${refreshSecs}s)`, 'success');
        } else {
          showFeedback(fb, '✗ ' + (d.error || 'Save failed'), 'error');
        }
      })
      .catch(() => showFeedback(fb, '✗ Network error', 'error'));
  }
});

// ── Dark / Light Theme Toggle ────────────────────────────────
function applyTheme(mode) {
  document.body.classList.toggle('light-mode', mode === 'light');
  document.getElementById('theme-icon').textContent = mode === 'light' ? '🌙' : '☀';
  localStorage.setItem('bv_theme', mode);
}

// Restore saved preference on page load
applyTheme(localStorage.getItem('bv_theme') || 'dark');

document.getElementById('theme-toggle').addEventListener('click', () => {
  applyTheme(document.body.classList.contains('light-mode') ? 'dark' : 'light');
});
</script>
</body>
</html>
