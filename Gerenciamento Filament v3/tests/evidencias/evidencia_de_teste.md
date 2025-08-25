# 📌 Central de Evidências e User Stories

## Índice
- [User Stories](#user-stories)
- [Evidências de Teste](#evidências-de-teste)
- [Checklist de Qualidade](#checklist-de-qualidade)

---

## User Stories

> Estrutura: **Como [persona], quero [objetivo] para [benefício].**

### US01 - Cadastro de Usuário
- **ID:** US01
- **Descrição:** Como **novo usuário**, quero **cadastrar minha conta**, para **acessar o sistema**.
- **Critérios de Aceite:**
  1. O cadastro é feito através do login via Google.
  2. Exibir mensagem de sucesso após cadastro.
  3. Enviar notificação de sucesso.
- **Status:** Em desenvolvimento
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

### US02 - Login no Sistema
- **ID:** US02
- **Descrição:** Como **usuário cadastrado**, quero **fazer login no sistema**, para **acessar minhas funcionalidades**.
- **Critérios de Aceite:**
  - Deve validar e-mail e senha.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

## Evidências de Teste

Abaixo, uma tabela consolidando todas as evidências de teste com informações principais:

| ID US | Funcionalidade     | Ambiente     | Data       | Responsável | Resultado  | Evidências |
|-------|-------------------|--------------|------------|-------------|------------|------------|
| US01  | Cadastro de Usuário | Homologação | 25/08/2025 | Vinicius    | **Aprovado** | [Vídeo](https://link-video.com/us01) |
| US02  | Login              | Produção    | 25/08/2025 | QA Team     | **Aprovado** | [Jam Dev](https://jam.dev/c/09331a80-fb73-4680-abba-aed5ba8c9dd0) |

> Use `<br>` dentro da célula para múltiplos links/prints.

---

## Checklist de Qualidade

- [x] Cenários de teste documentados  
- [x] Evidências anexadas (prints/vídeos)  
- [ ] Cobertura de testes > 80%  
- [ ] Homologação assinada pelo cliente  

---

> **Dica:** Centralizar em tabelas facilita a visualização e manutenção.  
> Se precisar, você pode quebrar a tabela em várias (ex: uma por módulo ou sprint).

---

### **Como usar:**
- Cada **nova User Story** deve ter uma linha na tabela.
- Coloque prints e links no campo **Evidências**.
- Se tiver várias etapas, pode adicionar outra coluna chamada **Passos**.

Quer que eu **gere um modelo já pronto com colunas extras (como Passos Testados, Status e Notas)** para você só ir preenchendo? Quer também que eu **adicione badges/cores para deixar o resultado mais visual no GitHub**?
