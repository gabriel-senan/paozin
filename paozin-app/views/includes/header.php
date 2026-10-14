<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Pãozin — O Pão Quentinho do seu Bairro') ?></title>
    <!-- Fontes Oficiais do Pãozin -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <!-- Favicon e CSS Design System Oficial -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🥖</text></svg>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- SVG Global com o Símbolo Pino-Pão Oficial -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="pin-pao" viewBox="0 0 100 116">
    <path d="M50 4c19.3 0 35 15.4 35 34.4 0 22.6-19.9 44.4-31.6 60.3-1.6 2.2-5.2 2.2-6.8 0C34.9 82.8 15 61 15 38.4 15 19.4 30.7 4 50 4z" fill="#E89B3C" stroke="#5C3410" stroke-width="5"/>
    <path d="M36 30c2.6-8.6 10.8-14.6 20-14.6 4.6 0 8.9 1.4 12.4 3.9-9.6-.6-18.6 4.2-23.6 12.4-2.2 3.6-3.4 7.6-3.6 11.7-2.9-4-4.3-8.9-5.2-13.4z" fill="#F5C46B" opacity=".85"/>
    <path d="M30 46c6.2 1.4 11.2 5.8 13.4 11.7" stroke="#B36B1F" stroke-width="4" stroke-linecap="round" fill="none" opacity=".55"/>
    <path d="M57 34c5.8 1.2 10.6 5 13.4 10.2" stroke="#B36B1F" stroke-width="4" stroke-linecap="round" fill="none" opacity=".55"/>
    <path d="M41 60c5.4 1.4 9.7 5.2 12 10.2" stroke="#B36B1F" stroke-width="4" stroke-linecap="round" fill="none" opacity=".55"/>
    <circle cx="50" cy="96" r="4" fill="#FFF6E9" opacity=".9"/>
  </symbol>
</svg>

<header class="topbar">
    <a href="/" class="brand-lockup">
        <svg viewBox="0 0 100 116" style="width: 32px; height: 38px;"><use href="#pin-pao"/></svg>
        <span class="wordmark">Pão<span class="acento">zin</span></span>
    </a>

    <nav class="topbar-nav">
        <a href="/#como-funciona" class="nav-link" style="color: var(--carvao); font-size: 14px;">Como Funciona</a>
        <a href="/#mapa" class="nav-link" style="color: var(--carvao); font-size: 14px;">Mapa</a>
        <a href="/#padarias" class="nav-link" style="color: var(--carvao); font-size: 14px;">Padarias</a>
        <a href="/#padaria-b2b" class="nav-link" style="color: var(--carvao); font-size: 14px;">Para Padarias</a>

        <?php if (!empty($_SESSION['user_type']) && $_SESSION['user_type'] === 'padaria'): ?>
            <a href="/painel" class="btn-3d btn-fornada" style="padding: 10px 16px; font-size: 13px;">
                Painel (<?= htmlspecialchars($_SESSION['padaria_nome'] ?? '') ?>)
            </a>
            <a href="/logout" class="pill" style="font-size: 12px;">Sair</a>
        <?php elseif (!empty($_SESSION['user_type']) && $_SESSION['user_type'] === 'cliente'): ?>
            <a href="/minha-conta" class="btn-3d btn-azul" style="padding: 10px 16px; font-size: 13px;">
                Minha Conta
            </a>
            <a href="/logout" class="pill" style="font-size: 12px;">Sair</a>
        <?php else: ?>
            <button onclick="openLoginModal('padaria')" class="btn-3d btn-crosta" style="padding: 10px 18px; font-size: 13px;">
                Entrar
            </button>
        <?php endif; ?>
    </nav>
</header>
<main>
