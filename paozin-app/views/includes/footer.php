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

                <?php if (isset($_GET['status']) && $_GET['status'] === 'senha_incorreta'): ?>
                    <div class="alerta alerta-erro" style="margin-bottom: 14px; padding: 10px 14px;">
                        <span style="font-weight: 900; color: var(--brasa);">!</span>
                        <div style="font-size: 13px;"><strong>Senha incorreta.</strong> Verifique e tente novamente.</div>
                    </div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'login_necessario'): ?>
                    <div class="alerta alerta-aviso" style="margin-bottom: 14px; padding: 10px 14px;">
                        <span style="font-weight: 900; color: var(--crosta-escura);">!</span>
                        <div style="font-size: 13px;">Faça login para acessar sua conta.</div>
                    </div>
                <?php endif; ?>

                <form action="/login-cliente" method="POST" id="form-login-cliente">
                    <div class="input-group">
                        <label class="input-label">Seu WhatsApp (com DDD):</label>
                        <input type="tel" name="whatsapp" id="input-login-tel" class="input-field"
                               placeholder="Ex: 11999990001" required autofocus
                               value="<?= htmlspecialchars($_GET['tel'] ?? '') ?>">
                    </div>

                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Senha:</label>
                        <div style="position: relative;">
                            <input type="password" name="senha" id="input-login-cliente-senha" class="input-field"
                                   placeholder="Digite sua senha" required style="padding-right: 44px;">
                            <button type="button" onclick="toggleVisibilidadeSenha('input-login-cliente-senha', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <label style="display: flex; align-items: center; gap: 10px; margin-top: 14px; cursor: pointer; font-size: 13.5px; color: var(--carvao); user-select: none;">
                        <input type="checkbox" name="lembrar_me" value="1"
                               style="width: 16px; height: 16px; accent-color: var(--crosta); cursor: pointer;">
                        Lembrar-me por 7 dias
                    </label>

                    <button type="submit" class="btn-3d btn-azul" style="width: 100%; margin-top: 16px;">
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

            <!-- Estado B: CADASTRO (nome + telefone + senha) -->
            <div id="cliente-cadastro-state" style="display: none;">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'senha_curta'): ?>
                    <div class="alerta alerta-erro" style="margin-bottom: 14px; padding: 10px 14px;">
                        <span style="font-weight: 900; color: var(--brasa);">!</span>
                        <div style="font-size: 13px;">A senha precisa ter pelo menos 4 caracteres.</div>
                    </div>
                <?php endif; ?>
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
                    <div class="input-group" style="margin-top: 14px;">
                        <label class="input-label">Crie uma Senha:</label>
                        <div style="position: relative;">
                            <input type="password" name="senha" id="input-cadastro-cliente-senha" class="input-field"
                                   placeholder="Mínimo 4 caracteres" required minlength="4" style="padding-right: 44px;">
                            <button type="button" onclick="toggleVisibilidadeSenha('input-cadastro-cliente-senha', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
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

            <!-- Estado C: DEFINIR SENHA (conta sem senha) -->
            <div id="cliente-definir-senha-state" style="display: none;">
                <?php if (isset($_GET['status']) && in_array($_GET['status'], ['sem_senha', 'senha_curta_def'])): ?>
                    <div class="alerta alerta-<?= $_GET['status'] === 'senha_curta_def' ? 'erro' : 'aviso' ?>" style="margin-bottom: 14px; padding: 10px 14px;">
                        <span style="font-weight: 900; color: var(--<?= $_GET['status'] === 'senha_curta_def' ? 'brasa' : 'crosta-escura' ?>);">!</span>
                        <div style="font-size: 13px;">
                            <?= $_GET['status'] === 'senha_curta_def'
                                ? 'A senha precisa ter pelo menos 4 caracteres.'
                                : 'Esta conta ainda não tem senha. Crie uma agora para continuar.' ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div style="background: var(--miolo); border-radius: var(--r-md); padding: 14px 16px; margin-bottom: 18px; border-left: 3px solid var(--crosta);">
                    <p style="font-size: 13px; color: var(--carvao); line-height: 1.5;">
                        Encontramos sua conta pelo WhatsApp. Crie uma senha para acessá-la com segurança.
                    </p>
                </div>
                <form action="/definir-senha-cliente" method="POST" id="form-definir-senha-cliente">
                    <input type="hidden" name="whatsapp" id="input-definir-senha-tel" value="<?= htmlspecialchars($_GET['tel'] ?? '') ?>">
                    <div class="input-group">
                        <label class="input-label">Nova Senha:</label>
                        <div style="position: relative;">
                            <input type="password" name="senha" id="input-nova-senha-cliente" class="input-field"
                                   placeholder="Mínimo 4 caracteres" required minlength="4" style="padding-right: 44px;">
                            <button type="button" onclick="toggleVisibilidadeSenha('input-nova-senha-cliente', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-3d btn-fornada" style="width: 100%; margin-top: 14px;">
                        Criar senha e entrar
                    </button>
                </form>
                <div style="margin-top: 16px; text-align: center;">
                    <button type="button" onclick="switchClienteState('login')"
                            style="background: none; border: none; cursor: pointer; color: var(--cinza); font-size: 13px; font-family: inherit;">
                        ← Voltar ao login
                    </button>
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
                        <div style="position: relative;">
                            <input type="password" name="senha" id="input-login-padaria-senha" class="input-field"
                                   placeholder="Digite sua senha de acesso" value="paozin123" required style="padding-right: 44px;">
                            <button type="button" onclick="toggleVisibilidadeSenha('input-login-padaria-senha', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
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
                <!-- Alerta se senhas não coincidirem -->
                <?php if (isset($_GET['status']) && $_GET['status'] === 'senhas_diferentes'): ?>
                    <div class="alerta alerta-erro" style="margin-bottom: 14px; padding: 10px 14px;">
                        <span style="font-weight: 900; color: var(--brasa);">!</span>
                        <div style="font-size: 13px;">
                            <strong>As senhas não coincidem!</strong> Digite a mesma senha nos dois campos.
                        </div>
                    </div>
                <?php endif; ?>

                <div style="background: var(--miolo); border-radius: var(--r-md); padding: 12px 14px; margin-bottom: 16px; border-left: 3px solid var(--crosta);">
                    <p style="font-size: 12.5px; color: var(--carvao); line-height: 1.45;">
                        Cadastre sua padaria no bairro. Você poderá avisar os vizinhos instantaneamente a cada fornada!
                    </p>
                </div>

                <form action="/cadastrar-padaria" method="POST" id="form-cadastro-padaria" onsubmit="return validarSenhasPadaria();">
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

                    <!-- Senha com olho para exibir/ocultar -->
                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Crie uma Senha de Acesso:</label>
                        <div style="position: relative;">
                            <input type="password" name="senha" id="cad-senha-padaria" class="input-field"
                                   placeholder="Digite sua senha" style="padding-right: 44px;" required minlength="4">
                            <button type="button" onclick="toggleVisibilidadeSenha('cad-senha-padaria', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar senha com olho para exibir/ocultar -->
                    <div class="input-group" style="margin-top: 12px;">
                        <label class="input-label">Confirme a Senha:</label>
                        <div style="position: relative;">
                            <input type="password" name="confirmar_senha" id="cad-confirma-senha-padaria" class="input-field"
                                   placeholder="Repita a senha digitada" style="padding-right: 44px;" required minlength="4">
                            <button type="button" onclick="toggleVisibilidadeSenha('cad-confirma-senha-padaria', this)"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--cinza); padding: 6px; display: flex; align-items: center; justify-content: center;"
                                    title="Exibir/Ocultar senha">
                                <svg class="eye-open" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <span id="msg-erro-senha" style="font-size: 11.5px; color: var(--brasa); display: none; margin-top: 4px; font-weight: 700;">
                            As senhas não coincidem.
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
    var loginState       = document.getElementById('cliente-login-state');
    var cadastroState    = document.getElementById('cliente-cadastro-state');
    var definirSenhaState = document.getElementById('cliente-definir-senha-state');

    loginState.style.display        = 'none';
    cadastroState.style.display     = 'none';
    if (definirSenhaState) definirSenhaState.style.display = 'none';

    if (state === 'cadastro') {
        cadastroState.style.display = 'block';
        setTimeout(function() {
            var el = document.getElementById('input-cadastro-nome');
            if (el) el.focus();
        }, 50);
    } else if (state === 'definir-senha') {
        if (definirSenhaState) {
            definirSenhaState.style.display = 'block';
            setTimeout(function() {
                var el = document.getElementById('input-nova-senha-cliente');
                if (el) el.focus();
            }, 50);
        }
    } else {
        loginState.style.display = 'block';
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
        var status = params.get('status');
        if (status === 'sem_senha' || status === 'senha_curta_def') {
            switchClienteState('definir-senha');
            var fTel = document.getElementById('input-definir-senha-tel');
            if (fTel && tel) fTel.value = tel;
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

// ---------- Olho para exibir/ocultar senha ----------
function toggleVisibilidadeSenha(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;

    var eyeOpen = btn.querySelector('.eye-open');
    var eyeClosed = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        if (eyeOpen) eyeOpen.style.display = 'none';
        if (eyeClosed) eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        if (eyeOpen) eyeOpen.style.display = 'block';
        if (eyeClosed) eyeClosed.style.display = 'none';
    }
}

// ---------- Validação de confirmação de senha ----------
function validarSenhasPadaria() {
    var s1 = document.getElementById('cad-senha-padaria');
    var s2 = document.getElementById('cad-confirma-senha-padaria');
    var msg = document.getElementById('msg-erro-senha');

    if (s1 && s2 && s1.value !== s2.value) {
        if (msg) msg.style.display = 'block';
        s2.focus();
        return false;
    }

    if (msg) msg.style.display = 'none';
    return true;
}

// Fechar com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLoginModal();
});
</script>

</body>
</html>
