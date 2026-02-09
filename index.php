<?php
declare(strict_types=1);

session_start();
header('Content-Type: text/html; charset=utf-8');

// ===== CONFIG DB (REMOTO) =====
$DB_HOST = "172.19.16.15";
$DB_PORT = "3306";
$DB_NAME = "cehab_online";
$DB_USER = "siscreche";
$DB_PASS = "Cehab@123_";

// 1) pega token (GET ou cookie)
$token = trim($_GET['token'] ?? ($_COOKIE['token'] ?? ''));

if ($token === '') {
  http_response_code(401);
  exit("Acesso negado: token ausente.");
}

try {
  $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $sql = "
    SELECT u.u_nome_completo, t.g_id, t.u_rede
    FROM token_sessao t
    JOIN users u ON u.g_id = t.g_id
    WHERE t.token = :token
    LIMIT 1
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':token' => $token]);
  $row = $stmt->fetch();

  if (!$row) {
    http_response_code(401);
    exit("Acesso negado: token inválido ou expirado.");
  }

  // salva na sessão
  $_SESSION['token']       = $token;
  $_SESSION['g_id']        = $row['g_id'];
  $_SESSION['u_rede']      = $row['u_rede'];
  $_SESSION['responsavel'] = $row['u_nome_completo'] ?? 'Desconhecido';

  // opcional: grava cookie pro usuário não precisar ficar com token na URL
  setcookie('token', $token, time() + 60*60*8, "/"); // 8h

  // redireciona para o formulário
  header("Location: secao_01.php");
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  exit("Erro interno ao validar usuário. " . $e->getMessage());
}
