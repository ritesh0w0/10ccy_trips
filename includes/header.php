<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>10ccy Trips</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="#">
    <img src="/assets/logo.png" height="40" class="me-2" alt="10ccy Trips Logo">
    <strong>10ccy Trips</strong>
  </a>
  <div class="ms-auto">
    <button onclick="toggleDarkMode()" class="btn btn-sm btn-warning">🌗 Toggle Mode</button>
  </div>
</nav>

<script>
function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
}

window.onload = function () {
    if (localStorage.getItem("darkMode") === "true") {
        document.body.classList.add("dark-mode");
    }
};
</script>
