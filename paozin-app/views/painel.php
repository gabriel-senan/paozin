<?php
$tituloPagina = "Painel da Padaria — Pãozin";

if (empty($_SESSION['padaria_id'])) {
    header("Location: /?status=login_necessario");
    exit;
}

$padariaId = (int)$_SESSION['padaria_id'];

try {
    $padaria = Padaria::buscarPorId($padariaId);
    if (!$padaria) {
        header("Location: /logout");
        exit;
    }
    $cooldown = Padaria::verificarCooldown($padariaId, 30);
} catch (Exception $e) {
    $padaria = [
        'id' => $padariaId,
        'nome' => 'Padaria em Demonstração',
        'endereco' => 'Rua Principal, 100',
        'telefone' => '11988880001',
        'preco_pao_quilo' => 15.90,
        'preco_pao_unidade' => 0.95,
        'horario_funcionamento' => '06:00 às 20:00',
        'horarios_fornada' => '06:30, 16:30',
        'status_assinatura' => 'ativo',
        'ultima_fornada' => null
    ];
    $cooldown = ['em_cooldown' => false, 'minutos_restantes' => 0];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="wrap" style="padding: 40px 24px; min-height: 80vh;">
    <div style="max-width: 860px; margin: 0 auto;">

        <!-- Topo com Identificação e Navegação -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; gap: 16px; flex-wrap: wrap;">
            <div>
                <span class="pill" style="margin-bottom: 8px; display: inline-block;">Painel do Estabelecimento</span>
                <h1 style="font-size: 32px; line-height: 1.2;">
                    <?= htmlspecialchars($padaria['nome'] ?? 'Sua Padaria') ?>
                </h1>
                <p style="color: var(--cinza); font-size: 14.5px; margin-top: 4px;">
                    <?= htmlspecialchars($padaria['endereco'] ?? 'Endereço não informado') ?>
                    <?php if (!empty($padaria['telefone'])): ?>
                        &bull; <?= htmlspecialchars($padaria['telefone']) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="/" class="btn-3d btn-ghost" style="padding: 10px 16px; font-size: 13px;">Ver Site</a>
                <a href="/logout" class="pill" style="font-size: 12px; color: var(--brasa);">Desconectar</a>
            </div>
        </div>

        <!-- Mensagens de Feedback -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'dados_atualizados'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">SALVO</span>
                <div>
                    <strong>Dados da Padaria Atualizados com Sucesso!</strong><br>
                    Preços, horários e informações já estão visíveis para os clientes do bairro.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'cadastro_sucesso'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">BEM-VINDO</span>
                <div>
                    <strong>Padaria cadastrada com sucesso!</strong><br>
                    Seu estabelecimento já está no Pãozin. Você já pode disparar avisos de fornada.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'sucesso'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>
                    <strong>Fornada Anunciada com Sucesso!</strong><br>
                    O n8n está despachando os avisos para os clientes do bairro.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'sucesso_simulado'): ?>
            <div class="alerta alerta-sucesso" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--fornada-escura);">SIMULAÇÃO</span>
                <div>
                    <strong>Fornada Registrada (Modo Simulação)!</strong><br>
                    Timestamp atualizado no banco local com sucesso.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'cooldown'): ?>
            <div class="alerta alerta-aviso" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--crosta-escura);">AGUARDE</span>
                <div>
                    <strong>Fornada Recente!</strong><br>
                    Aguarde mais <?= (int)($_GET['minutos'] ?? $cooldown['minutos_restantes']) ?> minutos para disparar um novo aviso em massa.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'erro_atualizar'): ?>
            <div class="alerta alerta-erro" style="margin-bottom: 24px;">
                <span style="font-weight: 900; color: var(--brasa);">ERRO</span>
                <div>
                    <strong>Não foi possível salvar os dados!</strong> Verifique os campos preenchidos.
                </div>
            </div>
        <?php endif; ?>

        <!-- GRID PRINCIPAL: 1) DISPARO DE FORNADA & STATUS | 2) EDIÇÃO DE DADOS (CRUD) -->
        <div style="display: grid; grid-template-columns: 1fr 1.35fr; gap: 24px; align-items: start;">

            <!-- COLUNA ESQUERDA: DISPARO DA FORNADA -->
            <div class="card-paozin" style="text-align: center;">
                <svg viewBox="0 0 100 116" style="width: 48px; height: 56px; margin-bottom: 8px;"><use href="#pin-pao"/></svg>
                <h2 style="font-size: 22px;">Aviso em Massa</h2>
                <p style="color: var(--cinza); font-size: 13.5px; margin-top: 4px; margin-bottom: 20px;">
                    Notifique os fregueses do bairro em 1 clique
                </p>

                <!-- Status Atual da Fornalha -->
                <div style="background: #FDFBF7; border: 2px solid var(--linha); border-radius: var(--r-md); padding: 14px; margin-bottom: 20px; text-align: left;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                        <span style="color: var(--cinza);">Preço por Quilo:</span>
                        <strong style="color: var(--carvao);">R$ <?= number_format((float)($padaria['preco_pao_quilo'] ?? 15.90), 2, ',', '.') ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                        <span style="color: var(--cinza);">Preço por Unidade:</span>
                        <strong style="color: var(--carvao);">
                            <?= !empty($padaria['preco_pao_unidade']) ? 'R$ ' . number_format((float)$padaria['preco_pao_unidade'], 2, ',', '.') : 'Não definido' ?>
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--cinza);">Última fornada:</span>
                        <strong style="color: var(--fornada-escura);">
                            <?= !empty($padaria['ultima_fornada']) ? date('H:i \d\o \d\i\a d/m', strtotime($padaria['ultima_fornada'])) : 'Nenhuma hoje' ?>
                        </strong>
                    </div>
                </div>

                <!-- Botão de Disparo do Padeiro -->
                <?php if ($cooldown['em_cooldown']): ?>
                    <button type="button" class="btn-3d btn-disabled btn-grande" style="width: 100%;" disabled>
                        AGUARDE <?= $cooldown['minutos_restantes'] ?> MIN
                    </button>
                    <p style="font-size: 12px; color: var(--cinza); margin-top: 8px;">
                        Proteção anti-flood ativada.
                    </p>
                <?php else: ?>
                    <form action="/painel/fornada-pronta" method="POST">
                        <button type="submit" class="btn-3d btn-fornada btn-grande" style="width: 100%;">
                            O PÃO SAIU QUENTINHO!
                        </button>
                    </form>
                    <p style="font-size: 12px; color: var(--cinza); margin-top: 8px;">
                        Aperte ao tirar a fornada da fornalha.
                    </p>
                <?php endif; ?>

                <div style="margin-top: 20px; padding-top: 14px; border-top: 1px dashed var(--linha);">
                    <a href="/painel/fornada-pronta?simular=1" onclick="event.preventDefault(); var f = document.createElement('form'); f.method='POST'; f.action='/painel/fornada-pronta?simular=1'; document.body.appendChild(f); f.submit();" style="font-size: 11.5px; color: var(--cinza);">
                        Testar disparo em modo simulação (sem Docker/n8n)
                    </a>
                </div>
            </div>

            <!-- COLUNA DIREITA: EDIÇÃO DE DADOS (CRUD COMPLETO) -->
            <div class="card-paozin">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 2px solid var(--linha); padding-bottom: 12px;">
                    <div>
                        <h2 style="font-size: 20px;">Configurações da Padaria</h2>
                        <p style="font-size: 13px; color: var(--cinza);">Altere preços, horários de saída do pão e atendimento.</p>
                    </div>
                    <span class="badge" style="background: var(--miolo); color: var(--crosta-fundo); font-weight: 800;">
                        ID #<?= (int)$padaria['id'] ?>
                    </span>
                </div>

                <form action="/painel/salvar" method="POST">
                    
                    <!-- Nome da Padaria -->
                    <div class="input-group">
                        <label class="input-label">Nome do Estabelecimento:</label>
                        <input type="text" name="nome" class="input-field" 
                               value="<?= htmlspecialchars($padaria['nome'] ?? '') ?>" required maxlength="100">
                    </div>

                    <!-- Endereço -->
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Endereço Completo (Bairro e Rua):</label>
                        <input type="text" name="endereco" class="input-field" 
                               value="<?= htmlspecialchars($padaria['endereco'] ?? '') ?>" required maxlength="150">
                        <span style="font-size: 11.5px; color: var(--cinza); display: block; margin-top: 4px;">
                            📍 Coordenadas no mapa: <strong><?= number_format((float)($padaria['latitude'] ?? -23.55), 4) ?>, <?= number_format((float)($padaria['longitude'] ?? -46.63), 4) ?></strong> (geocodificado automaticamente)
                        </span>
                    </div>

                    <!-- Telefone / WhatsApp -->
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Telefone / WhatsApp de Contato:</label>
                        <input type="tel" name="telefone" class="input-field" 
                               placeholder="Ex: 11988880001"
                               value="<?= htmlspecialchars($padaria['telefone'] ?? '') ?>" required>
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Utilizado para login da sua equipe no Pãozin.
                        </span>
                    </div>

                    <!-- Preço do Quilo e Preço por Unidade -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
                        <div class="input-group">
                            <label class="input-label">Preço do Quilo (R$):</label>
                            <input type="text" name="preco_pao_quilo" class="input-field" 
                                   value="<?= number_format((float)($padaria['preco_pao_quilo'] ?? 15.90), 2, ',', '.') ?>" required>
                            <span style="font-size: 11.5px; color: var(--cinza); display: block; margin-top: 4px;">Ex: 15,90</span>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Valor por Unidade (R$):</label>
                            <input type="text" name="preco_pao_unidade" class="input-field" 
                                   placeholder="Ex: 0,95"
                                   value="<?= !empty($padaria['preco_pao_unidade']) ? number_format((float)$padaria['preco_pao_unidade'], 2, ',', '.') : '0,95' ?>">
                            <span style="font-size: 11.5px; color: var(--cinza); display: block; margin-top: 4px;">Pão francês por un.</span>
                        </div>
                    </div>

                    <!-- Horário de Funcionamento -->
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Horário de Funcionamento:</label>
                        <input type="text" name="horario_funcionamento" class="input-field" 
                               placeholder="Ex: Seg a Sáb 06:00 às 20:00 / Dom 07:00 às 13:00"
                               value="<?= htmlspecialchars($padaria['horario_funcionamento'] ?? '06:00 às 20:00') ?>">
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Informa os vizinhos do horário em que o balcão está aberto.
                        </span>
                    </div>

                    <!-- Horários Fixos de Fornada -->
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Há horário fixo para o pão sair?</label>
                        <input type="text" name="horarios_fornada" class="input-field" 
                               placeholder="Ex: 06:30, 16:30 ou deixe 'Sem horário fixo'"
                               value="<?= htmlspecialchars($padaria['horarios_fornada'] ?? '06:30, 16:30') ?>">
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Ex: "06:30, 16:30" ou "Fornadas contínuas sem horário fixo".
                        </span>
                    </div>

                    <!-- Senha de Acesso do Estabelecimento -->
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Senha de Acesso ao Painel:</label>
                        <div style="position: relative;">
                            <input type="password" name="senha" id="painel-senha" class="input-field"
                                   placeholder="Deixe em branco para manter a atual"
                                   value="<?= htmlspecialchars($padaria['senha'] ?? 'paozin123') ?>"
                                   style="padding-right: 44px;">
                            <button type="button" onclick="toggleVisibilidadeSenha('painel-senha', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Utilizada para entrar no painel via telefone ou lista de padarias.
                        </span>
                    </div>

                    <!-- Botão de Salvar Alterações -->
                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn-3d btn-azul" style="width: 100%; padding: 13px;">
                            Salvar Dados da Padaria
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
