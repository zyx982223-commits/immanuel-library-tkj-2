<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$user = $_SESSION['user'] ?? [];
$role = $user['role'] ?? null;
?>

<header class="app-topbar">
  <div class="page-title">
    <h1><?= $pageTitle ?></h1>
    <p><?= $pageSubtitle ?></p>
  </div>
  <div class="topbar-user">
    <span class="avatar">BS</span>
    <div>
      <?= $user['name'] ?? 'Guest' ?>  
      <br>
      <span class="badge badge-member" style="margin-top:2px;">
        <?= $role === 'member' ? 'Member' : ($role === 'admin' ? 'Admin' : 'Guest') ?>
      </span>
    </div>
  </div>
</header>