<!-- cadastro_sala_form.php -->

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Sala de Aula</title>

    <link rel="stylesheet" type="text/css" href="style/cadastro_sala_form.css" media="screen" />

    <script src="/js/validateForm.js"></script>

    <!-- Incluindo o jQuery via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <div class="menu">
        <h2>Cadastro de Sala de Aula</h2>
    </div>
    
    <div class="container">
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form action="processar_cadastro_sala.php" method="POST" class="form-container" onsubmit="return validateForm()">
            <label for="sala_nome">Nome da Sala:</label>
            <input type="text" id="sala_nome" name="sala_nome" required>
            
            <label for="sala_endereco">Endereço:</label>
            <input type="text" id="sala_endereco" name="sala_endereco" required>

            <label for="sala_tipo">Tipo:</label>
            <input type="text" id="sala_tipo" name="sala_tipo" required>

            <label>Situação:</label><br>
            <ul class="checkbox-list">
                <li>
                    <input type="radio" id="ativa" name="sala_situacao" value="Ativa" required>
                    <label for="ativa">Ativa</label>
                </li>
                <li>
                    <input type="radio" id="inativa" name="sala_situacao" value="Inativa">
                    <label for="inativa">Inativa</label>
                </li>
            </ul>

            <label for="sala_capacidade_alunos">Capacidade:</label>
            <input type="number" id="sala_capacidade_alunos" name="sala_capacidade_alunos" required>
                    
            <label>Recursos:</label><br>
            <ul class="checkbox-list">
                <li>
                    <input type="checkbox" id="notebook" name="sala_descricao[]" value="Notebook">
                    <label for="notebook">Computador</label>
                </li>
                <li>
                    <input type="checkbox" id="tv" name="sala_descricao[]" value="TV" >
                    <label for="tv">TV</label>
                </li>
                <li>
                    <input type="checkbox" id="ar_condicionado" name="sala_descricao[]" value="Ar condicionado" >
                    <label for="ar_condicionado">Ar condicionado</label>
                </li>

                <li>
                    <input type="checkbox" id="sem_equipamento" name="sala_descricao[]" value="Sem equipamento">
                    <label for="sem_equipamento">Sem equipamento</label>
                </li>
            </ul>
                  
                    
            <button type="submit" class="button primary">Cadastrar Sala</button>
        </form>
    </div>
</body>
</html>
