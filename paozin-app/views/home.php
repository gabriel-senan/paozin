<?php
$tituloPagina = "Pãozin — O Pão Quentinho no Seu Bairro";

// Leaflet carregado no head (sem SRI)
$extraHead = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
';

// Padarias com coordenadas para o mapa real da landing page
$padariasMapa = array_filter($padarias ?? [], function($p) {
    return !empty($p['latitude']) && !empty($p['longitude']);
});
$padariasMapa = array_values(array_map(function($p) {
    return [
        'id'                    => (int)$p['id'],
        'nome'                  => $p['nome'],
        'endereco'              => $p['endereco'],
        'lat'                   => (float)$p['latitude'],
        'lng'                   => (float)$p['longitude'],
        'nota'                  => (float)$p['nota_media'],
        'preco'                 => (float)$p['preco_pao_quilo'],
        'preco_unidade'         => !empty($p['preco_pao_unidade']) ? (float)$p['preco_pao_unidade'] : null,
        'horario_funcionamento' => $p['horario_funcionamento'] ?? null,
        'horarios_fornada'      => $p['horarios_fornada'] ?? null,
    ];
}, $padariasMapa));

require_once __DIR__ . '/includes/header.php';
?>

<!-- ================= HERO SECTION ================= -->
<section class="hero">
    <div class="wrap">
        
        <!-- Feedback Alerts -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'inscrito_sucesso'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 28px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>
                    <strong>Inscrição Concluída com Sucesso!</strong><br>
                    Seu WhatsApp receberá uma notificação no minuto exato em que a próxima fornada sair do forno.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'cliente_novo'): ?>
            <div class="alerta alerta-aviso" style="margin-bottom: 28px;">
                <span style="font-weight: 900; color: var(--crosta-escura);">AVISO</span>
                <div>
                    <strong>Bem-vindo ao Pãozin!</strong><br>
                    Não encontramos inscrições ativas para o telefone informado. Escolha uma das padarias abaixo e cadastre-se para começar a receber avisos.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'desconectado'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 28px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>Você encerrou sua sessão com segurança. Até a próxima fornada.</div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'erro_login_cliente'): ?>
            <div class="alerta alerta-erro" style="margin-bottom: 28px;">
                <span style="font-weight: 900; color: var(--brasa);">ERRO</span>
                <div>Por favor, informe seu número de WhatsApp para acessar a sua conta.</div>
            </div>
        <?php endif; ?>

        <div class="hero-grid">
            <div>
                <div class="eyebrow">A crocância encontra o GPS</div>
                <h1>O pão quentinho,<br><span>perto de você!</span></h1>
                <p class="lead">
                    Conectamos você à sua padaria favorita no segundo exato em que o pão sai do forno. Chega de errar o horário da fornalha.
                </p>

                <div class="hero-ctas">
                    <a href="#padarias" class="btn-3d btn-fornada btn-grande">
                        Quero ser avisado
                    </a>
                    <button onclick="openLoginModal('padaria')" class="btn-3d btn-ghost">
                        Tenho Padaria
                    </button>
                </div>
            </div>

            <!-- Mascote com Balão Flutuante -->
            <div class="mascote-container">
                <img src="/assets/img/paozin-mascote.png" alt="Mascote Pãozin" class="mascote-img" onerror="this.src='/paozin-mascote.png'">

                <div class="balao-zap-float">
                    <b style="color: #2F6D01; display: flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 100 116" style="width: 13px; height: 15px;"><use href="#pin-pao"/></svg>
                        Aviso do Pãozin
                    </b>
                    <p style="margin: 6px 0; font-size: 13px; color: #2D4C1E; line-height: 1.4;">
                        Olá, vizinho! Acabou de sair pão quentinho na <strong>Padaria Fornada de Ouro</strong>
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ================= COMO FUNCIONA ================= -->
<section id="como-funciona">
    <div class="wrap">
        <div class="eyebrow">01 · Simplicidade e Rapidez</div>
        <h2>A vizinhança conectada em 3 passos</h2>
        <p class="lead">Sem baixar aplicativos pesados. Tudo acontece onde você já está: no seu WhatsApp e na caminhada até a padaria.</p>

        <div class="grid g3" style="margin-top: 40px;">
            <div class="card-paozin">
                <h3 style="font-size: 20px; margin-bottom: 8px;">1. O Padeiro Tira a Fornada</h3>
                <p style="font-size: 14.5px; color: var(--cinza);">
                    No minuto em que a assadeira sai da fornalha, o padeiro aperta um único botão no balcão: <em>"O Pão Saiu Quentinho!"</em>.
                </p>
            </div>

            <div class="card-paozin">
                <h3 style="font-size: 20px; margin-bottom: 8px;">2. Notificação no WhatsApp</h3>
                <p style="font-size: 14.5px; color: var(--cinza);">
                    O motor inteligente do Pãozin notifica os moradores cadastrados no bairro instantaneamente com o preço do quilo atualizado.
                </p>
            </div>

            <div class="card-paozin">
                <h3 style="font-size: 20px; margin-bottom: 8px;">3. Você Pega o Pão Estalando</h3>
                <p style="font-size: 14.5px; color: var(--cinza);">
                    Você caminha poucos minutos até a padaria e garante o pão crocante por fora e macio por dentro na janela ideal de degustação.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================= SISTEMA DE MAPA ================= -->
