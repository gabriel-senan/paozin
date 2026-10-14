<?php
$tituloPagina = "Minha Conta — Pãozin";

// Leaflet carregado no <head> — sem SRI para evitar bloqueio por hash mismatch
$extraHead = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
';

require_once __DIR__ . '/includes/header.php';

// Serializa padarias para o JS (apenas as que têm coordenadas)
$padariasMapa = array_filter($todasPadarias ?? [], function($p) {
    return !empty($p['latitude']) && !empty($p['longitude']);
});
$padariasMapa = array_values($padariasMapa);
$padariasMapa = array_map(function($p) use ($idsSeguidas) {
    return [
        'id'      => (int)$p['id'],
        'nome'    => $p['nome'],
        'endereco'=> $p['endereco'],
        'lat'     => (float)$p['latitude'],
        'lng'     => (float)$p['longitude'],
        'nota'    => (float)$p['nota_media'],
        'preco'   => (float)$p['preco_pao_quilo'],
        'segue'   => in_array((int)$p['id'], $idsSeguidas ?? []),
    ];
}, $padariasMapa);
?>

<div class="wrap" style="padding: 40px 24px; min-height: 80vh;">
    <div style="max-width: 760px; margin: 0 auto;">

        <!-- Cabeçalho -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; gap: 16px; flex-wrap: wrap;">
            <div>
                <span class="pill" style="margin-bottom: 8px; display: inline-block;">Área do Cliente</span>
                <h1 style="font-size: 30px; line-height: 1.2;">
                    Olá, <?= htmlspecialchars($_SESSION['cliente_nome'] ?? 'Vizinho') ?>!
                </h1>
                <p style="color: var(--cinza); font-size: 14px; margin-top: 4px;">
                    WhatsApp: <strong><?= htmlspecialchars($_SESSION['cliente_whatsapp'] ?? '') ?></strong>
                </p>
            </div>
            <a href="/logout" class="btn-3d btn-ghost" style="padding: 10px 20px; font-size: 13px; white-space: nowrap;">
                Sair da conta
            </a>
        </div>

        <!-- Boas-vindas (primeiro acesso) -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'bem_vindo'): ?>
            <div style="background: linear-gradient(135deg,#FFFBF2,#FFF3D6); border: 2px solid var(--crosta-clara); border-radius: var(--r-lg); padding: 20px 24px; margin-bottom: 28px; display: flex; align-items: center; gap: 16px;">
                <svg viewBox="0 0 100 116" style="width: 36px; height: 42px; flex-shrink: 0;"><use href="#pin-pao"/></svg>
                <div>
                    <p style="font-weight: 800; font-size: 16px; color: var(--crosta-escura); margin-bottom: 4px;">Conta criada com sucesso!</p>
                    <p style="font-size: 13.5px; color: var(--carvao); line-height: 1.5;">Explore as padarias no mapa e comece a receber avisos de pão quentinho.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Alerta removido -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'alerta_removido'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 20px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>Alerta cancelado. Você não receberá mais notificações desta padaria.</div>
            </div>
        <?php endif; ?>

        <!-- Card: Padarias seguidas -->
        <div class="card-paozin" style="margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 20px;">Suas padarias</h2>
                <span class="badge <?= count($padariasSeguidas) > 0 ? 'quente' : '' ?>"
                      style="<?= count($padariasSeguidas) === 0 ? 'background: var(--linha); color: var(--cinza);' : '' ?>">
                    <?= count($padariasSeguidas) ?> ativa<?= count($padariasSeguidas) !== 1 ? 's' : '' ?>
                </span>
            </div>

            <?php if (!empty($padariasSeguidas)): ?>
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <?php foreach ($padariasSeguidas as $pad): ?>
                        <div style="background: var(--miolo); border: 1.5px solid var(--linha); border-radius: var(--r-md); padding: 16px 18px; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
                            <div>
                                <h3 style="font-size: 17px; margin-bottom: 3px;"><?= htmlspecialchars($pad['nome']) ?></h3>
                                <p style="font-size: 13px; color: var(--cinza); margin-bottom: 6px;"><?= htmlspecialchars($pad['endereco']) ?></p>
                                <span class="pill" style="font-size: 12px; background: #fff;">
                                    R$ <?= number_format((float)$pad['preco_pao_quilo'], 2, ',', '.') ?>/kg &nbsp;&middot;&nbsp; Nota <?= number_format((float)$pad['nota_media'], 1, ',', '.') ?>
                                </span>
                            </div>
                            <form action="/cancelar-alerta" method="POST"
                                  onsubmit="return confirm('Parar de receber avisos desta padaria?');">
                                <input type="hidden" name="padaria_id" value="<?= (int)$pad['id'] ?>">
                                <button type="submit" class="btn-3d btn-ghost"
                                        style="color: var(--brasa); border-color: #F8BDBD; padding: 9px 15px; font-size: 13px;">
                                    Parar avisos
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 28px 16px;">
                    <svg viewBox="0 0 100 116" style="width: 40px; height: 46px; margin-bottom: 10px; opacity: 0.4;"><use href="#pin-pao"/></svg>
                    <p style="color: var(--cinza); margin-bottom: 16px; font-size: 14.5px;">Você ainda não segue nenhuma padaria.</p>
                    <button type="button" onclick="expandirMapa()" class="btn-3d btn-fornada" style="font-size: 14px;">
                        Explorar padarias no mapa
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- =================== CARD MAPA =================== -->
        <div class="card-paozin" id="card-mapa" style="padding: 0; overflow: hidden;">

            <!-- Barra de controle -->
            <div style="padding: 18px 22px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid var(--linha); flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 style="font-size: 20px; margin-bottom: 2px;">Padarias no bairro</h2>
                    <p style="font-size: 13px; color: var(--cinza);"><?= count($padariasMapa) ?> padaria<?= count($padariasMapa) !== 1 ? 's' : '' ?> no mapa</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="btn-minha-loc" onclick="centralizarNaMinhaLocalizacao()"
                            class="btn-3d btn-azul" style="font-size: 13px; padding: 9px 16px;">
                        Minha localização
                    </button>
                    <button type="button" id="btn-toggle-mapa" onclick="toggleMapa()"
                            class="btn-3d btn-ghost" style="font-size: 13px; padding: 9px 16px;">
                        Expandir mapa
                    </button>
                </div>
            </div>

            <!-- Preview (clicável) -->
            <div id="mapa-preview" onclick="expandirMapa()"
                 style="cursor: pointer; padding: 28px 22px; background: var(--miolo); display: flex; align-items: center; gap: 18px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--crosta)" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px; flex-shrink: 0;">
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7z"/>
                </svg>
                <div>
                    <p style="font-weight: 700; color: var(--carvao); margin-bottom: 2px;">Ver padarias próximas</p>
                    <p style="font-size: 13px; color: var(--cinza);">Clique para abrir o mapa e explorar as padarias conveniadas.</p>
                </div>
            </div>

            <!-- Mapa Leaflet (oculto inicialmente) -->
            <div id="mapa-expandido" style="display: none;">
                <div id="paozin-map" style="width: 100%; height: 440px;"></div>

                <!-- Legenda -->
                <div style="padding: 16px 22px; border-top: 1.5px solid var(--linha); display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
                    <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; color: var(--cinza);">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:var(--crosta); border:2px solid var(--crosta-escura); display:inline-block;"></span>
                            Padaria conveniada
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:var(--fornada); border:2px solid var(--fornada-escura); display:inline-block;"></span>
                            Você segue
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span style="width:12px; height:12px; border-radius:50%; background:var(--azul); border:2px solid var(--azul-escuro); display:inline-block;"></span>
                            Sua localização
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <?php if (!empty($padariasSeguidas)): ?>
            <div style="text-align: center; margin-top: 28px;">
                <button type="button" onclick="expandirMapa()" class="btn-3d btn-crosta">
                    Explorar mais padarias no bairro
                </button>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
