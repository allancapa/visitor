/* ================================================
   Visitor Management System - Shared JavaScript
   ================================================ */

// API base URL - UPDATE THIS to match your PHP server
const API_BASE = '../api';

// ---- Auth Check ----
function checkAuth() {
  const user = sessionStorage.getItem('vms_user');
  if (!user) {
    window.location.href = 'login.html';
    return null;
  }
  return JSON.parse(user);
}

// ---- Sidebar Toggle ----
function initSidebar() {
  const toggleBtn = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    });
  }
}

// ---- Logout ----
function initLogout() {
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function (e) {
      e.preventDefault();
      handleLogout();
    });
  }
}

async function handleLogout() {
  try {
    await fetch(API_BASE + '/logout.php', { method: 'POST' });
  } catch (err) {
    // Ignore errors, still clear session
  }
  sessionStorage.removeItem('vms_user');
  window.location.href = 'login.html';
}

// ---- Display User Info ----
function displayUserInfo() {
  const user = checkAuth();
  if (!user) return;

  const userNameEl = document.getElementById('userName');
  if (userNameEl) {
    userNameEl.textContent = user.full_name || user.username;
  }
}

// ---- Floating Alert ----
function showAlert(message, type = 'success') {
  // Remove existing alerts
  const existing = document.querySelectorAll('.alert-float');
  existing.forEach(el => el.remove());

  const alert = document.createElement('div');
  alert.className = `alert alert-${type} alert-dismissible fade show alert-float`;
  alert.setAttribute('role', 'alert');
  alert.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  document.body.appendChild(alert);

  // Auto-remove after 4 seconds
  setTimeout(() => {
    if (alert.parentNode) {
      alert.classList.remove('show');
      setTimeout(() => alert.remove(), 150);
    }
  }, 4000);
}

// ---- Loading Overlay ----
function showLoading() {
  let overlay = document.getElementById('loadingOverlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    `;
    document.body.appendChild(overlay);
  }
  overlay.style.display = 'flex';
}

function hideLoading() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.style.display = 'none';
  }
}

// ---- API Fetch Wrapper ----
async function apiFetch(endpoint, options = {}) {
  const url = API_BASE + endpoint;
  const config = {
    headers: {
      'Content-Type': 'application/json',
    },
    ...options,
  };

  if (config.body && typeof config.body === 'object') {
    config.body = JSON.stringify(config.body);
  }

  const response = await fetch(url, config);
  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.message || 'Something went wrong');
  }

  return data;
}

// ---- Date Formatting ----
function formatDate(dateString) {
  if (!dateString) return '---';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

function formatDateTime(dateString) {
  if (!dateString) return '---';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

// ---- Get URL Parameter ----
function getUrlParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

// ---- Confirm Dialog ----
function confirmAction(message) {
  return confirm(message);
}

// ---- Init Common Elements ----
document.addEventListener('DOMContentLoaded', function () {
  initSidebar();
  initLogout();
});
