<?php

// LENDO O CSV

// Função necessária para mudar delimitador padrão do CSV
function callbackCsv($csv)
{
    return str_getcsv($csv, ";");
}

// Armazenando dados na variável
$dados = array_map('callbackCsv', file('lista.csv'));

// Removendo a primeira linha (cabeçalho)
array_shift($dados);

// Prévia do array
print_r($dados);






echo "<br/>";
echo "<br/>";







/* 
    Criando tabela no BD
    Exemplo do https://www.w3schools.com/php/php_mysql_create_table.asp
*/

$servername = "localhost";
$username = "jota"; // seu usuário do banco
$password = "senha123"; // sua senha do banco
$dbname = "tmp_exemplo_csv"; // precisa criar manualmente pelo phpMyAdmin ou terminal por exemplo

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // criando a tabela "cores"
  $sql = "CREATE TABLE cores (
  id INT(6) PRIMARY KEY,
  hexadecimal VARCHAR(7) NOT NULL,
  nome_pt VARCHAR(25) NOT NULL,
  nome_en VARCHAR(25) NOT NULL
  )";

  $conn->exec($sql);
  echo "-----> Tabela criada com sucesso!!!";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}



echo "<br/>";
echo "<br/>";




/* 
    Inserindo dados do CSV convertido em array no BD
    Exemplo do https://www.w3schools.com/php/php_mysql_prepared_statements.asp
    A parte de conexão com o banco foi removida pois já consta no código acima
*/

try {

  // preparando dados para inserir na tabela cores
  $stmt = $conn->prepare("INSERT INTO cores (id, hexadecimal, nome_pt, nome_en)
  VALUES (:id, :hexadecimal, :nome_pt, :nome_en)");
  $stmt->bindParam(':id', $id);
  $stmt->bindParam(':hexadecimal', $hexadecimal);
  $stmt->bindParam(':nome_pt', $nome_pt);
  $stmt->bindParam(':nome_en', $nome_en);

  // Para cada item do array, inserir uma nova linha na tabela
  foreach ($dados as $dado) {
    $id = $dado[0]; // O código da cor(cod_cor) na primeira posição do array
    $hexadecimal = $dado[3]; // O código do hexadecimal está na última posição
    $nome_pt = $dado[1]; // O nome em português na segunda posição
    $nome_en = $dado[2]; // O nome em inglês na terceira posição
    $stmt->execute();
  }
  

  echo "-----> Dados inseridos com sucesso!!!";
} catch(PDOException $e) {
  echo "Erro: " . $e->getMessage();
}

$conn = null;
?>