# Módulo Melhor Envio para Magento 1

Agora ficou mais fácil ter o serviço de cotações do Melhor Envio no seu projeto de e-commerce.

## Índice

- [Módulo Melhor Envio para Magento 1](#módulo-melhor-envio-para-magento-1)
  - [Índice](#índice)
  - [Dependências](#dependências)
    - [require](#require)
  - [Instalação](#instalação)
  - [Configuração inicial](#configuração-inicial)
    - [1 - Configurando token e ambiente](#1---configurando-token-e-ambiente)
    - [2 - Informações do remetente](#2---informações-do-remetente)
    - [3 - Configurações da Forma de Entrega](#3---configurações-da-forma-de-entrega)
    - [4 - Configurações de Entrega](#4---configurações-de-entrega)
  - [Sobre os produtos e os cálculos de frete](#sobre-os-produtos-e-os-cálculos-de-frete)
  - [Changelog](#changelog)
  - [Contribuindo](#contribuindo)
  - [Segurança](#segurança)
  - [Créditos](#créditos)
  - [Licença](#licença)

## Dependências

### require 
* PHP >= 5.6
* Magento >= 1.9.x.x


## Instalação

Você pode instalar o módulo realizando o download do mesmo e extraindo os arquivos contidos na pasta NovaPC_Melhorenvio na raiz do seu projeto Magento.

Abaixo você encontra as informações básicas para a instalação do módulo, informações completas para instalação e utilização do módulo podem ser encontradas na [central de ajuda do Melhor Envio](https://ajuda.melhorenvio.com.br/pt-BR/articles/4586935-manual-de-integracao-plataforma-magento-1)

## Configuração inicial

Ao instalar o módulo será adicionada uma nova aba ao painel administrativo do seu projeto Magento intitulada "Melhor Envio".

### 1 - Configurando token e ambiente

A primeira configuração a ser feita é a token e seu ambiente, para isso será necessário que primeiro você possua um token do Melhor Envio.

Para gerar o token, acesso o Painel do Melhor Envio em Gerenciar -> Tokens -> Novo token. Tenha certeza de copiar todo o token, caso o mesmo seja perdido será necessário gerar um novo.

Com o token gerado, acesse o painel Configurações Gerais do Melhor Envio no seu Magento. No item "Geral", cole o token e selecione o devido ambiente.

As opções de ambiente são "Homologação", referente ao [Sandbox do Melhor Envio](https://sandbox.melhorenvio.com.br/), e "Produção", referente ao ambiente de produção do [Melhor Envio](https://www.melhorenvio.com.br/).

Antes de confirar o restante das informações clique em Salvar para registrar o token e o ambiente.

### 2 - Informações do remetente

No mesmo painel onde é configurado o token, após o token ser salvo, você deverá configurar as informações relacionadas ao remetente dos envios a serem gerados utilizando o módulo do Melhor Envio.

Como você já salvou o token na etapa anterior, você pode clicar em Buscar para que o módulo consulte o Melhor Envio e recupere seus dados de cadastro. Igual você pode inserir estas informações manualmente ou editar as mesmas após o preenchimento automático.

Tenha certeza de preencher todas as informaçẽos corretamente para evitar problemas na hora de gerar os envios.

Caso algum dos campos de Pessoa Jurídica (CNPJ e Inscrição Estadual) não se aplique ao seu caso deixe-os em branco. O mesmo vale para o campo Complemento do endereço.

### 3 - Configurações da Forma de Entrega

Com a instalação do módulo será adicionada uma nova forma de entrega ao seu projeto Magento chamada Nova PC - Melhor Envio.

Um atalho para esta configuração também foi adicionado a nova aba do painel administrativo do Magento para fácil acesso, bastando então acessar o item Melhor Envio -> Configurações de Envios.

### 4 - Configurações de Entrega

Nas configurações do Magento, no item Vendas -> Configurações de Entrega você deverá configurar corretamente as informações da Origem do Envio. Para evitar confusão, recomendamos utilizar o mesmo endereço cadastrado na etapa 2 deste documento.

## Sobre os produtos e os cálculos de frete

O módulo também irá inserir novos campos no cadastro dos produtos destinados a suas dimensões (Altura, Largura e Comprimento).

É de suma importância que os produtos as informações de suas dimensões cadastradas para que o módulo possa realizar cotações de forma correta.

## Changelog

Consulte [CHANGELOG](CHANGELOG.md) para mais informações de alterações recentes.

## Contribuindo

Consulte [CONTRIBUTING](CONTRIBUTING.md) para mais detalhes.

## Segurança

Se você descobrir algum problema de segurança, por favor, envie um e-mail para tecnologia@melhorenvio.com, ao invés de usar um *issue tracker*.

## Créditos

- [NovaPC](http://www.novapc.com.br/)

## Licença

Melhor Envio. Consulte [Arquivo de lincença](LICENSE.md) para mais informações.