// ── Dados vindos do PHP ────────────────────────────────────────
var PADARIAS = <?= json_encode($padariasMapa, JSON_UNESCAPED_UNICODE) ?>;

// ── Estado ─────────────────────────────────────────────────────
var leafletMap = null;
var mapaAberto = false;
var userMarker = null;

// ── Ícones SVG ─────────────────────────────────────────────────
function criarIcone(cor, corBorda, tamanho) {
    var h  = Math.round(tamanho * 1.3);
    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 52" width="' + tamanho + '" height="' + h + '">'
        + '<path d="M20 2C11.2 2 4 9.2 4 18c0 11.7 16 32 16 32s16-20.3 16-32C36 9.2 28.8 2 20 2z"'
        + ' fill="' + cor + '" stroke="' + corBorda + '" stroke-width="2.5"/>'
        + '<circle cx="20" cy="18" r="7" fill="white" opacity="0.9"/>'
        + '</svg>';
    return L.divIcon({
        html:       svg,
        className:  '',
        iconSize:   [tamanho, h],
        iconAnchor: [tamanho / 2, h],
        popupAnchor:[0, -(h - 4)]
    });
}

var iconeNormal  = null;
var iconeSeguida = null;
var iconeUser    = null;

function inicializarIcones() {
    iconeNormal  = criarIcone('#E89B3C', '#B36B1F', 36);
    iconeSeguida = criarIcone('#58CC02', '#46A302', 42);
    iconeUser    = L.divIcon({
        html: '<div style="width:18px;height:18px;border-radius:50%;background:#1CB0F6;border:3px solid #fff;box-shadow:0 0 0 4px rgba(28,176,246,0.3);"></div>',
        className:  '',
        iconSize:   [18, 18],
        iconAnchor: [9, 9]
    });
}

