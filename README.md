# WHMCS - Módulo de pagamento em Bitcoin pela PagCripto 

Este módulo implementa pagamentos em Bitcoin com retorno automático no WHMCS.

* **Versão mais Recente:** 1.0
* **Requer WHMCS** versão mínima 5.0
* **Requisitos:** PHP >= 5.2.0, cURL ativado.
* **Compatibilidade:** WHMCS 7.1.2, PHP 7.x. Mod_rewrite opcional

# Como Instalar
1. Crie sua conta na PagCripto [clique aqui para criar uma nova conta](http://dashboard.pagcripto.com.br/registro).

2. Baixe o arquivo [pagcripto.php](https://github.com/PagCripto/WHMCS/tree/master/modules/gateways), coloque o arquivo na pasta gateways dentro de modules/gateways, para que o Gateway fique disponível dentro do seu WHMCS. 

3. Dentro da área administrativa do seu WHMCS, vá em: Setup > Payments > Payment Gateways (em inglês) ou Opções > Pagamentos > Portais para Pagamento

4. Após, va na aba “All Payment Gateways” ou "Todos os Portais de Pagamento" e procure pelo modulo de nome: “PagCripto” e clique em cima.

5. Configure o campo ID de cadastro PagCripto com a ID existente em [Integração HTML](https://dashboard.pagcripto.com.br/html-integracao) e configure também o nome do usuário administrativo no WHMCS.

# Suporte

Para questões relacionadas a integração e plugin, acesse o [forum de suporte no Github](https://github.com/PagCripto/WHMCS/issues);
Para dúvidas comerciais e/ou sobre o funcionamento do serviço, envie-nos um e-mail para [suporte@pagcripto.com.br](mailto:suporte@pagcripto.com.br).

# Changelog

## 1.0 (15/06/2018) - 

* Lançamento inicial
	- Criação de ordem de pagamento;
	- Cálculo da cotação em tempo real;
	- Retorno automático com adição de LOG no WHMCS.