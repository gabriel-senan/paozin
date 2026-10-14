<?php
$tituloPagina = "Pãozin";
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

        <div class="mapa-frame">
            <!-- Vetor de Mapa Temático Apple Maps -->
            <svg viewBox="0 0 900 440" style="width: 100%; height: auto; display: block;" preserveAspectRatio="xMidYMid slice">
                <rect width="900" height="440" fill="#F2F0EB"/>
                
                <!-- Água / Córrego -->
                <path d="M-10 330 C120 300 220 380 340 390 C500 405 600 360 740 380 C820 395 860 430 910 440 L910 460 L-10 460 Z" fill="#AADAFF"/>
                
                <!-- Parques e Praças -->
                <ellipse cx="180" cy="110" rx="100" ry="60" fill="#C8E6B5"/>
                <ellipse cx="680" cy="180" rx="90" ry="54" fill="#C8E6B5"/>
                <ellipse cx="460" cy="80" rx="60" ry="36" fill="#CDE9BC"/>

                <!-- Quarteirões do Bairro -->
                <g fill="#E8E4DC" opacity="0.8">
                    <rect x="320" y="120" width="80" height="60" rx="6"/>
                    <rect x="440" y="120" width="70" height="60" rx="6"/>
                    <rect x="320" y="210" width="80" height="70" rx="6"/>
                    <rect x="440" y="210" width="70" height="70" rx="6"/>
                    <rect x="550" y="270" width="90" height="60" rx="6"/>
                    <rect x="120" y="240" width="90" height="60" rx="6"/>
                </g>

                <!-- Rodovia Arterial Amarela -->
                <path d="M-10 200 L910 170" stroke="#FFD983" stroke-width="16" stroke-linecap="round"/>
                <path d="M-10 200 L910 170" stroke="#FFF0C9" stroke-width="6" stroke-linecap="round" opacity="0.75"/>

                <!-- Vias e Ruas Locais com Contorno Quente -->
                <g stroke="#E3DED4" stroke-width="12" stroke-linecap="round" fill="none">
                    <path d="M280 -10 L280 450"/>
                    <path d="M530 -10 L530 450"/>
                    <path d="M-10 100 L910 80"/>
                    <path d="M-10 290 L910 270"/>
                    <path d="M-10 390 L910 360"/>
                </g>
                <g stroke="#FFFFFF" stroke-width="8" stroke-linecap="round" fill="none">
                    <path d="M280 -10 L280 450"/>
                    <path d="M530 -10 L530 450"/>
                    <path d="M-10 100 L910 80"/>
                    <path d="M-10 290 L910 270"/>
                    <path d="M-10 390 L910 360"/>
                </g>

                <!-- Nomes de Ruas com Halo Protetor -->
                <g font-family="Inter, sans-serif" font-size="11" font-weight="600" fill="#9A9086" text-anchor="middle">
                    <text x="280" y="60" transform="rotate(90 280 60)" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">R. das Flores</text>
                    <text x="530" y="60" transform="rotate(90 530 60)" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">Av. do Pão</text>
                    <text x="410" y="96" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">Rua Central</text>
                    <text x="410" y="284" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">R. Padre Anchieta</text>
                    <text x="180" y="115" stroke="#C8E6B5" stroke-width="3" paint-order="stroke">Praça da Matriz</text>
                </g>

                <!-- Pinos Pãozin Ativos -->
                <!-- Padaria 1: Fornada de Ouro (Fornada Ativa com Anel Pulsante) -->
                <g transform="translate(370, 180)">
                    <circle cx="20" cy="22" r="30" fill="none" stroke="#58CC02" stroke-width="4" class="pulse-ring"/>
                    <use href="#pin-pao" x="0" y="0" width="40" height="46"/>
                    <text x="20" y="62" font-family="Nunito, sans-serif" font-size="12" font-weight="900" fill="#5C3410" text-anchor="middle" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">Padaria Fornada de Ouro</text>
                </g>

                <!-- Padaria 2: Pão da Vila -->
                <g transform="translate(620, 240)">
                    <use href="#pin-pao" x="0" y="0" width="34" height="40" opacity="0.85"/>
                    <text x="17" y="54" font-family="Nunito, sans-serif" font-size="11.5" font-weight="900" fill="#5C3410" text-anchor="middle" stroke="#F2F0EB" stroke-width="3" paint-order="stroke">Panificadora Pão da Vila</text>
                </g>

                <!-- Posição do Usuário (Ponto Azul Apple) -->
                <circle cx="310" cy="290" r="16" fill="#1CB0F6" opacity="0.18"/>
                <circle cx="310" cy="290" r="8" fill="#1CB0F6" stroke="#fff" stroke-width="3"/>
            </svg>

            <!-- Card Glassmorphism Flutuante sobre o Mapa -->
            <div class="glass mapa-overlay-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <h4 style="font-size: 16px; font-weight: 900; color: var(--carvao);">Padaria Fornada de Ouro</h4>
                        <p style="font-size: 12.5px; color: var(--cinza);">Rua das Flores, 120 · 3 min a pé</p>
                    </div>
                    <span class="badge quente">Saído agora</span>
                </div>
                <div style="display: flex; gap: 8px; margin: 12px 0;">
                    <span class="pill" style="font-size: 12px; flex: 1; justify-content: center; font-weight: 700;">R$ 15,90/kg</span>
                    <span class="pill" style="font-size: 12px; flex: 1; justify-content: center; font-weight: 700;">Nota 4.9</span>
                </div>
                <a href="#padarias" class="btn-3d btn-fornada" style="width: 100%; padding: 11px; font-size: 13px;">
                    Quero o Aviso Desta Padaria
                </a>
            </div>
        </div>

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
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 18px 0;">
                            <div style="background: var(--miolo); border: 1px solid var(--linha); border-radius: var(--r-md); padding: 12px; text-align: center;">
                                <span style="font-size: 12px; color: var(--cinza); display: block;">Preço do Quilo</span>
                                <strong style="font-size: 18px; color: var(--crosta-fundo);">
                                    R$ <?= number_format((float)$p['preco_pao_quilo'], 2, ',', '.') ?>
                                </strong>
                            </div>
                            <div style="background: var(--miolo); border: 1px solid var(--linha); border-radius: var(--r-md); padding: 12px; text-align: center;">
                                <span style="font-size: 12px; color: var(--cinza); display: block;">Status da Fornalha</span>
                                <strong style="font-size: 14px; color: var(--fornada-escura);">
                                    Fornadas Ativas
                                </strong>
                            </div>
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