// ── Inicializa Leaflet ──────────────────────────────────────────
function inicializarMapa(lat, lng) {
    if (leafletMap) {
        leafletMap.setView([lat, lng], 15, { animate: true });
        if (userMarker) userMarker.setLatLng([lat, lng]);
        return;
    }

    inicializarIcones();

    leafletMap = L.map('paozin-map', {
        center:           [lat, lng],
        zoom:             15,
        zoomControl:      true,
        attributionControl: true
    });

    // Esri Light Gray Canvas — gratuito, sem API key, visual Apple Maps
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
        maxZoom: 16
    }).addTo(leafletMap);

    // Marcador do usuário
    userMarker = L.marker([lat, lng], { icon: iconeUser, zIndexOffset: 2000 })
        .addTo(leafletMap)
        .bindPopup('<strong style="font-family:Inter,sans-serif;">Você está aqui</strong>', { closeButton: false });

    // Pins das padarias
    PADARIAS.forEach(function(p) {
        var icone  = p.segue ? iconeSeguida : iconeNormal;
        var badge  = p.segue
            ? '<br><span style="display:inline-block;margin-top:6px;padding:2px 10px;background:#58CC02;color:#fff;border-radius:20px;font-size:11px;font-weight:700;">Você segue</span>'
            : '';
        var popup  = '<div style="font-family:Inter,sans-serif;min-width:190px;line-height:1.4;">'
            + '<strong style="font-size:14px;display:block;margin-bottom:3px;">' + p.nome + '</strong>'
            + '<span style="font-size:12px;color:#8A7C6B;display:block;margin-bottom:5px;">' + p.endereco + '</span>'
            + '<span style="font-size:12.5px;">R$ ' + p.preco.toFixed(2).replace('.', ',') + '/kg &middot; Nota ' + p.nota.toFixed(1).replace('.', ',') + '</span>'
            + badge
            + '</div>';

        L.marker([p.lat, p.lng], { icon: icone })
            .addTo(leafletMap)
            .bindPopup(popup, { maxWidth: 250 });
    });

    // Ajusta bounds para mostrar usuário + todas as padarias
    var pontos = [[lat, lng]];
    PADARIAS.forEach(function(p) { pontos.push([p.lat, p.lng]); });
    if (pontos.length > 1) {
        leafletMap.fitBounds(L.latLngBounds(pontos), { padding: [50, 50], maxZoom: 16 });
    }
}

// ── Geolocalização ──────────────────────────────────────────────
function obterLocalizacao(callback) {
    if (!navigator.geolocation) { callback(null); return; }
    navigator.geolocation.getCurrentPosition(
        function(pos) { callback({ lat: pos.coords.latitude, lng: pos.coords.longitude }); },
        function()    { callback(null); },
        { timeout: 8000, enableHighAccuracy: true, maximumAge: 60000 }
    );
}

function centralizarNaMinhaLocalizacao() {
    var btn = document.getElementById('btn-minha-loc');
    if (btn) { btn.textContent = 'Localizando...'; btn.disabled = true; }

    obterLocalizacao(function(loc) {
        if (btn) { btn.textContent = 'Minha localização'; btn.disabled = false; }
        if (!loc) {
            alert('Não foi possível obter sua localização. Verifique as permissões do navegador.');
            return;
        }
        if (!mapaAberto) { expandirMapa(loc); return; }
        if (leafletMap) {
            leafletMap.setView([loc.lat, loc.lng], 16, { animate: true });
            if (userMarker) userMarker.setLatLng([loc.lat, loc.lng]).openPopup();
        }
    });
}

// ── Controle de exibição ────────────────────────────────────────
function expandirMapa(locForcada) {
    var preview   = document.getElementById('mapa-preview');
    var expandido = document.getElementById('mapa-expandido');
    var btnToggle = document.getElementById('btn-toggle-mapa');

    if (preview)   preview.style.display   = 'none';
    if (expandido) expandido.style.display = 'block';
    if (btnToggle) btnToggle.textContent   = 'Recolher mapa';
    mapaAberto = true;

    var card = document.getElementById('card-mapa');
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Garante que o Leaflet invalide o tamanho após a exibição
    setTimeout(function() {
        if (leafletMap) { leafletMap.invalidateSize(); return; }

        if (locForcada) {
            inicializarMapa(locForcada.lat, locForcada.lng);
            return;
        }

        obterLocalizacao(function(loc) {
            var lat = loc ? loc.lat : -23.5505;
            var lng = loc ? loc.lng : -46.6333;
            inicializarMapa(lat, lng);
        });
    }, 80);
}

function recolherMapa() {
    var preview   = document.getElementById('mapa-preview');
    var expandido = document.getElementById('mapa-expandido');
    var btnToggle = document.getElementById('btn-toggle-mapa');

    if (preview)   preview.style.display   = 'flex';
    if (expandido) expandido.style.display = 'none';
    if (btnToggle) btnToggle.textContent   = 'Expandir mapa';
    mapaAberto = false;
}

function toggleMapa() {
    if (mapaAberto) recolherMapa(); else expandirMapa();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
