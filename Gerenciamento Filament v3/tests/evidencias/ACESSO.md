# 📌 Central de Evidências e User Stories

## Índice
- [User Stories](#user-stories)
- [Evidências de Teste](#evidências-de-teste)

---

## User Stories

> Estrutura: **Como [persona], quero [objetivo] para [benefício].**

### US01 - Cadastro de Usuário
- **ID:** US01
- **Descrição:** Como **novo usuário**, quero **cadastrar minha conta**, para **acessar o sistema**.
- **Critérios de Aceite:**
  - O cadastro é feito através do login via Google.
  - Exibir mensagem de sucesso após cadastro.
  - Enviar notificação de sucesso.
- **Status:** Em desenvolvimento
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

### US02 - Login no Sistema
- **ID:** US02
- **Descrição:** Como **usuário cadastrado**, quero **fazer login no sistema**, para **acessar minhas funcionalidades**.
- **Critérios de Aceite:**
  - Deve validar e-mail e senha.
  - Deve ser um usuário com status de ativo.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

### US03 - Tentativa de Login com Campos em Branco
- **ID:** US03
- **Descrição:** O **sistema**, valida se os campos estão **preenchidos**, para **acessar o sistema**.
- **Critérios de Aceite:**
  - Deve validar e-mail e senha.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

### US04 - Tentativa de Login com email ou senha incorretos
- **ID:** US04
- **Descrição:** O **sistema**, valida se os campos estão preenchidos com dados de um **usuário valido**, para **acessar o sistema**.
- **Critérios de Aceite:**
  - Deve validar e-mail e senha.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

### US05 - Após login usuário é redirecionado para a Listagem de Servidores
- **ID:** US05
- **Descrição:** Como **usuário**, quero visualizar a **Listagem de Servidores** após o login, para **ter acesso a informações relevantes**.
- **Critérios de Aceite:**
  - Usuário precisa fazer login.
  - Usuário precisa de permissão para acessar a listagem.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

## Evidências de Teste

Abaixo, uma tabela consolidando todas as evidências de teste com informações principais:

| ID US | Funcionalidade     | Data       | Resultado  | Evidências |
|-------|-------------------|------------|------------|------------|
| US01  | Cadastro de Usuário  | 25/08/2025  | **Não realizado** | |
| US02  | Login              | 25/08/2025 | **Aprovado** | [Video](https://jam.dev/c/09331a80-fb73-4680-abba-aed5ba8c9dd0) |
| US03  | Tentativa de Login com Campos em Branco              | 25/08/2025 | **Aprovado** | [Video](https://jam.dev/c/f2911f7a-6610-4bee-8668-6088fd190593) |
| US04  | Tentativa de Login com email ou senha incorretos              | 25/08/2025 | **Aprovado** | [Video](https://jam.dev/c/0c5eedda-7130-4611-83b5-a3eb0ffd8cb6) |
| US05  | Após login usuário é redirecionado para a Listagem de Servidores             | 25/08/2025 | **Aprovado** | [Video](https://jam.dev/c/09331a80-fb73-4680-abba-aed5ba8c9dd0) |

---
