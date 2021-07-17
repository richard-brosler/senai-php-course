<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calculo Imposto de Renda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
  <!-- Inicio do Programa PHP -->
<?php
  $salarioBase = $_POST['salario'];
  $inssBase = $_POST['salario'];
  $numDependentes = $_POST['dependentes'];
  $inssValor = 0.00;
  $irrfValor = 0.00;
  $irrfBase = 0.00;

/*
Vamos a fórmula de como calcular o valor do INSS do funcionário, mas antes, 
vou apresentar a tabela de faixas:
+------------------------------+
|  Inicio  |   Fim    |   %    |
+----------+----------+--------+
|     0,00 | 1.100,00 | 07,50% |
| 1.100,01 | 2.203,48 | 09,00% |
| 2.203,49 | 3.305,22 | 12,00% |
| 3.305,23 | 6.433,57 | 14,00% |
+----------+----------+--------+

Vou colocar 5 situações de cálculo para ficar mais fácil de compreender.
1) Salário de R$900,00
a) ( 900,00 - 0 ) * 0,075 => 67,50
Resultado => a => 67,50
Onde 0 é a faixa inicial

2) Salário de R$ 1.500,00
a) ( 1.100,00 - 0 ) * 0,075 => 82,50
b) ( 1.500,00 - 1.100,00 ) * 0,09 => 36,00
Resultado => a + b => 82,50 + 36,00 => 118,50

3) Salário de R$ 2.600,00
A ideia é a mesma do 2º exemplo.
a) ( 1.100,00 - 0 ) * 0,075 => 82,50
b) ( 2.203,48 - 1.100,00 ) * 0,090 => 99,31
c) ( 2.600,00 - 2.203,48 ) * 0,12 => 47,58
Resultado = a + b + c => 82,50 + 99,31 + 47,58 => 229,39

4) Salário de 4.500,00
a) ( 1.100,00 - 0 ) * 0,075 => 82,50
b) ( 2.203,48 - 1.100,00 )* 0,090 => 99,31
c) ( 3.305,22 - 2.203,48 )* 0,120 => 132,21
c) ( 4.500,00 - 3.305,22 ) * 0,14 => 167,27
Resultado = a + b + c + d => 82,50 + 99,31 + 132,21 + 167,27 => 481,29

5) Salário de 10.500,00
a) ( 1.100,00 - 0 ) * 0,075 => 82,50
b) ( 2.203,48 - 1.100,00 )* 0,090 => 99,31
c) ( 3.305,22 - 2.203,48 )* 0,120 => 132,21
c) ( 6.433,57 - 3.305,22 ) * 0,14 => 437,97
Resultado = a + b + c + d => 82,50 + 99,31 + 132,21 + 437,97 => 751,99
Nesse último caso, o que ultrapassa a faixa máxima é desprezado do calculo, pois é o teto máximo do INSS.

Agora vamos para a orientação para o calculo do IRRF (Imposto de Renda Retido na Fonte)
Para isso vamos precisar dos valores recebíveis do funcionário (no caso só o 
salário nessa situação, se houvesse ganhos como comissão, isso entraria na base de calculo também)
Base de Calculo do IRRF é igual:
Valor Recebíveis - Valor do INSS - Pensão Alimentícia - dedução por número de dependentes
Depois de obtida a base de calculo, passamos pela tabela para aplicar a alíquota e subtrai-se a 
dedução do valor do imposto.
Atualmente o valor da dedução por dependentes está em R$ 189,59.
Tabela do IRRF:
+----------+----------+--------+--------+
| Inicio   | Fim      | Alíq.  | Dedução|
+----------+----------+--------+--------+
|     0,00 | 1.903,98 | isento |   0,00 |
| 1.903,99 | 2.826,65 |  7,50% | 142,80 |
| 2.826,66 | 3.751,05 | 15,00% | 354,80 |
| 3.751,06 | 4.664,68 | 22,50% | 636,13 |
| 4.664,68 | -------- | 27,50% | 869,36 |
+----------+----------+--------+--------+

Vamos pegar a situação 1 do INSS com 2 dependentes
1) BaseIR = 900,00 - 67,50 - (189,59 * 2) => 453,32
Valor do Imposto nessa situação é isento 453,32 está na primeira faixa

Vamos pegar a situação 2 do INSS com 1 dependente
2) BaseIR = 1.500,00 - 118,50 - (189,59 * 1) => 1.191,91
Valor do Imposto nessa situação é isento porque 1.191,91 está na primeira faixa

Vamos pegar a situação 3 do INSS com 0 dependente
3) BaseIR = 2.600,00 - 229,49 - (189,59 * 0) => 2.370,51
Valor do imposto nessa situação será 2.370,51 * 7,50% - 142,80 => 34,99 (2ª faixa)

Vamos pegar a situção 4 do INSS com 0 dependente
4) BaseIR = 4.500,00 - 481,29 - (189,59 * 0) => 4.018,71
Valor do imposto nessa situação será 4.018,71 * 22,50% - 636,13 => 268,08 (4ª faixa)

Vamos pegar a situção 5 do INSS com 5 dependente
5) BaseIR = 10.500,00 - 751,99 - (189,59 * 5) => 8.800,06
Valor do imposto nessa situação será 8.800,06 * 27,50% - 869,36 => 1550,66 (5ª faixa)
*/
//zerando as variaveis de trabalho
$vlrfx01=0; $vlrfx02=0; $vlrfx03=0; $vlrfx04=0;
$valorDependente=189.59;
$tabelaIR = array([   0.00, 1903.98, 0.00,   0.00],
                  [1903.99, 2826.65, 7.50, 142.80],
                  [2826.66, 3751.05, 15.00, 354.80],
                  [3751.06, 4664.68, 22.50, 636.13],
                  [4664.68, 99999999999999999, 27.50, 869.36]);

