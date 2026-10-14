<?php
$tituloPagina = "Padeiro";
$padariaId = (int)($_SESSION['padaria_id'] ?? 1);

try {
    $padaria = Padaria::buscarPorId($padariaId);
    $cooldown = Padaria::verificarCooldown($padariaId, 30);
} catch (Exception $e) {
    $padaria = [
        'nome' => 'Padaria Fornada de Ouro (Demonstração)',
        'preco_pao_quilo' => 15.90,
        'status_assinatura' => 'ativo',
        'ultima_fornada' => null
    ];
    $cooldown = ['em_cooldown' => false, 'minutos_restantes' => 0];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="wrap" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card-paozin" style="max-width: 480px; width: 100%; text-align: center;">

        <!-- Ícone / Mascote -->
        <svg viewBox="0 0 100 116" style="width: 52px; height: 60px; margin-bottom: 8px;"><use href="#pin-pao"/></svg>
        
        <h1>Painel do Padeiro</h1>
        <p style="color: var(--cinza); font-size: 14.5px; margin-top: 4px; margin-bottom: 24px;">
            <?= htmlspecialchars($padaria['nome'] ?? 'Sua Padaria') ?>
        </p>

        <!-- Mensagens de Feedback -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'sucesso'): ?>
            <div class="alerta alerta-sucesso">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>
                    <strong>Fornada Anunciada com Sucesso!</strong><br>
                    O n8n está despachando os avisos para os clientes do bairro.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'sucesso_simulado'): ?>
            <div class="alerta alerta-sucesso">
                <span style="font-weight: 900; color: var(--fornada-escura);">SIMULAÇÃO</span>
                <div>
                    <strong>Fornada Registrada (Modo Simulação)!</strong><br>
                    Timestamp atualizado no banco local com sucesso.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'cooldown'): ?>
            <div class="alerta alerta-aviso">
                <span style="font-weight: 900; color: var(--crosta-escura);">AGUARDE</span>
                <div>
                    <strong>Fornada Recente!</strong><br>
                    Aguarde mais <?= (int)($_GET['minutos'] ?? $cooldown['minutos_restantes']) ?> minutos para disparar um novo aviso em massa.
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'erro'): ?>
            <div class="alerta alerta-erro">
                <span style="font-weight: 900; color: var(--brasa);">ERRO</span>
                <div>
                    <strong>Serviço de Automação Indisponível!</strong><br>
                    O motor local do n8n não respondeu. Certifique-se de que o Docker está em execução ou use a simulação abaixo.
                </div>
            </div>
        <?php endif; ?>

        <!-- Caixa de Status da Fornada -->
        <div style="background: #FDFBF7; border: 2px solid var(--linha); border-radius: var(--r-md); padding: 18px; margin-bottom: 24px; text-align: left;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13.5px; color: var(--cinza);">Preço do quilo hoje:</span>
                <strong style="color: var(--carvao);">R$ <?= number_format((float)($padaria['preco_pao_quilo'] ?? 15.90), 2, ',', '.') ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13.5px; color: var(--cinza);">Última fornada:</span>
                <strong style="color: var(--carvao);">
                    <?= !empty($padaria['ultima_fornada']) ? date('H:i \d\o \d\i\a d/m', strtotime($padaria['ultima_fornada'])) : 'Nenhuma hoje' ?>
                </strong>
            </div>
        </div>

        <!-- Botão Principal do Padeiro -->
        <?php if ($cooldown['em_cooldown']): ?>
            <button type="button" class="btn-3d btn-disabled btn-grande" disabled>
                AGUARDE <?= $cooldown['minutos_restantes'] ?> MIN P/ PRÓXIMO
            </button>
            <p style="font-size: 12.5px; color: var(--cinza); margin-top: 10px;">
                Proteção anti-flood ativada para não saturar os clientes do bairro.
            </p>
        <?php else: ?>
            <form action="/painel/fornada-pronta" method="POST">
                <button type="submit" class="btn-3d btn-fornada btn-grande">
                    O PÃO SAIU QUENTINHO!
                </button>
            </form>
            <p style="font-size: 12.5px; color: var(--cinza); margin-top: 10px;">
                Aperte apenas quando o pão acabar de sair da fornalha.
            </p>
        <?php endif; ?>

        <!-- Opção de teste rápido sem n8n ligado -->
        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px dashed var(--linha); display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
            <a href="/" style="color: var(--cinza);">Voltar à Landing Page</a>
            <a href="/logout" style="color: var(--brasa);">Trocar de Padaria / Sair</a>
        </div>
        <div style="margin-top: 12px;">
            <a href="/painel/fornada-pronta?simular=1" onclick="event.preventDefault(); var f = document.createElement('form'); f.method='POST'; f.action='/painel/fornada-pronta?simular=1'; document.body.appendChild(f); f.submit();" style="font-size: 11.5px; color: var(--cinza);">
                Testar disparo em modo simulação (sem n8n ativo)
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