<section id="mapa" style="background: #FAF6EE;">
    <div class="wrap">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
            <div>
                <div class="eyebrow">02 · O Bairro como Palco</div>
                <h2>A crocância no mapa</h2>
                <p class="lead">Descubra as fornalhas ativas ao redor da sua casa em tempo real.</p>
            </div>
            <span class="pill" style="background: var(--farinha);">Capão Redondo / SP</span>
        </div>

        <div class="mapa-frame" style="position: relative;">
            <!-- Container do Mapa Leaflet -->
            <div id="home-paozin-map" style="width: 100%; height: 460px; z-index: 1;"></div>

            <!-- Botão de Minha Localização no Mapa -->
            <button type="button" onclick="centralizarMinhaLocalizacaoHome()" class="btn-3d btn-ghost" 
                    title="Minha Localização"
                    style="position: absolute; top: 16px; right: 16px; z-index: 10; padding: 8px 14px; font-size: 12px; background: rgba(255,255,255,0.92); backdrop-filter: blur(8px); display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 14px;"></span> Minha Localização
            </button>

            <!-- Card Glassmorphism Flutuante sobre o Mapa -->
            <div class="glass mapa-overlay-card" id="home-mapa-overlay" style="z-index: 10;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <h4 id="card-padaria-nome" style="font-size: 16px; font-weight: 900; color: var(--carvao);">Padaria Fornada de Ouro</h4>
                        <p id="card-padaria-endereco" style="font-size: 12.5px; color: var(--cinza);">Rua das Flores, 120 · Bairro Central</p>
                    </div>
                    <span class="badge quente">Saído agora</span>
                </div>
                <div style="display: flex; gap: 8px; margin: 12px 0;">
                    <span id="card-padaria-preco" class="pill" style="font-size: 12px; flex: 1; justify-content: center; font-weight: 700;">R$ 15,90/kg</span>
                    <span id="card-padaria-nota" class="pill" style="font-size: 12px; flex: 1; justify-content: center; font-weight: 700;">Nota 4.9</span>
                </div>
                <div id="card-padaria-extra" style="font-size: 11.5px; color: var(--cinza); margin-bottom: 12px;">
                    ⏱ Funcionamento: 06:00 às 20:00
                </div>
                <a href="#padarias" class="btn-3d btn-fornada" style="width: 100%; padding: 11px; font-size: 13px;">
                    Quero o Aviso Desta Padaria
                </a>
            </div>
        </div>

        <script>
        var PADARIAS_HOME = <?= json_encode($padariasMapa, JSON_UNESCAPED_UNICODE) ?>;
        var homeMap = null;
        var homeUserMarker = null;

        function criarIconePao(cor, corBorda, tamanho) {
            var h = Math.round(tamanho * 1.3);
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 52" width="' + tamanho + '" height="' + h + '">'
                + '<path d="M20 2C11.2 2 4 9.2 4 18c0 11.7 16 32 16 32s16-20.3 16-32C36 9.2 28.8 2 20 2z"'
                + ' fill="' + cor + '" stroke="' + corBorda + '" stroke-width="2.5"/>'
                + '<circle cx="20" cy="18" r="7" fill="white" opacity="0.9"/>'
                + '</svg>';
            return L.divIcon({
                html: svg,
                className: '',
                iconSize: [tamanho, h],
                iconAnchor: [tamanho / 2, h],
                popupAnchor: [0, -(h - 4)]
            });
        }

        function selecionarPadariaCard(p) {
            var elNome = document.getElementById('card-padaria-nome');
            var elEnd  = document.getElementById('card-padaria-endereco');
            var elPrc  = document.getElementById('card-padaria-preco');
            var elNota = document.getElementById('card-padaria-nota');
            var elExt  = document.getElementById('card-padaria-extra');

            if (elNome) elNome.textContent = p.nome;
            if (elEnd)  elEnd.textContent  = p.endereco;
            if (elPrc)  {
                var txt = 'R$ ' + p.preco.toFixed(2).replace('.', ',') + '/kg';
                if (p.preco_unidade) txt += ' (R$ ' + p.preco_unidade.toFixed(2).replace('.', ',') + '/un)';
                elPrc.textContent = txt;
            }
            if (elNota) elNota.textContent = 'Nota ' + p.nota.toFixed(1).replace('.', ',');
            if (elExt) {
                var extra = [];
                if (p.horario_funcionamento) extra.push('⏱ ' + p.horario_funcionamento);
                if (p.horarios_fornada) extra.push('🥖 Fornadas: ' + p.horarios_fornada);
                elExt.innerHTML = extra.join(' &bull; ') || 'Fornadas no bairro';
            }
        }

        function initHomeMap() {
            var container = document.getElementById('home-paozin-map');
            if (!container || typeof L === 'undefined') return;

            var defaultLat = -23.555;
            var defaultLng = -46.645;
            if (PADARIAS_HOME.length > 0) {
                defaultLat = PADARIAS_HOME[0].lat;
                defaultLng = PADARIAS_HOME[0].lng;
            }

            homeMap = L.map('home-paozin-map', {
                center: [defaultLat, defaultLng],
                zoom: 14,
                zoomControl: true,
                attributionControl: true
            });

            // Esri Light Gray Canvas (visual Apple Maps, sem chave)
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; DeLorme, NAVTEQ',
                maxZoom: 16
            }).addTo(homeMap);

            var iconePadaria = criarIconePao('#E89B3C', '#B36B1F', 38);
            var boundsPoints = [];

            PADARIAS_HOME.forEach(function(p, idx) {
                boundsPoints.push([p.lat, p.lng]);

                var precoUnTxt = p.preco_unidade ? ' &bull; R$ ' + p.preco_unidade.toFixed(2).replace('.', ',') + '/un' : '';
                var infoHorarios = '';
                if (p.horario_funcionamento) infoHorarios += '<div style="font-size:11.5px;color:#6b5d4e;margin-top:4px;">⏱ ' + p.horario_funcionamento + '</div>';
                if (p.horarios_fornada) infoHorarios += '<div style="font-size:11.5px;color:#58CC02;font-weight:700;margin-top:2px;">🥖 Fornadas: ' + p.horarios_fornada + '</div>';

                var popupContent = '<div style="font-family:Inter,sans-serif;min-width:200px;line-height:1.4;">'
                    + '<strong style="font-size:14px;display:block;margin-bottom:3px;">' + p.nome + '</strong>'
                    + '<span style="font-size:12px;color:#8A7C6B;display:block;margin-bottom:5px;">' + p.endereco + '</span>'
                    + '<span style="font-size:12.5px;font-weight:600;">R$ ' + p.preco.toFixed(2).replace('.', ',') + '/kg' + precoUnTxt + ' &middot; Nota ' + p.nota.toFixed(1).replace('.', ',') + '</span>'
                    + infoHorarios
                    + '</div>';

                var marker = L.marker([p.lat, p.lng], { icon: iconePadaria }).addTo(homeMap);
                marker.bindPopup(popupContent, { maxWidth: 260 });
                marker.on('click', function() {
                    selecionarPadariaCard(p);
                });

                if (idx === 0) {
                    selecionarPadariaCard(p);
                }
            });

            if (boundsPoints.length > 1) {
                homeMap.fitBounds(L.latLngBounds(boundsPoints), { padding: [50, 50], maxZoom: 15 });
            }

            // Tenta obter geolocalização do usuário suavemente
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    var uLat = pos.coords.latitude;
                    var uLng = pos.coords.longitude;
                    var iconeUser = L.divIcon({
                        html: '<div style="width:18px;height:18px;border-radius:50%;background:#1CB0F6;border:3px solid #fff;box-shadow:0 0 0 4px rgba(28,176,246,0.35);"></div>',
                        className: '',
                        iconSize: [18, 18],
                        iconAnchor: [9, 9]
                    });

                    homeUserMarker = L.marker([uLat, uLng], { icon: iconeUser, zIndexOffset: 2000 })
                        .addTo(homeMap)
                        .bindPopup('<strong style="font-family:Inter,sans-serif;">Você está aqui</strong>');

                    boundsPoints.push([uLat, uLng]);
                    homeMap.fitBounds(L.latLngBounds(boundsPoints), { padding: [60, 60], maxZoom: 15 });
                }, function() {
                    // Sem permissão - mantém foco nas padarias
                }, { timeout: 6000, maximumAge: 60000 });
            }
        }

        function centralizarMinhaLocalizacaoHome() {
            if (!homeMap) return;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    var uLat = pos.coords.latitude;
                    var uLng = pos.coords.longitude;
                    if (!homeUserMarker) {
                        var iconeUser = L.divIcon({
                            html: '<div style="width:18px;height:18px;border-radius:50%;background:#1CB0F6;border:3px solid #fff;box-shadow:0 0 0 4px rgba(28,176,246,0.35);"></div>',
                            className: '',
                            iconSize: [18, 18],
                            iconAnchor: [9, 9]
                        });
                        homeUserMarker = L.marker([uLat, uLng], { icon: iconeUser, zIndexOffset: 2000 }).addTo(homeMap);
                    }
                    homeMap.setView([uLat, uLng], 15, { animate: true });
                    homeUserMarker.bindPopup('<strong style="font-family:Inter,sans-serif;">Você está aqui</strong>').openPopup();
                });
            }
        }

        // Garante que o mapa inicializa após o carregamento da biblioteca Leaflet
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHomeMap);
        } else {
            setTimeout(initHomeMap, 50);
        }
        </script>

    </div>