if ($salarioBase<=1100.00) 
  $vlrfx01=$salarioBase*0.075;
else 
  $vlrfx01=(1100-0)*0.075;
//1.100,01 | 2.203,48  
if ($salarioBase>1100.00 && $salarioBase<=2203.48) 
  $vlrfx02=($salarioBase-1100.00)*0.090;
else if ($salarioBase>2203.48)
  $vlrfx02=(2203.48-1100)*0.090;
//| 2.203,49 | 3.305,22 | 12,00% |
if ($salarioBase>2203.48 && $salarioBase<=3305.22) 
  $vlrfx03=($salarioBase-2203.48)*0.12;
else if ($salarioBase>3305.22)
  $vlrfx03=(3305.22-2203.48)*0.12;
//| 3.305,23 | 6.433,57 | 14,00% |
if ($salarioBase>3305.22 && $salarioBase<=6433.57) 
  $vlrfx04=($salarioBase-3305.22)*0.14;
else if ($salarioBase>6433.57)
  $vlrfx04=(6433.57-3305.22)*0.14;
//Calcular o INSS
$inssValor=round($vlrfx01+$vlrfx02+$vlrfx03+$vlrfx04,2);
  //Calcular o IRRF
$irrfBase=$salarioBase-$inssValor-($numDependentes * $valorDependente );
//Utilizando laço de repetição
for($i=0;$i<5;$i++){
  $faixa=$tabelaIR[$i];
  if ($irrfBase>=$faixa[0] && $irrfBase<=$faixa[1]){
    $irrfValor=round($irrfBase * $faixa[2] / 100.00 - $faixa[3],2);
    break;
  }
}
?>
  <!-- Término do programa PHP -->
  <div class="container-md">
    <div class="card">
      <div class="card-header">
        Resultado do Calculo do Imposto de renda
      </div>
      <div class="card-body">
        <!-- Informações do Salário Base -->
        <div class="mb-3">
          <label for="salarioid" class="form-label">Salário Base</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">R$</span>
            <input type="number" class="form-control" id="salarioid" step=".01" value="<?=$salarioBase;?>" disabled>
          </div>
        </div>
        <!-- Informações da Base de cálculo do INSS -->
        <div class="mb-3">
          <label for="inssBaseid" class="form-label">Base de Calculo INSS</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">R$</span>
            <input type="number" class="form-control" id="inssBaseid" step=".01" value="<?=$inssBase;?>" disabled>
          </div>
        </div>
        <!-- Informações do Valor do INSS -->
        <div class="mb-3">
          <label for="inssVlrid" class="form-label">Valor INSS</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">R$</span>
            <input type="number" class="form-control" id="inssVlrid" step=".01" value="<?=$inssValor;?>" disabled>
          </div>
        </div>
        <!-- Informações da Base de cálculo do IRRF -->
        <div class="mb-3">
          <label for="irrfBaseid" class="form-label">Base de Calculo IRRF</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">R$</span>
            <input type="number" class="form-control" id="irrfBaseid" step=".01" value="<?=$irrfBase;?>" disabled>
          </div>
        </div>
        <!-- Informações do Valor do Imposto de Renda -->
        <div class="mb-3">
          <label for="IrVlrid" class="form-label">Valor Imposto de Renda</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">R$</span>
            <input type="number" class="form-control" id="IrVlrid" step=".01" value="<?=$irrfValor;?>" disabled>
          </div>
        </div>
        <!-- Término do Body do Card -->
      </div>
      <!-- Término do Card -->
    </div>
    <!-- Término do Container -->
  </div>  
</body>
</html>