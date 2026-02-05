<?php
// Verifica se os dados do formulário foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Inclui o arquivo de configuração do banco de dados
    require_once "../conexao.php";

    // Classe para manipular a inserção de dados na tabela 'sala'
    class SalaHandler {
        private $conn;

        // Construtor que recebe uma conexão com o banco de dados
        public function __construct($conn) {
            $this->conn = $conn;
        }

        // Método para verificar se a sala já existe no banco de dados
        public function salaExiste($sala_nome) {
            // Prepara a declaração SQL para verificar se a sala já existe
            $sql = "SELECT sala_nome FROM sala WHERE sala_nome = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $sala_nome);
            $stmt->execute();
            $stmt->store_result();
            // Retorna verdadeiro se a sala já existe, falso caso contrário
            return $stmt->num_rows > 0;
        }

        // Método para inserir os dados da sala no banco de dados
        public function cadastrarSala($sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $sala_capacidade_alunos, $sala_descricao) {
            // Verifica se a sala já existe
            if ($this->salaExiste($sala_nome)) {
                return false; // Retorna falso se a sala já existir
            }

            // Prepara a declaração SQL para inserção
            $sql = "INSERT INTO sala (sala_nome, sala_endereco, sala_tipo, sala_situacao, sala_capacidade_alunos, sala_descricao) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            // Executa a inserção
            $stmt->execute([$sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $sala_capacidade_alunos, implode(", ", $sala_descricao)]);
            // Verifica se a inserção foi bem-sucedida
            return $stmt->affected_rows > 0;
        }
    }

    // Cria uma instância da classe SalaHandler
    $salaHandler = new SalaHandler($conn);

    // Obtém os dados do formulário
    $sala_nome = $_POST["sala_nome"];
    $sala_endereco = $_POST["sala_endereco"];
    $sala_tipo = $_POST["sala_tipo"];
    $sala_situacao = $_POST["sala_situacao"];
    $sala_capacidade_alunos = $_POST["sala_capacidade_alunos"];
    $sala_descricao = $_POST["sala_descricao"];

    // Chama o método para cadastrar a sala
    if ($salaHandler->cadastrarSala($sala_nome, $sala_endereco, $sala_tipo, $sala_situacao, $sala_capacidade_alunos, $sala_descricao)) {
        // Mensagem de sucesso
        echo "<p>Sala cadastrada com sucesso!</p>";
    } else {
        // Mensagem de erro
        echo "<p>A sala '$sala_nome' já existe. Por favor, escolha outro nome para a sala.</p>";
    }
} else {
    // Redireciona para a página do formulário, se os dados não foram submetidos via POST
    header("Location: cadastro_sala_form.php");
    exit();
}
?>
