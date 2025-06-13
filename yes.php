<?php

// Configurações do Banco de Dados
$dbHost = 'localhost';
$dbName = 'seu_banco_de_dados';
$dbUser = 'seu_usuario_db';
$dbPass = 'sua_senha_db';

// Conexão com o Banco de Dados
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// Dados do formulário (exemplo)
$username_input = "usuario_teste";
$password_input = "senha123"; // A senha digitada pelo usuário

// O hash real que estaria no banco de dados para 'usuario_teste'
// No mundo real, você recuperaria isso do DB
$hashed_password_from_db = password_hash("senha123", PASSWORD_DEFAULT);

// 1. Preparar a consulta SQL
// Usamos APENAS o username aqui para buscar o hash da senha,
// pois a senha não deve ser usada diretamente na busca do DB.
// A verificação da senha (password_verify) é feita DEPOIS de obter o hash.
$stmt = mysqli_prepare($conn, "SELECT id, username, senha FROM usuarios WHERE username = ?");

if ($stmt) {
    // 2. Vincular o parâmetro (apenas o username, que é uma string)
    mysqli_stmt_bind_param($stmt, "s", $username_input); // 's' para string

    // 3. Executar a consulta
    mysqli_stmt_execute($stmt);

    // 4. Armazenar o resultado
    mysqli_stmt_store_result($stmt);

    // 5. Verificar se um usuário foi encontrado
    if (mysqli_stmt_num_rows($stmt) == 1) {
        // 6. Vincular as variáveis de resultado
        mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_hashed_password);

        // 7. Obter os valores do resultado
        mysqli_stmt_fetch($stmt);

        // 8. Verificar a senha usando password_verify()
        if (password_verify($password_input, $db_hashed_password)) {
            echo "Login bem-sucedido para o usuário: " . htmlspecialchars($db_username) . "<br>";
            // Aqui você iniciaria a sessão, etc.
        } else {
            echo "Senha incorreta.<br>";
        }
    } else {
        echo "Usuário não encontrado.<br>";
    }

    // 9. Fechar o statement
    mysqli_stmt_close($stmt);
} else {
    echo "Erro na preparação da consulta: " . mysqli_error($conn) . "<br>";
}

// Fechar a conexão com o banco de dados
mysqli_close($conn);

?>
