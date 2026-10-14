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

        <!-- ABA 1: CLIENTE (sub-estados: login / cadastro) -->
        <div id="tab-cliente" class="tab-pane active">

            <!-- Estado A: LOGIN -->
            <div id="cliente-login-state">
                <form action="/login-cliente" method="POST" id="form-login-cliente">
                    <div class="input-group">
                        <label class="input-label">Seu WhatsApp (com DDD):</label>
                        <input type="tel" name="whatsapp" id="input-login-tel" class="input-field"
                               placeholder="Ex: 11999990001" required autofocus>
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Digite o número vinculado à sua conta.
                        </span>
                    </div>
                    <button type="submit" class="btn-3d btn-azul" style="width: 100%; margin-top: 10px;">
                        Entrar
                    </button>
                </form>
                <div style="margin-top: 18px; text-align: center;">
                    <p style="font-size: 13px; color: var(--cinza);">
                        Primeira vez aqui?
                        <button type="button" onclick="switchClienteState('cadastro')"
                                style="background: none; border: none; cursor: pointer; color: var(--crosta-escura); font-weight: 700; font-size: 13px; font-family: inherit;">
                            Criar conta grátis
                        </button>
                    </p>
                </div>
            </div>

            <!-- Estado B: CADASTRO (nome + telefone) -->
            <div id="cliente-cadastro-state" style="display: none;">
                <div style="background: var(--miolo); border-radius: var(--r-md); padding: 14px 16px; margin-bottom: 20px; border-left: 3px solid var(--crosta);">
                    <p style="font-size: 13px; color: var(--carvao); line-height: 1.5;">
                        Cadastro simples e gratuito. Você vai receber avisos de pão quentinho direto no seu WhatsApp.
                    </p>
                </div>
                <form action="/cadastrar-cliente" method="POST" id="form-cadastro-cliente">
                    <div class="input-group">
                        <label class="input-label">Seu nome:</label>
                        <input type="text" name="nome" id="input-cadastro-nome" class="input-field"
                               placeholder="Ex: João da Silva" required maxlength="80">
                    </div>
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Seu WhatsApp (com DDD):</label>
                        <input type="tel" name="whatsapp" id="input-cadastro-tel" class="input-field"
                               placeholder="Ex: 11999990001" required>
                    </div>
                    <button type="submit" class="btn-3d btn-fornada" style="width: 100%; margin-top: 16px;">
                        Criar minha conta
                    </button>
                </form>
                <div style="margin-top: 18px; text-align: center;">
                    <p style="font-size: 13px; color: var(--cinza);">
                        Já tem conta?
                        <button type="button" onclick="switchClienteState('login')"
                                style="background: none; border: none; cursor: pointer; color: var(--crosta-escura); font-weight: 700; font-size: 13px; font-family: inherit;">
                            Fazer login
                        </button>
                    </p>
                </div>
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

                <div class="input-group" style="margin-top: 14px;">
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
            Foodtech Hiperlocal &bull; Sistema Inteligente de Aviso de Fornada &bull; Conectado à Evolution API &amp; n8n
        </p>
        <div style="margin-top: 24px; display: flex; justify-content: center; gap: 20px; font-size: 13px; flex-wrap: wrap;">
            <a href="/#como-funciona" style="color: #C2B4A2;">Como Funciona</a>
            <a href="/#mapa" style="color: #C2B4A2;">Mapa do Bairro</a>
            <a href="/#padarias" style="color: #C2B4A2;">Padarias Conveniadas</a>
            <a href="javascript:openLoginModal('padaria')" style="color: var(--crosta-clara);">Área do Padeiro</a>
        </div>
    </div>
</footer>

<script>
// ---------- Controle do modal ----------
function openLoginModal(tab, clienteState) {
    var modal = document.getElementById('modal-login');
    if (modal) {
        modal.classList.add('active');
        switchLoginTab(tab || 'cliente');
        if (tab === 'cliente' && clienteState) {
            switchClienteState(clienteState);
        }
    }
}

function closeLoginModal() {
    var modal = document.getElementById('modal-login');
    if (modal) modal.classList.remove('active');
}

function switchLoginTab(tab) {
    var tabBtnCliente = document.getElementById('tab-btn-cliente');
    var tabBtnPadaria = document.getElementById('tab-btn-padaria');
    var tabCliente    = document.getElementById('tab-cliente');
    var tabPadaria    = document.getElementById('tab-padaria');

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

// ---------- Sub-estados da aba cliente ----------
function switchClienteState(state) {
    var loginState    = document.getElementById('cliente-login-state');
    var cadastroState = document.getElementById('cliente-cadastro-state');

    if (state === 'cadastro') {
        loginState.style.display    = 'none';
        cadastroState.style.display = 'block';
        setTimeout(function() {
            var el = document.getElementById('input-cadastro-nome');
            if (el) el.focus();
        }, 50);
    } else {
        loginState.style.display    = 'block';
        cadastroState.style.display = 'none';
        setTimeout(function() {
            var el = document.getElementById('input-login-tel');
            if (el) el.focus();
        }, 50);
    }
}

// ---------- Auto-abertura por query param ----------
(function () {
    var params = new URLSearchParams(window.location.search);
    var modal  = params.get('modal');
    var tel    = params.get('tel') || '';

    if (modal === 'cadastro') {
        openLoginModal('cliente', 'cadastro');
        if (tel) {
            var f = document.getElementById('input-cadastro-tel');
            if (f) f.value = tel;
        }
    } else if (modal === 'cliente') {
        openLoginModal('cliente', 'login');
        if (tel) {
            var f = document.getElementById('input-login-tel');
            if (f) f.value = tel;
        }
    }
})();

// Fechar com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLoginModal();
});
</script>

</body>
</html>
