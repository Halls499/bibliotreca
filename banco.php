<?php
// ========================================
// 📚 SISTEMA DE BIBLIOTECA (SEM INSERTS AUTOMÁTICOS)
// ========================================

// 1️⃣ CONEXÃO
$host = "localhost";
$usuario = "root";
$senha = "";
$conexao = new mysqli($host, $usuario, $senha);
if ($conexao->connect_error) die("Erro: " . $conexao->connect_error);

// 2️⃣ BANCO DE DADOS
$conexao->query("CREATE DATABASE IF NOT EXISTS biblioteca");
$conexao->select_db("biblioteca");

// 3️⃣ TABELAS
$tabelas = [
"CREATE TABLE IF NOT EXISTS autor (
  id_autor INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  nacionalidade VARCHAR(50)
)",
"CREATE TABLE IF NOT EXISTS livro (
  id_livro INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100) NOT NULL,
  ano_publicacao INT,
  id_autor INT,
  FOREIGN KEY (id_autor) REFERENCES autor(id_autor)
)",
"CREATE TABLE IF NOT EXISTS pessoa (
  id_pessoa INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20),
  email VARCHAR(100)
)",
"CREATE TABLE IF NOT EXISTS emprestimo (
  id_emprestimo INT AUTO_INCREMENT PRIMARY KEY,
  id_livro INT NOT NULL,
  id_pessoa INT NOT NULL,
  data_emprestimo DATE NOT NULL,
  data_prevista_devolucao DATE,
  FOREIGN KEY (id_livro) REFERENCES livro(id_livro),
  FOREIGN KEY (id_pessoa) REFERENCES pessoa(id_pessoa)
)"
];
foreach ($tabelas as $sql) $conexao->query($sql);

