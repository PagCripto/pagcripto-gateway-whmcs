<?php

/**
 * PagCripto - Módulo oficial para integração com WHMCS
 * 
 * @package    PagCripto para WHMCS
 * @version    1.0
 * @author     Carlos Heitor Lain
 */

// Opções padrão do Gateway
function pagcripto_config($params) {
    $config = array(

        'FriendlyName' => array(
            "Type" => "System",
            "Value" => "PagCripto.com.br"
        ),
        
        'id' => array(
            "FriendlyName" => "ID de cadastro PagCripto",
            "Type" => "text",
            "Size" => "150",
            "Description" => "ID da conta PagCripto que irá receber - Para mais informações checar https://github.com/PagCripto/WHMCS"
        ),

        "admin" => array(
            "FriendlyName" => "Administrador atribuído",
            "Type" => "text",
            "Size" => "10",
            "Default" => "admin",
            "Description" => "Insira o nome de usuário ou ID do administrador do WHMCS que será atribuído as transações. Necessário para usar a API interna do WHMCS."
        )
       
    );

    return $config;
}

function pagcripto_link($params) {   

    $function = 'getinvoice';
    $getinvoiceid['invoiceid'] = $params['invoiceid'];
    $whmcsAdmin = $params['admin'];
    $getinvoiceResults = localAPI($function,$getinvoiceid,$whmcsAdmin);
	
    // Definimos
    $systemurl = rtrim($params['systemurl'],"/");
    $urlRetorno = $systemurl.'/modules/gateways/'.basename(__FILE__);
        
$code = "
<!-- INICIO DO FORM PAGCRIPTO -->
    <form name=\"pagcripto\" action=\"https://dashboard.pagcripto.com.br/pagamento\" method=\"post\">
    <input type='image' src='https://pagcripto.com.br/img/btc.png' 
    title='Pagar com Bitcoin' alt='Pagar com Bitcoin' border='0'
     align='absbottom' /><br>
	<input type=\"hidden\" name=\"valor\" value='{$params['amount']}'>
	<input type=\"hidden\" name=\"descricao\" value='{$params['invoiceid']}'>
	<input type=\"hidden\" name=\"id\" value='{$params['id']}'>
	<input type=\"hidden\" name=\"retorno\" value='{$urlRetorno}'>
    <input type=\"submit\" value=\"Gerar carteira para pagamento\">
    <!-- FIM DO FORM PAGCRIPTO -->
    </form>
    {$abrirAuto}";

   return $code;                  
}

// Nenhuma das funções foi executada, então o script foi acessado diretamente.
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    
    header("access-control-allow-origin: *");

    // Inicializar WHMCS, carregar o gateway e a fatura.
    require "../../init.php";
    $whmcs->load_function("gateway");
    $whmcs->load_function("invoice");

    // Initialize module settings
    $GATEWAY = getGatewayVariables("pagcripto");

    // Se o usuário admin estiver vazio nas configurações, usamos o padrão
    $whmcsAdmin = (empty(trim($GATEWAY['admin'])) ? 'admin' : trim($GATEWAY['admin']));

    // Pegamos os campos enviados por POST. Vamos checar esses dados.
    $wallet    = $_POST['wallet'];
    $recebido   = $_POST['totalbrl'];
    $status = $_POST['status']; 
    $custom      = $_POST['custom'];
	$fee = 0;
	// Antes de mais nada, checamos se o ID da fatura existe no banco. 
    $invoiceid = checkCbInvoiceID($custom,$GATEWAY["name"]);
    // Pegamos a fatura como array e armazenamos na variável para uso posterior
    $command = "getinvoice";
    $values["invoiceid"] = $invoiceid;
    $results = localAPI($command,$values,$whmcsAdmin);
    // Função que vamos usar na localAPI
    $addtransaction = "addtransaction";
    // Cliente fez emissão do boleto, logamos apenas como memorando
    if ($status == "Criado") {
        $addtransvalues['userid'] = $results['userid'];
        $addtransvalues['invoiceid'] = $invoiceid;
        $addtransvalues['description'] = "Pagamento em Bitcoin iniciado.";
        $addtransvalues['amountin'] = '0.00';
        $addtransvalues['fees'] = '0.00';
        $addtransvalues['paymentmethod'] = 'pagcripto';
        $addtransvalues['transid'] = $wallet.'- Carteira Bitcoin';
        $addtransvalues['date'] = date('d/m/Y');
        $addtransresults = localAPI($addtransaction,$addtransvalues,$whmcsAdmin);
        logTransaction($GATEWAY["name"],$_POST,"Aguardando o Pagamento"); # Salva informações da transação no log do WHMCS.
    // Transação foi aprovada
    } else if ($status == "Confirmado") {
        // Essa função checa se a transação ja foi registrada no banco de dados. 
        checkCbTransID($wallet);
        // Registramos o pagamento e damos baixa na fatura
        addInvoicePayment($invoiceid,$wallet,$recebido,$fee,'pagcripto');
        // Logamos a transação no log de Gateways do WHMCS.
        logTransaction($GATEWAY["name"],$_POST,"Transação Concluída");
    }
    //TODO
    // Prever todos os tipos de retorno.   
}
?>
