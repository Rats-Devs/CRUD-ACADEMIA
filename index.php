<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/biblioteca-css.css" rel="stylesheet">

    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
    rel="stylesheet"
    />
    <title>Home</title>
</head>
<body>
    <nav>
        <div class="nav_header">
            <div class="nav_logo">
                <a href="index.php">
                    <img src="assets/gym.png" alt="logo" class="gym-logo">
                </a>
            </div>
        </div>
        
        <ul class="nav_link" id="nav-links">
            <li><a href="index.php">Home</a></li>
        </ul>
    </nav>

    <header>
        <div class="header_image">
            <img src="assets/exercitando.png" alt="header" />
        </div>
        <div class="header_content">
            <h2>ACADEMIA TOP</h2>
            <h1>Sistema de Gerenciamento de Academia</h1>
            <p>
                Gerencie as informações da academia em um só lugar. Escolha abaixo o módulo que deseja acessar.
            </p>
            <div class="header_btn">
                <a href="alunos/cadastro.php">Alunos</a>
                <a href="professores/cadastro.php">Professores</a>
                <a href="planos/cadastro.php">Planos</a>
                <a href="matriculas/cadastro.php">Matrículas</a>
                <a href="pagamentos/cadastro.php">Pagamentos</a>
                <a href="treinos/cadastro.php">Treinos</a>
                <a href="avaliacoes/cadastro.php">Avaliações Físicas</a>
            </div>
        </div>
    </header>
</body>
</html>