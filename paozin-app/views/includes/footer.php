</main>

<!-- ================= MODAL UNIFICADO DE LOGIN ================= -->
<div id="modal-login" class="modal-overlay" onclick="if(event.target===this) closeLoginModal();">
    <div class="modal-card">
        <button class="modal-close" onclick="closeLoginModal();">&times;</button>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <svg viewBox="0 0 100 116" style="width: 38px; height: 44px; margin-bottom: 6px;"><use href="#pin-pao"/></svg>
            <h2 style="font-size: 24px;">Entrar no Pãozin</h2>
            <p style="font-size: 13.5px; color: var(--cinza);">Acesse o serviço de acordo com o seu perfil</p>
        </div>

        <div class="modal-tabs">
            <button type="button" class="modal-tab active" id="tab-btn-cliente" onclick="switchLoginTab('cliente')">
                Sou Cliente
            </button>
            <button type="button" class="modal-tab" id="tab-btn-padaria" onclick="switchLoginTab('padaria')">
                Sou Padaria
            </button>
        </div>

        <!-- ABA 1: LOGIN CLIENTE -->
        <div id="tab-cliente" class="tab-pane active">
            <form action="/login-cliente" method="POST">
                <div class="input-group">
                    <label class="input-label">Seu WhatsApp (com DDD):</label>
                    <input type="tel" name="whatsapp" class="input-field" placeholder="Ex: 11999990001" required autofocus>
                    <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                        Digite o número que você usa para receber os avisos de fornada.
                    </span>
                </div>

                <button type="submit" class="btn-3d btn-azul" style="width: 100%; margin-top: 10px;">
                    Entrar como Cliente
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center; border-top: 1px dashed var(--linha); padding-top: 14px;">
                <p style="font-size: 12.5px; color: var(--cinza);">
                    Ainda não tem cadastro? <a href="/#padarias" onclick="closeLoginModal();" style="color: var(--crosta-escura);">Cadastre-se na sua padaria favorita!</a>
                </p>
            </div>
        </div>

        <!-- ABA 2: LOGIN PADARIA -->
        <div id="tab-padaria" class="tab-pane">
            <form action="/login-padaria" method="POST">
                <div class="input-group">
                    <label class="input-label">Selecione seu Estabelecimento:</label>
                    <select name="padaria_id" class="input-field" style="cursor: pointer;" required>
                        <?php 
                        try {
                            $listaPadarias = Padaria::listarTodas();
                            foreach ($listaPadarias as $pad) {
                                echo '<option value="' . (int)$pad['id'] . '">' . htmlspecialchars($pad['nome']) . ' (R$ ' . number_format((float)$pad['preco_pao_quilo'], 2, ',', '.') . '/kg)</option>';
                            }
                        } catch (Exception $e) {
                            echo '<option value="1">Padaria Fornada de Ouro</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="input-group">
                    <label class="input-label">Código de Acesso do Balcão:</label>
                    <input type="password" name="codigo_acesso" class="input-field" placeholder="Padrão do MVP (opcional)" value="paozin123">
                    <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                        Acesso simplificado de balcão para disparo rápido da fornalha.
                    </span>
                </div>

                <button type="submit" class="btn-3d btn-fornada" style="width: 100%; margin-top: 10px;">
                    Entrar no Painel do Padeiro
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center; border-top: 1px dashed var(--linha); padding-top: 14px;">
                <p style="font-size: 12.5px; color: var(--cinza);">
                    Quer cadastrar sua padaria no Pãozin? <a href="/#padaria-b2b" onclick="closeLoginModal();" style="color: var(--crosta-escura);">Saiba como por R$ 20/mês</a>
                </p>
            </div>
        </div>

    </div>
</div>

<!-- ================= FOOTER ================= -->
<footer>
    <div class="wrap">
        <div class="wordmark">Pão<span style="color: var(--crosta);">zin</span></div>
        <p style="font-weight: 700; color: #E0D2C1; margin-top: 6px;">O pão quentinho, no mapa do seu bairro.</p>
        <p style="font-size: 13px; color: #8F8170; margin-top: 14px;">
            Foodtech Hiperlocal • Sistema Inteligente de Aviso de Fornada • Conectado à Evolution API & n8n
        </p>
        <div style="margin-top: 24px; display: flex; justify-content: center; gap: 20px; font-size: 13px;">
            <a href="/#como-funciona" style="color: #C2B4A2;">Como Funciona</a>
            <a href="/#mapa" style="color: #C2B4A2;">Mapa do Bairro</a>
            <a href="/#padarias" style="color: #C2B4A2;">Padarias Conveniadas</a>
            <a href="javascript:openLoginModal('padaria')" style="color: var(--crosta-clara);">Área do Padeiro</a>
        </div>
    </div>
</footer>

<script>
function openLoginModal(tab) {
    var modal = document.getElementById('modal-login');
    if (modal) {
        modal.classList.add('active');
        switchLoginTab(tab || 'cliente');
    }
}

function closeLoginModal() {
    var modal = document.getElementById('modal-login');
    if (modal) {
        modal.classList.remove('active');
    }
}

function switchLoginTab(tab) {
    var tabBtnCliente = document.getElementById('tab-btn-cliente');
    var tabBtnPadaria = document.getElementById('tab-btn-padaria');
    var tabCliente = document.getElementById('tab-cliente');
    var tabPadaria = document.getElementById('tab-padaria');

    if (tab === 'padaria') {
        tabBtnPadaria.classList.add('active');
        tabBtnCliente.classList.remove('active');
        tabPadaria.classList.add('active');
        tabCliente.classList.remove('active');
    } else {
        tabBtnCliente.classList.add('active');
        tabBtnPadaria.classList.remove('active');
        tabCliente.classList.add('active');
        tabPadaria.classList.remove('active');
    }
}

// Fechar com tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLoginModal();
});
</script>

</body>
</html>
