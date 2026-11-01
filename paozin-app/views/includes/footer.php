</main>

<!-- ================= MODAL UNIFICADO DE LOGIN ================= -->
<div id="modal-login" class="modal-overlay" onclick="if(event.target===this) closeLoginModal();">
    <div class="modal-card">
        <button class="modal-close" onclick="closeLoginModal();">&times;</button>
        
        <div style="text-align: center; margin-bottom: 20px;">
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

        <!-- ABA 2: PADARIA (sub-estados: login com telefone ou lista / signin/cadastro) -->
        <div id="tab-padaria" class="tab-pane">

            <!-- Mensagem de erro de senha/login -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'senha_incorreta'): ?>
                <div class="alerta alerta-erro" style="margin-bottom: 16px; padding: 10px 14px;">
                    <span style="font-weight: 900; color: var(--brasa);">!</span>
                    <div style="font-size: 13px;">
                        <strong>Senha Incorreta!</strong> Verifique a senha de acesso da sua padaria.
                    </div>
                </div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'erro_login_padaria'): ?>
                <div class="alerta alerta-erro" style="margin-bottom: 16px; padding: 10px 14px;">
                    <span style="font-weight: 900; color: var(--brasa);">!</span>
                    <div style="font-size: 13px;">
                        <strong>Padaria não encontrada!</strong> Confira o telefone ou selecione na lista.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Estado A: LOGIN PADARIA -->
            <div id="padaria-login-state">
                <form action="/login-padaria" method="POST" id="form-login-padaria">
                    <div class="input-group">
                        <label class="input-label">Telefone / WhatsApp da Padaria:</label>
                        <input type="tel" name="telefone" id="input-login-padaria-tel" class="input-field"
                               placeholder="Ex: 11988880001" autofocus>
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Digite o telefone cadastrado do seu estabelecimento.
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; margin: 14px 0;">
                        <div style="flex: 1; height: 1px; background: var(--linha);"></div>
                        <span style="font-size: 11px; color: var(--cinza); font-weight: 700; text-transform: uppercase;">ou selecione da lista</span>
                        <div style="flex: 1; height: 1px; background: var(--linha);"></div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Estabelecimentos Cadastrados:</label>
                        <select name="padaria_id" class="input-field" style="cursor: pointer;">
                            <option value="">Escolha sua padaria</option>
                            <?php 
                            try {
                                $listaPadarias = Padaria::listarTodas();
                                foreach ($listaPadarias as $pad) {
                                    echo '<option value="' . (int)$pad['id'] . '">' . htmlspecialchars($pad['nome']) . ' (' . htmlspecialchars($pad['telefone'] ?? 'sem tel') . ')</option>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="1">Padaria Fornada de Ouro</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Senha de Acesso da Padaria:</label>
                        <input type="password" name="senha" id="input-login-padaria-senha" class="input-field"
                               placeholder="Digite sua senha de acesso" value="paozin123" required>
                        <span style="font-size: 12px; color: var(--cinza); display: block; margin-top: 4px;">
                            Senha de segurança da padaria (Padrão: <code>paozin123</code>).
                        </span>
                    </div>

                    <button type="submit" class="btn-3d btn-fornada" style="width: 100%; margin-top: 16px;">
                        Entrar
                    </button>

                    <!-- Botão de Signin de destaque logo abaixo do Entrar -->
                    <button type="button" onclick="switchPadariaState('cadastro')" class="btn-3d btn-crosta" style="width: 100%; margin-top: 10px; padding: 12px 18px; font-size: 13.5px;">
                        Cadastrar
                    </button>
                </form>
            </div>

            <!-- Estado B: SIGNIN / CADASTRO DE PADARIA -->
            <div id="padaria-cadastro-state" style="display: none;">
                <div style="background: var(--miolo); border-radius: var(--r-md); padding: 12px 14px; margin-bottom: 16px; border-left: 3px solid var(--crosta);">
                    <p style="font-size: 12.5px; color: var(--carvao); line-height: 1.45;">
                        Cadastre sua padaria no bairro. Você poderá avisar os vizinhos instantaneamente a cada fornada!
                    </p>
                </div>

                <form action="/cadastrar-padaria" method="POST" id="form-cadastro-padaria">
                    <div class="input-group">
                        <label class="input-label">Nome da Padaria:</label>
                        <input type="text" name="nome" id="input-cadastro-padaria-nome" class="input-field"
                               placeholder="Ex: Padaria Estrela do Pão" required maxlength="100">
                    </div>

                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Telefone / WhatsApp da Padaria:</label>
                        <input type="tel" name="telefone" id="input-cadastro-padaria-tel" class="input-field"
                               placeholder="Ex: 11988880001" required>
                    </div>

                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Crie uma Senha de Acesso:</label>
                        <input type="password" name="senha" class="input-field"
                               placeholder="Ex: sua senha secreta" value="paozin123" required>
                        <span style="font-size: 11.5px; color: var(--cinza); display: block; margin-top: 4px;">
                            Utilizada pela sua equipe para entrar no painel.
                        </span>
                    </div>

                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Endereço Completo:</label>
                        <input type="text" name="endereco" class="input-field"
                               placeholder="Ex: Rua das Flores, 120 - Centro" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px;">
                        <div class="input-group">
                            <label class="input-label">Preço/Kg (R$):</label>
                            <input type="text" name="preco_pao_quilo" class="input-field"
                                   placeholder="Ex: 15.90" value="15.90" required>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Preço/Unid. (R$):</label>
                            <input type="text" name="preco_pao_unidade" class="input-field"
                                   placeholder="Ex: 0.95" value="0.95">
                        </div>
                    </div>

                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Horário de Funcionamento:</label>
                        <input type="text" name="horario_funcionamento" class="input-field"
                               placeholder="Ex: 06:00 às 20:00" value="06:00 às 20:00">
                    </div>

                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Horários Fixos de Fornada (opcional):</label>
                        <input type="text" name="horarios_fornada" class="input-field"
                               placeholder="Ex: 06:30, 16:30 (ou 'Sem horário fixo')">
                    </div>

                    <button type="submit" class="btn-3d btn-fornada" style="width: 100%; margin-top: 16px;">
                        Criar Cadastro da Padaria
                    </button>
                </form>

                <div style="margin-top: 16px; text-align: center;">
                    <p style="font-size: 13px; color: var(--cinza);">
                        Já tem cadastro?
                        <button type="button" onclick="switchPadariaState('login')"
                                style="background: none; border: none; cursor: pointer; color: var(--crosta-escura); font-weight: 700; font-size: 13px; font-family: inherit;">
                            Fazer login
                        </button>
                    </p>
                </div>
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
function openLoginModal(tab, state) {
    var modal = document.getElementById('modal-login');
    if (modal) {
        modal.classList.add('active');
        switchLoginTab(tab || 'cliente');
        if (tab === 'cliente' && state) {
            switchClienteState(state);
        } else if (tab === 'padaria' && state) {
            switchPadariaState(state);
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

// ---------- Sub-estados da aba padaria ----------
function switchPadariaState(state) {
    var loginState    = document.getElementById('padaria-login-state');
    var cadastroState = document.getElementById('padaria-cadastro-state');

    if (state === 'cadastro') {
        loginState.style.display    = 'none';
        cadastroState.style.display = 'block';
        setTimeout(function() {
            var el = document.getElementById('input-cadastro-padaria-nome');
            if (el) el.focus();
        }, 50);
    } else {
        loginState.style.display    = 'block';
        cadastroState.style.display = 'none';
        setTimeout(function() {
            var el = document.getElementById('input-login-padaria-tel');
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
    } else if (modal === 'padaria_cadastro') {
        openLoginModal('padaria', 'cadastro');
        if (tel) {
            var f = document.getElementById('input-cadastro-padaria-tel');
            if (f) f.value = tel;
        }
    } else if (modal === 'padaria') {
        openLoginModal('padaria', 'login');
        if (tel) {
            var f = document.getElementById('input-login-padaria-tel');
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
