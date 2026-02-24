/* ================================================
   City Councilor Office Visitor E-Logbook System
   Shared JavaScript
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

function getUser() {
  const user = sessionStorage.getItem('vms_user');
  return user ? JSON.parse(user) : null;
}

function isAdmin() {
  const user = getUser();
  return user && user.role === 'admin';
}

function isStaff() {
  const user = getUser();
  return user && user.role === 'staff';
}

// ---- Sidebar Initialization ----
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

  // Show/hide menu items based on role
  applyRoleVisibility();
}

// ---- Role-based Sidebar Visibility ----
function applyRoleVisibility() {
  const user = getUser();
  if (!user) return;

  // Elements with data-role="admin" only visible to admin
  document.querySelectorAll('[data-role="admin"]').forEach(function (el) {
    el.style.display = user.role === 'admin' ? '' : 'none';
  });

  // Elements with data-role="staff" only visible to staff
  document.querySelectorAll('[data-role="staff"]').forEach(function (el) {
    el.style.display = user.role === 'staff' ? '' : 'none';
  });

  // Update role badge
  var roleBadge = document.getElementById('userRoleBadge');
  if (roleBadge) {
    roleBadge.textContent = user.role === 'admin' ? 'Admin' : 'Staff';
    roleBadge.className = user.role === 'admin' 
      ? 'badge bg-danger ms-2' 
      : 'badge bg-info ms-2';
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

function handleLogout() {
  Swal.fire({
    title: 'Logout',
    text: 'Are you sure you want to logout?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Yes, logout'
  }).then(function (result) {
    if (result.isConfirmed) {
      fetch(API_BASE + '/logout.php', { method: 'POST' }).catch(function () {});
      sessionStorage.removeItem('vms_user');
      window.location.href = 'login.html';
    }
  });
}

// ---- Display User Info ----
function displayUserInfo() {
  const user = checkAuth();
  if (!user) return;

  const userNameEl = document.getElementById('userName');
  if (userNameEl) {
    userNameEl.textContent = user.fullname || user.username;
  }
}

// ---- SweetAlert2 Wrappers ----
function showSuccess(message, title) {
  return Swal.fire({
    icon: 'success',
    title: title || 'Success',
    text: message,
    confirmButtonColor: '#3b82f6',
    timer: 2000,
    timerProgressBar: true
  });
}

function showError(message, title) {
  return Swal.fire({
    icon: 'error',
    title: title || 'Error',
    text: message,
    confirmButtonColor: '#3b82f6'
  });
}

function showWarning(message, title) {
  return Swal.fire({
    icon: 'warning',
    title: title || 'Warning',
    text: message,
    confirmButtonColor: '#3b82f6'
  });
}

function showConfirm(message, title) {
  return Swal.fire({
    title: title || 'Are you sure?',
    text: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Yes, proceed',
    cancelButtonText: 'Cancel'
  });
}

function showToast(message, icon) {
  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: function (toast) {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });
  Toast.fire({ icon: icon || 'success', title: message });
}

// ---- Loading Overlay ----
function showLoading(message) {
  Swal.fire({
    title: message || 'Loading...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: function () {
      Swal.showLoading();
    }
  });
}

function hideLoading() {
  Swal.close();
}

// ---- API Fetch Wrapper ----
async function apiFetch(endpoint, options) {
  options = options || {};
  var url = API_BASE + endpoint;
  var config = {
    headers: { 'Content-Type': 'application/json' },
    ...options
  };

  if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
    config.body = JSON.stringify(config.body);
  }

  var response = await fetch(url, config);
  var data = await response.json();

  if (!response.ok) {
    throw new Error(data.message || 'Something went wrong');
  }

  return data;
}

// ---- Date / Time Formatting ----
function formatDate(dateString) {
  if (!dateString) return '---';
  var date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric'
  });
}

function formatTime(timeString) {
  if (!timeString) return '---';
  var parts = timeString.split(':');
  var h = parseInt(parts[0]);
  var m = parts[1];
  var ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return h + ':' + m + ' ' + ampm;
}

function formatDateTime(dateStr, timeStr) {
  return formatDate(dateStr) + ' ' + formatTime(timeStr);
}

function getTodayDate() {
  var d = new Date();
  var month = String(d.getMonth() + 1).padStart(2, '0');
  var day = String(d.getDate()).padStart(2, '0');
  return d.getFullYear() + '-' + month + '-' + day;
}

function getCurrentTime() {
  var d = new Date();
  var h = String(d.getHours()).padStart(2, '0');
  var m = String(d.getMinutes()).padStart(2, '0');
  return h + ':' + m;
}

// ---- URL Parameters ----
function getUrlParam(param) {
  var urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

// ---- Escape HTML ----
function escapeHtml(text) {
  if (!text) return '';
  var div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// ---- Excel Export using SheetJS ----
function exportTableToExcel(tableId, filename) {
  filename = filename || 'export';
  var table = document.getElementById(tableId);
  if (!table) {
    showError('Table not found for export.');
    return;
  }
  var wb = XLSX.utils.table_to_book(table, { sheet: 'Sheet1' });
  XLSX.writeFile(wb, filename + '.xlsx');
}

function exportDataToExcel(dataArray, headers, filename) {
  filename = filename || 'export';
  var ws = XLSX.utils.aoa_to_sheet([headers].concat(dataArray));
  var wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
  XLSX.writeFile(wb, filename + '.xlsx');
}

// ---- Init Common Elements ----
document.addEventListener('DOMContentLoaded', function () {
  initSidebar();
  initLogout();
});
