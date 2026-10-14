<?php
$tituloPagina = "Minha Conta — Pãozin";
require_once __DIR__ . '/includes/header.php';
?>

<div class="wrap" style="padding: 40px 24px; min-height: 80vh;">
    <div style="max-width: 680px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
            <div>
                <span class="pill" style="margin-bottom: 8px;">Área do Cliente</span>
                <h1 style="font-size: 32px;">Olá, <?= htmlspecialchars($_SESSION['cliente_nome'] ?? 'Vizinho') ?>!</h1>
                <p style="color: var(--cinza); font-size: 14.5px;">
                    WhatsApp conectado: <strong><?= htmlspecialchars($_SESSION['cliente_whatsapp'] ?? '') ?></strong>
                </p>
            </div>
            <a href="/logout" class="btn-3d btn-ghost" style="padding: 10px 18px; font-size: 13px;">
                Sair
            </a>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'alerta_removido'): ?>
            <div class="alerta alerta-sucesso">
                <span style="font-weight: 900; color: var(--fornada-escura);">OK</span>
                <div>Alerta cancelado com sucesso. Você não receberá mais notificações desta padaria.</div>
            </div>
        <?php endif; ?>

        <div class="card-paozin" style="margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 22px;">Padarias que você segue</h2>
                <span class="badge quente"><?= count($padariasSeguidas) ?> ativas</span>
            </div>

            <?php if (!empty($padariasSeguidas)): ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach ($padariasSeguidas as $pad): ?>
                        <div style="background: var(--miolo); border: 2px solid var(--linha); border-radius: var(--r-md); padding: 18px; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
                            <div>
                                <h3 style="font-size: 18px; margin-bottom: 4px;"><?= htmlspecialchars($pad['nome']) ?></h3>
                                <p style="font-size: 13.5px; color: var(--cinza); margin-bottom: 6px;"><?= htmlspecialchars($pad['endereco']) ?></p>
                                <span class="pill" style="font-size: 12px; background: #fff;">
                                    R$ <?= number_format((float)$pad['preco_pao_quilo'], 2, ',', '.') ?>/kg · Nota <?= number_format((float)$pad['nota_media'], 1, ',', '.') ?>
                                </span>
                            </div>

                            <form action="/cancelar-alerta" method="POST" onsubmit="return confirm('Deseja realmente parar de receber avisos desta padaria?');">
                                <input type="hidden" name="padaria_id" value="<?= (int)$pad['id'] ?>">
                                <button type="submit" class="btn-3d btn-ghost" style="color: var(--brasa); border-color: #F8BDBD; padding: 10px 16px; font-size: 13px;">
                                    Parar Avisos
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 32px 16px;">
                    <svg viewBox="0 0 100 116" style="width: 44px; height: 50px; margin-bottom: 12px;"><use href="#pin-pao"/></svg>
                    <p style="color: var(--cinza); margin-bottom: 16px;">Você ainda não está seguindo nenhuma padaria.</p>
                    <a href="/#padarias" class="btn-3d btn-fornada" style="font-size: 14px;">
                        Ver Padarias do Bairro
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center;">
            <a href="/#padarias" class="btn-3d btn-crosta">
                Explorar Mais Padarias no Bairro
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
