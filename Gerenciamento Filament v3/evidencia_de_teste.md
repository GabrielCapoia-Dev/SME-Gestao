# 📌 Central de Evidências e User Stories

## Índice
- [User Stories](#user-stories)
- [Evidências de Teste](#evidências-de-teste)
- [Checklist de Qualidade](#checklist-de-qualidade)

---

## User Stories

> Estrutura sugerida: **Como [persona], quero [objetivo] para [benefício].**

### US01 - Cadastro de Usuário
- **ID:** US01
- **Descrição:** Como **novo usuário**, quero **cadastrar minha conta**, para **acessar o sistema**.
- **Critérios de Aceite:**
  1. O cadastro é feito através do login via google
  2. Exibir mensagem de sucesso após cadastro.
  3. Enviar notificação de sucesso
- **Status:** Em desenvolvimento
- **Evidências:**

---

### US02 - Login no Sistema
- **ID:** US02
- **Descrição:** Como **usuário cadastrado**, quero **fazer login no sistema**, para **acessar minhas funcionalidades**.
- **Critérios de Aceite:**
  - Deve validar e-mail e senha.
- **Status:** Concluído
- **Evidências:** [Ver teste](https://jam.dev/c/09331a80-fb73-4680-abba-aed5ba8c9dd0)

---

## Evidências de Teste

### Evidência US01
- **Funcionalidade:** Cadastro de Usuário
- **Ambiente:** Homologação
- **Data:** 25/08/2025
- **Responsável:** Vinicius
- **Passos Testados:**
  1. Preencher formulário corretamente.
  2. Submeter e verificar mensagem de sucesso.
- **Resultado:** **Aprovado**
- **Prints/Links:**
  - ![Print do cadastro](./evidencias/us01-cadastro.png)
  - [Vídeo de teste](https://link-video.com/us01)

---

### Evidência US02
- **Funcionalidade:** Login
- **Ambiente:** Produção
- **Data:** 25/08/2025
- **Responsável:** QA Team
- **Passos Testados:**
  1. Login com credenciais corretas.
  2. Teste de bloqueio após 5 tentativas inválidas.
- **Resultado:** **Aprovado**
- **Prints/Links:**
  - ![Print login](./evidencias/us02-login.png)

---

## Checklist de Qualidade

- [x] Cenários de teste documentados
- [x] Evidências anexadas (prints/vídeos)
- [ ] Cobertura de testes > 80%
- [ ] Homologação assinada pelo cliente

---

> **Dica:** Salve as evidências (prints, vídeos, PDFs) na pasta `/evidencias` do repositório e linke aqui.  
> Cada User Story pode ter sua própria seção de testes e evidências.

