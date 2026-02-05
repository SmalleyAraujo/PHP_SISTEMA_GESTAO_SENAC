<?php
session_start();
require_once "../conexao.php";

// Classe para manipular a edição de dados na tabela 'sala'
class SalaHandler {
    private $conn;

    // Construtor que recebe uma conexão com o banco de dados
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para atualizar os dados da sala no banco de dados
    public function atualizarSala($sala_codigo, $sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $sala_descricao, $sala_capacidade_alunos) {
        // Converte a matriz de descrição da sala em uma string antes de usá-la na declaração SQL
        $descricao = implode(", ", $sala_descricao);

        // Prepara a declaração SQL para atualizar os dados da sala
        $sql = "UPDATE sala SET sala_nome = ?, sala_endereco = ?, sala_tipo = ?, sala_situacao = ?, sala_descricao = ?, sala_capacidade_alunos = ? WHERE sala_codigo = ?";
        $stmt = $this->conn->prepare($sql);
        
        // Executa a atualização
        $stmt->bind_param("sssssis", $sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $descricao, $sala_capacidade_alunos, $sala_codigo);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}

// Verifica se os dados do formulário foram submetidos via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $sala_codigo = $_POST["sala_codigo"];
    $sala_nome = $_POST["sala_nome"];
    $sala_endereco = $_POST["sala_endereco"];
    $sala_tipo = $_POST["sala_tipo"];
    $sala_situacao = $_POST["sala_situacao"];
    $sala_capacidade_alunos = $_POST["sala_capacidade_alunos"];
    $sala_descricao = $_POST["sala_descricao"];

    // Cria uma instância da classe SalaHandler
    $salaHandler = new SalaHandler($conn);

    // Chama o método para atualizar a sala
    if ($salaHandler->atualizarSala($sala_codigo, $sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $sala_descricao, $sala_capacidade_alunos)) {
        // Mensagem de sucesso
        echo "<p>Sala atualizada com sucesso!</p>";
    } else {
        // Mensagem de erro
        echo "<p>Ocorreu um erro ao atualizar a sala.</p>";
    }
    // Finaliza o script para evitar que o restante do código HTML seja processado
    exit();
}

// Verifica se um ID foi passado como parâmetro na URL
if(isset($_GET['id'])) {
    $sala_id = $_GET['id'];

    // Consulta a sala no banco de dados pelo ID
    $sql = "SELECT * FROM sala WHERE sala_codigo = $sala_id";
    $result = $conn->query($sql);

    // Verifica se a consulta retornou algum resultado
    if ($result->num_rows > 0) {
        // Exibe o formulário de edição preenchido com os dados da sala
        $row = $result->fetch_assoc();
        ?>

        <!DOCTYPE html>
        <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Editar Sala de Aula</title>

            <link rel="stylesheet" type="text/css" href="style/cadastro_sala_form.css" media="screen" />

            <script src="/js/validateForm.js"></script>

            <!-- Incluindo o jQuery via CDN -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        </head>
        <body>
            <div class="menu">
                <h2>Editar Sala de Aula</h2>
            </div>
            <div class="container">
                 <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
                <form action="atualizar_sala.php" method="POST" class="form-container" onsubmit="return validateForm()">
                    <!-- Campos do formulário -->
                    <input type="hidden" name="sala_codigo" value="<?php echo $row['sala_codigo']; ?>">

                    <label for="sala_nome">Nome da Sala:</label>
                    <input type="text" id="sala_nome" name="sala_nome" value="<?php echo $row['sala_nome']; ?>" required>
                    
                    <label for="sala_endereco">Endereço:</label>
                    <input type="text" id="sala_endereco" name="sala_endereco" value="<?php echo $row['sala_endereco']; ?>" required>

                    <label for="sala_tipo">Tipo:</label>
                    <input type="text" id="sala_tipo" name="sala_tipo" value="<?php echo $row['sala_tipo']; ?>" required>

                    <label>Situação:</label><br>
                    <ul class="checkbox-list">
                        <li>
                            <input type="radio" id="ativa" name="sala_situacao" value="Ativa" <?php if($row['sala_situacao'] == 'Ativa') echo 'checked'; ?> required>
                            <label for="ativa">Ativa</label>
                        </li>
                        <li>
                            <input type="radio" id="inativa" name="sala_situacao" value="Inativa" <?php if($row['sala_situacao'] == 'Inativa') echo 'checked'; ?>>
                            <label for="inativa">Inativa</label>
                        </li>
                    </ul>

                    <label for="sala_capacidade_alunos">Capacidade:</label>
                    <input type="number" id="sala_capacidade_alunos" name="sala_capacidade_alunos" value="<?php echo $row['sala_capacidade_alunos']; ?>" required>
                            
                    <label>Recursos:</label><br>
                    <ul class="checkbox-list">
                        <li>
                            <input type="checkbox" id="notebook" name="sala_descricao[]" value="Notebook" <?php if(in_array('Notebook', explode(", ", $row['sala_descricao']))) echo 'checked'; ?>>
                            <label for="notebook">Computador</label>
                        </li>
                        <li>
                            <input type="checkbox" id="tv" name="sala_descricao[]" value="TV" <?php if(in_array('TV', explode(", ", $row['sala_descricao']))) echo 'checked'; ?>>
                            <label for="tv">TV</label>
                        </li>
                        <li>
                            <input type="checkbox" id="ar_condicionado" name="sala_descricao[]" value="Ar condicionado" <?php if(in_array('Ar condicionado', explode(", ", $row['sala_descricao']))) echo 'checked'; ?>>
                            <label for="ar_condicionado">Ar condicionado</label>
                        </li>

                        <li>
                            <input type="checkbox" id="sem_equipamento" name="sala_descricao[]" value="Sem equipamento" <?php if(in_array('Sem equipamento', explode(", ", $row['sala_descricao']))) echo 'checked'; ?>>
                            <label for="sem_equipamento">Sem equipamento</label>
                        </li>
                    </ul>
                          
                            
                    <button type="submit" class="button primary">Atualizar Sala</button>
                </form>
            </div>
        </body>
        </html>

        <?php
    } else {
        echo "Sala não encontrada.";
    }
} else {
    // Redireciona para a página de consulta de salas, se o ID não estiver presente na URL
    header("Location: consultar_sala.php");
    exit();
}

$conn->close();
?>