// 4️⃣ FORMULÁRIOS DE CADASTRO
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['novo_autor'])) {
        $nome = $_POST['nome_autor'];
        $nac = $_POST['nacionalidade'];
        $conexao->query("INSERT INTO autor (nome, nacionalidade) VALUES ('$nome', '$nac')");
    } elseif (isset($_POST['novo_livro'])) {
        $titulo = $_POST['titulo'];
        $ano = $_POST['ano_publicacao'];
        $autor = $_POST['id_autor'];
        $conexao->query("INSERT INTO livro (titulo, ano_publicacao, id_autor)
                         VALUES ('$titulo', '$ano', '$autor')");
    } elseif (isset($_POST['nova_pessoa'])) {
        $nome = $_POST['nome_pessoa'];
        $tel = $_POST['telefone'];
        $email = $_POST['email'];
        $conexao->query("INSERT INTO pessoa (nome, telefone, email)
                         VALUES ('$nome', '$tel', '$email')");
    } elseif (isset($_POST['novo_emprestimo'])) {
        $livro = $_POST['id_livro'];
        $pessoa = $_POST['id_pessoa'];
        $data = $_POST['data_emprestimo'];
        $prev = $_POST['data_prevista_devolucao'];
        $conexao->query("INSERT INTO emprestimo (id_livro, id_pessoa, data_emprestimo, data_prevista_devolucao)
                         VALUES ('$livro', '$pessoa', '$data', '$prev')");
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Biblioteca</title>
<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 20px; }
h1 { text-align: center; }
h2 { margin-top: 40px; color: #333; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; background: #fff; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #eee; }
form { background: #fff; padding: 10px; margin-top: 10px; border: 1px solid #ccc; }
input, select, button { padding: 5px; margin: 3px; }
button { background: #007bff; color: white; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
</style>
</head>
<body>
<h1>📚 Sistema de Biblioteca</h1>

<!-- ==================== FORMULÁRIOS ==================== -->
<h2>Adicionar Autor</h2>
<form method="post">
    <input type="hidden" name="novo_autor">
    <input type="text" name="nome_autor" placeholder="Nome" required>
    <input type="text" name="nacionalidade" placeholder="Nacionalidade">
    <button type="submit">Salvar</button>
</form>

<h2>Adicionar Livro</h2>
<form method="post">
    <input type="hidden" name="novo_livro">
    <input type="text" name="titulo" placeholder="Título" required>
    <input type="number" name="ano_publicacao" placeholder="Ano">
    <select name="id_autor" required>
        <option value="">Selecione o autor</option>
        <?php
        $res = $conexao->query("SELECT * FROM autor");
        while ($a = $res->fetch_assoc()) echo "<option value='{$a['id_autor']}'>{$a['nome']}</option>";
        ?>
    </select>
    <button type="submit">Salvar</button>
</form>

<h2>Adicionar Pessoa</h2>
<form method="post">
    <input type="hidden" name="nova_pessoa">
    <input type="text" name="nome_pessoa" placeholder="Nome" required>
    <input type="text" name="telefone" placeholder="Telefone">
    <input type="email" name="email" placeholder="Email">
    <button type="submit">Salvar</button>
</form>

<h2>Registrar Empréstimo</h2>
<form method="post">
    <input type="hidden" name="novo_emprestimo">
    <select name="id_livro" required>
        <option value="">Selecione o livro</option>
        <?php
        $res = $conexao->query("SELECT * FROM livro");
        while ($l = $res->fetch_assoc()) echo "<option value='{$l['id_livro']}'>{$l['titulo']}</option>";
        ?>
    </select>
    <select name="id_pessoa" required>
        <option value="">Selecione a pessoa</option>
        <?php
        $res = $conexao->query("SELECT * FROM pessoa");
        while ($p = $res->fetch_assoc()) echo "<option value='{$p['id_pessoa']}'>{$p['nome']}</option>";
        ?>
    </select>
    <input type="date" name="data_emprestimo" required>
    <input type="date" name="data_prevista_devolucao">
    <button type="submit">Salvar</button>
</form>

<!-- ==================== LISTAGENS ==================== -->
<h2>Autores</h2>
<table>
<tr><th>ID</th><th>Nome</th><th>Nacionalidade</th></tr>
<?php
$res = $conexao->query("SELECT * FROM autor");
while ($a = $res->fetch_assoc()) echo "<tr><td>{$a['id_autor']}</td><td>{$a['nome']}</td><td>{$a['nacionalidade']}</td></tr>";
?>
</table>

<h2>Livros</h2>
<table>
<tr><th>ID</th><th>Título</th><th>Ano</th><th>Autor</th></tr>
<?php
$res = $conexao->query("SELECT l.*, a.nome AS autor FROM livro l JOIN autor a ON l.id_autor = a.id_autor");
while ($l = $res->fetch_assoc())
    echo "<tr><td>{$l['id_livro']}</td><td>{$l['titulo']}</td><td>{$l['ano_publicacao']}</td><td>{$l['autor']}</td></tr>";
?>
</table>

<h2>Pessoas</h2>
<table>
<tr><th>ID</th><th>Nome</th><th>Telefone</th><th>Email</th></tr>
<?php
$res = $conexao->query("SELECT * FROM pessoa");
while ($p = $res->fetch_assoc())
    echo "<tr><td>{$p['id_pessoa']}</td><td>{$p['nome']}</td><td>{$p['telefone']}</td><td>{$p['email']}</td></tr>";
?>
</table>

<h2>Empréstimos</h2>
<table>
<tr><th>ID</th><th>Livro</th><th>Pessoa</th><th>Data</th><th>Devolução Prevista</th></tr>
<?php
$res = $conexao->query("SELECT e.*, l.titulo, p.nome
                        FROM emprestimo e
                        JOIN livro l ON e.id_livro = l.id_livro
                        JOIN pessoa p ON e.id_pessoa = p.id_pessoa");
while ($e = $res->fetch_assoc())
    echo "<tr><td>{$e['id_emprestimo']}</td><td>{$e['titulo']}</td><td>{$e['nome']}</td>
          <td>{$e['data_emprestimo']}</td><td>{$e['data_prevista_devolucao']}</td></tr>";
?>
</table>
</body>
</html>