</section>

<!-- ================= PADARIAS CONVENIADAS ================= -->
<section id="padarias">
    <div class="wrap">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 14px;">
            <div>
                <div class="eyebrow">03 · Estabelecimentos no Bairro</div>
                <h2>Padarias com aviso em tempo real</h2>
                <p class="lead">Cadastre seu número na padaria que você mais frequenta. O serviço é 100% gratuito para o freguês.</p>
            </div>
            <div>
                <button onclick="openLoginModal('cliente')" class="btn-3d btn-ghost" style="padding: 10px 18px; font-size: 13px;">
                    Já tem cadastro? Entrar
                </button>
            </div>
        </div>

        <div class="grid g2">
            <?php if (!empty($padarias)): ?>
                <?php foreach ($padarias as $p): ?>
                    <div class="card-paozin">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <h3 style="font-size: 22px; margin-bottom: 4px;"><?= htmlspecialchars($p['nome']) ?></h3>
                                <p style="color: var(--cinza); font-size: 13.5px;">
                                    <?= htmlspecialchars($p['endereco']) ?>
                                </p>
                            </div>
                            <span class="badge quente">
                                Nota <?= number_format((float)$p['nota_media'], 1, ',', '.') ?>
                            </span>
                        </div>

                        <!-- Mini Stats do Estabelecimento -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 18px 0 10px;">
                            <div style="background: var(--miolo); border: 1px solid var(--linha); border-radius: var(--r-md); padding: 12px; text-align: center;">
                                <span style="font-size: 12px; color: var(--cinza); display: block;">Preço do Quilo</span>
                                <strong style="font-size: 17px; color: var(--crosta-fundo);">
                                    R$ <?= number_format((float)$p['preco_pao_quilo'], 2, ',', '.') ?>
                                </strong>
                            </div>
                            <div style="background: var(--miolo); border: 1px solid var(--linha); border-radius: var(--r-md); padding: 12px; text-align: center;">
                                <span style="font-size: 12px; color: var(--cinza); display: block;">Valor por Unidade</span>
                                <strong style="font-size: 17px; color: var(--crosta-fundo);">
                                    <?= !empty($p['preco_pao_unidade']) ? 'R$ ' . number_format((float)$p['preco_pao_unidade'], 2, ',', '.') : '—' ?>
                                </strong>
                            </div>
                        </div>

                        <!-- Horários de funcionamento e saídas de pão -->
                        <div style="background: #FDFBF7; border: 1px solid var(--linha); border-radius: var(--r-md); padding: 10px 14px; margin-bottom: 18px; font-size: 12.5px;">
                            <?php if (!empty($p['horario_funcionamento'])): ?>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span style="color: var(--cinza);">Funcionamento:</span>
                                    <span style="color: var(--carvao); font-weight: 600;"><?= htmlspecialchars($p['horario_funcionamento']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($p['horarios_fornada'])): ?>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--cinza);">Fornadas fixas:</span>
                                    <span style="color: var(--fornada-escura); font-weight: 700;"><?= htmlspecialchars($p['horarios_fornada']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Formulário de Inscrição em 1 Clique -->
                        <form action="/inscrever" method="POST" style="border-top: 1px dashed var(--linha); padding-top: 18px;">
                            <input type="hidden" name="padaria_id" value="<?= (int)$p['id'] ?>">

                            <div class="input-group">
                                <label class="input-label">Seu Nome:</label>
                                <input type="text" name="nome" class="input-field" placeholder="Ex: Gabriel Sena" required>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Seu WhatsApp (com DDD):</label>
                                <input type="tel" name="whatsapp" class="input-field" placeholder="Ex: 11999990001" required>
                            </div>

                            <button type="submit" class="btn-3d btn-fornada" style="width: 100%;">
                                Ativar Aviso Desta Padaria
                            </button>
                        </form>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card-paozin" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
                    <p style="color: var(--cinza);">Carregando estabelecimentos credenciados...</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ================= GAMIFICAÇÃO ================= -->
<section style="background: #FFF9EE;">
    <div class="wrap">
        <div class="eyebrow">04 · A Disputa da Vizinhança</div>
        <h2>A nota do pão e suas sequências</h2>
        <p class="lead">Porque comer pão fresco deveria ser a coisa mais divertida e saborosa do seu dia.</p>

        <div class="grid g4" style="margin-top: 36px;">
            <div class="card" style="text-align: center;">
                <span class="pill" style="margin-bottom: 10px; font-size: 11.5px;">Sequência</span>
                <b style="font-family: var(--font-display); font-weight: 900; font-size: 26px; display: block; margin-top: 6px;">7 dias</b>
                <p style="font-size: 13px; color: var(--cinza); margin-top: 4px;">Sequência de pão quentinho garantida</p>
            </div>
            <div class="card" style="text-align: center;">
                <span class="pill" style="margin-bottom: 10px; font-size: 11.5px;">Avaliação</span>
                <b style="font-family: var(--font-display); font-weight: 900; font-size: 26px; display: block; margin-top: 6px;">4,95</b>
                <p style="font-size: 13px; color: var(--cinza); margin-top: 4px;">Nota média de crocância da região</p>
            </div>
            <div class="card" style="text-align: center;">
                <span class="pill" style="margin-bottom: 10px; font-size: 11.5px;">Ranking</span>
                <b style="font-family: var(--font-display); font-weight: 900; font-size: 26px; display: block; margin-top: 6px;">1º Lugar</b>
                <p style="font-size: 13px; color: var(--cinza); margin-top: 4px;">Padaria Fornada de Ouro na liderança</p>
            </div>
            <div class="card" style="text-align: center;">
                <span class="pill" style="margin-bottom: 10px; font-size: 11.5px;">Qualidade</span>
                <b style="font-family: var(--font-display); font-weight: 900; font-size: 26px; display: block; margin-top: 6px;">100%</b>
                <p style="font-size: 13px; color: var(--cinza); margin-top: 4px;">Pão francês sempre no ponto certo</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= ÁREA DA PADARIA (B2B) ================= -->
<section id="padaria-b2b">
    <div class="wrap">
        <div class="card-paozin" style="background: linear-gradient(135deg, #FFFDF9 0%, #FFF3DD 100%); border-color: var(--crosta-clara); padding: 48px 36px;">
            <div style="max-width: 640px;">
                <span class="pill" style="margin-bottom: 16px; background: #FFF4D9; color: var(--crosta-escura); border-color: var(--crosta);">
                    Para Donos de Panificadoras e Mercados
                </span>
                <h2 style="font-size: clamp(28px, 4vw, 42px); margin-bottom: 16px;">
                    Zere o desperdício de pão frio e encha seu balcão na hora do pico.
                </h2>
                <p class="lead" style="font-size: 16px; margin-bottom: 24px;">
                    Por apenas <strong>R$ 20,00 mensais</strong> (menos que um pacote de café tradicional), o Pãozin conecta o seu forno aos clientes do seu quarteirão no minuto exato em que a fornada fica pronta.
                </p>

                <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 32px;">
                    <span class="badge" style="background: #fff; font-size: 12.5px;">Sem chip extra na padaria</span>
                    <span class="badge" style="background: #fff; font-size: 12.5px;">Um botão no celular do padeiro</span>
                    <span class="badge" style="background: #fff; font-size: 12.5px;">Sem taxa sobre suas vendas</span>
                </div>

                <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                    <button onclick="openLoginModal('padaria')" class="btn-3d btn-fornada btn-grande">
                        Acessar Painel do Padeiro
                    </button>
                    <a href="/painel" class="btn-3d btn-ghost">
                        Testar Modo Operacional
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
