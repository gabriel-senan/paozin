<?php

class Padaria {
    public static function buscarPorId(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM padarias WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function listarTodas(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM padarias WHERE status_assinatura = 'ativo' ORDER BY nota_media DESC");
        return $stmt->fetchAll();
    }

    /**
     * Retorna exatamente 4 padarias em destaque para o carrossel:
     *  - As 3 mais seguidas (maior nº de inscritos em padaria_cliente)
     *  - A 4ª mais recentemente cadastrada que não esteja nas 3 anteriores
     */
    public static function listarDestaques(): array {
        $db = Database::getConnection();

        // Top 3 mais seguidas
        $stmt = $db->query("
            SELECT p.*, COUNT(pc.id) AS total_inscritos
            FROM padarias p
            LEFT JOIN padaria_cliente pc ON pc.padaria_id = p.id
            WHERE p.status_assinatura = 'ativo'
            GROUP BY p.id
            ORDER BY total_inscritos DESC, p.nota_media DESC
            LIMIT 3
        ");
        $top3 = $stmt->fetchAll();

        $idsTop3 = array_column($top3, 'id');

        // A mais recente que não esteja no top 3
        $placeholders = count($idsTop3) > 0
            ? implode(',', array_fill(0, count($idsTop3), '?'))
            : '0';

        $stmt2 = $db->prepare("
            SELECT p.*, COUNT(pc.id) AS total_inscritos
            FROM padarias p
            LEFT JOIN padaria_cliente pc ON pc.padaria_id = p.id
            WHERE p.status_assinatura = 'ativo'
            AND p.id NOT IN ({$placeholders})
            GROUP BY p.id
            ORDER BY p.created_at DESC, p.id DESC
            LIMIT 1
        ");
        $stmt2->execute($idsTop3);
        $maisRecente = $stmt2->fetch();

        $destaques = $top3;
        if ($maisRecente) {
            $destaques[] = $maisRecente;
        }

        return $destaques;
    }

    public static function verificarCooldown(int $id, int $limiteMinutos = 30): array {
        $padaria = self::buscarPorId($id);
        if (!$padaria) {
            return ['em_cooldown' => false, 'minutos_restantes' => 0];
        }

        if (empty($padaria['ultima_fornada'])) {
            return ['em_cooldown' => false, 'minutos_restantes' => 0];
        }

        $timestampUltima = strtotime($padaria['ultima_fornada']);
        $minutosPassados = (time() - $timestampUltima) / 60;

        if ($minutosPassados < $limiteMinutos) {
            return [
                'em_cooldown' => true,
                'minutos_restantes' => (int)ceil($limiteMinutos - $minutosPassados),
                'ultima_fornada' => $padaria['ultima_fornada']
            ];
        }

        return ['em_cooldown' => false, 'minutos_restantes' => 0];
    }

    public static function registrarFornada(int $id, float $preco): bool {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Atualiza carimbo na padaria
            $stmt1 = $db->prepare("UPDATE padarias SET ultima_fornada = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt1->execute([$id]);

            // Conta total de assinantes no momento
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM padaria_cliente pc INNER JOIN clientes c ON c.id = pc.cliente_id WHERE pc.padaria_id = ? AND c.ativo = 1");
            $stmtCount->execute([$id]);
            $totalAvisados = (int)$stmtCount->fetchColumn();

            // Grava histórico da fornada
            $stmt2 = $db->prepare("INSERT INTO fornadas (padaria_id, preco_quilo_momento, total_avisados) VALUES (?, ?, ?)");
            $stmt2->execute([$id, $preco, $totalAvisados]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function buscarPorTelefone(string $telefone): ?array {
        $db = Database::getConnection();
        $clean = preg_replace('/\D/', '', $telefone);
        if (strlen($clean) >= 8) {
            $lastDigits = substr($clean, -8);
            $stmt = $db->prepare("SELECT * FROM padarias WHERE telefone = ? OR replace(replace(replace(replace(telefone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ? LIMIT 1");
            $stmt->execute([$clean, '%' . $lastDigits]);
            $res = $stmt->fetch();
            if ($res) return $res;
        }

        $stmt = $db->prepare("SELECT * FROM padarias WHERE telefone = ? LIMIT 1");
        $stmt->execute([$clean]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function cadastrar(array $dados): ?int {
        $db = Database::getConnection();
        $nome = trim($dados['nome'] ?? '');
        $endereco = trim($dados['endereco'] ?? '');
        $telefone = preg_replace('/\D/', '', $dados['telefone'] ?? '');
        $precoQuilo = (float)($dados['preco_pao_quilo'] ?? 14.90);
        $precoUnidade = isset($dados['preco_pao_unidade']) && $dados['preco_pao_unidade'] !== '' ? (float)$dados['preco_pao_unidade'] : null;
        $horarioFunc = trim($dados['horario_funcionamento'] ?? '06:00 às 20:00');
        $horariosFornada = trim($dados['horarios_fornada'] ?? '06:30, 16:30');
        $senha = trim($dados['senha'] ?? 'paozin123');
        $latitude = !empty($dados['latitude']) ? (float)$dados['latitude'] : null;
        $longitude = !empty($dados['longitude']) ? (float)$dados['longitude'] : null;

        // Se latitude e longitude não foram enviadas explicitamente, geocodifica pelo endereço
        if ($latitude === null || $longitude === null) {
            $coords = self::geocodificarEndereco($endereco);
            $latitude = $coords['lat'];
            $longitude = $coords['lng'];
        }

        if (empty($nome) || empty($telefone)) {
            return null;
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO padarias (
                    nome, endereco, telefone, preco_pao_quilo, preco_pao_unidade,
                    horario_funcionamento, horarios_fornada, senha, latitude, longitude,
                    nota_media, status_assinatura
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 5.0, 'ativo')
            ");
            $stmt->execute([
                $nome,
                $endereco ?: 'Endereço a definir',
                $telefone,
                $precoQuilo,
                $precoUnidade,
                $horarioFunc,
                $horariosFornada,
                $senha ?: 'paozin123',
                $latitude,
                $longitude
            ]);
            return (int)$db->lastInsertId();
        } catch (Exception $e) {
            return null;
        }
    }

    public static function geocodificarEndereco(string $endereco): array {
        $limpo = trim($endereco);
        if (empty($limpo)) {
            return ['lat' => -23.55052, 'lng' => -46.63330];
        }

        // Se não tiver menção a SP/São Paulo, adiciona para melhorar precisão
        $termo = $limpo;
        if (!stripos($termo, 'são paulo') && !stripos($termo, 'sp') && !stripos($termo, 'capão redondo')) {
            $termo .= ', São Paulo, SP, Brasil';
        } elseif (!stripos($termo, 'brasil')) {
            $termo .= ', Brasil';
        }

        try {
            $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' . urlencode($termo);
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: PaozinApp/1.0\r\n",
                    'timeout' => 3
                ]
            ]);
            $res = @file_get_contents($url, false, $context);
            if ($res) {
                $json = json_decode($res, true);
                if (!empty($json[0]['lat']) && !empty($json[0]['lon'])) {
                    return [
                        'lat' => (float)$json[0]['lat'],
                        'lng' => (float)$json[0]['lon']
                    ];
                }
            }
        } catch (Exception $e) {
            // fallback
        }

        return ['lat' => -23.55052, 'lng' => -46.63330];
    }

    public static function atualizarDados(int $id, array $dados): bool {
        $db = Database::getConnection();
        $nome = trim($dados['nome'] ?? '');
        $endereco = trim($dados['endereco'] ?? '');
        $telefone = preg_replace('/\D/', '', $dados['telefone'] ?? '');
        $precoQuilo = (float)($dados['preco_pao_quilo'] ?? 14.90);
        $precoUnidade = isset($dados['preco_pao_unidade']) && $dados['preco_pao_unidade'] !== '' ? (float)$dados['preco_pao_unidade'] : null;
        $horarioFunc = trim($dados['horario_funcionamento'] ?? '');
        $horariosFornada = trim($dados['horarios_fornada'] ?? '');
        $senha = trim($dados['senha'] ?? '');
        $latitude = !empty($dados['latitude']) ? (float)$dados['latitude'] : null;
        $longitude = !empty($dados['longitude']) ? (float)$dados['longitude'] : null;

        // Se o endereço foi fornecido e as coordenadas não, busca novas coordenadas
        if (!empty($endereco) && ($latitude === null || $longitude === null)) {
            $coords = self::geocodificarEndereco($endereco);
            $latitude = $coords['lat'];
            $longitude = $coords['lng'];
        }

        if (empty($nome)) {
            return false;
        }

        try {
            if (!empty($senha)) {
                $stmt = $db->prepare("
                    UPDATE padarias SET
                        nome = ?,
                        endereco = ?,
                        telefone = ?,
                        preco_pao_quilo = ?,
                        preco_pao_unidade = ?,
                        horario_funcionamento = ?,
                        horarios_fornada = ?,
                        senha = ?,
                        latitude = COALESCE(?, latitude),
                        longitude = COALESCE(?, longitude),
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $nome,
                    $endereco,
                    $telefone,
                    $precoQuilo,
                    $precoUnidade,
                    $horarioFunc,
                    $horariosFornada,
                    $senha,
                    $latitude,
                    $longitude,
                    $id
                ]);
            } else {
                $stmt = $db->prepare("
                    UPDATE padarias SET
                        nome = ?,
                        endereco = ?,
                        telefone = ?,
                        preco_pao_quilo = ?,
                        preco_pao_unidade = ?,
                        horario_funcionamento = ?,
                        horarios_fornada = ?,
                        latitude = COALESCE(?, latitude),
                        longitude = COALESCE(?, longitude),
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $nome,
                    $endereco,
                    $telefone,
                    $precoQuilo,
                    $precoUnidade,
                    $horarioFunc,
                    $horariosFornada,
                    $latitude,
                    $longitude,
                    $id
                ]);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function validarSenha(array $padaria, string $senhaDigitada): bool {
        $senhaBanco = trim($padaria['senha'] ?? '');
        if (empty($senhaBanco)) {
            // Se ainda não tinha senha configurada, aceita o padrão paozin123
            return $senhaDigitada === 'paozin123';
        }
        return $senhaBanco === trim($senhaDigitada);
    }
}
