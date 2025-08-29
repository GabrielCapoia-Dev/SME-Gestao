
# 📌 Evidências de Teste e User Stories para tela de Servidores

## Índice
- [User Stories](#user-stories)
- [Evidências de Teste](#evidências-de-teste)

---

## User Stories

> Estrutura: **Como [persona], quero [objetivo] para [benefício].**


### US01 - Visualização Dinâmica de dados
- **ID:** US01
- **Descrição:** Como **usuário**, quero visualizar os gráficos de forma **dinâmica**, para **ter controle em tempo real** das informações atualizadas no sistema.
- **Critérios de Aceite:**
  - Usuário precisa fazer login.
  - Usuário precisa de permissão para acessar a listagem.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US02 - Cadastro de Servidor
- **ID:** US02  
- **Descrição:** Como **administrador ou RH**, quero registrar um novo servidor com matrícula, dados pessoais, cargo, turno, lotação e locais de trabalho, para manter o controle dos colaboradores.  
- **Critérios de Aceite:**  
  - Campos matrícula, nome, email, cargo, turno, locais de trabalho, lotação e data de admissão são obrigatórios.  
  - Usuários sem perfil de **Admin** ou **RH** podem visualizar, mas não editar esses dados.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US03 - Definição de Carga Horária
- **ID:** US03  
- **Descrição:** Como **administrador ou RH**, quero definir a carga horária do servidor com horários de entrada, saída para almoço, retorno e saída, para registrar o expediente.  
- **Critérios de Aceite:**  
  - Horários de entrada e saída são obrigatórios.  
  - Horários de intervalo são exibidos apenas para servidores de 40 horas.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US04 - Filtrar Servidores
- **ID:** US04  
- **Descrição:** Como **usuário**, quero filtrar a listagem de servidores por cargo, turno, locais de trabalho e lotação, para localizar rapidamente informações específicas.  
- **Critérios de Aceite:**  
  - Filtros devem aceitar múltiplas seleções quando aplicável.  
  - A combinação de filtros deve refletir imediatamente na lista e nos gráficos.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US05 - Exportação da Listagem
- **ID:** US05  
- **Descrição:** Como **usuário autorizado**, quero exportar a listagem de servidores em PDF, para compartilhar ou arquivar os dados.  
- **Critérios de Aceite:**  
  - A exportação deve respeitar os filtros aplicados.  
  - O arquivo gerado deve conter apenas as colunas disponíveis na tabela.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US06 - Controle de Acesso por Setor
- **ID:** US06  
- **Descrição:** Como **gestor de setor**, quero visualizar apenas servidores vinculados ao meu setor, para manter o foco nos colaboradores sob minha responsabilidade.  
- **Critérios de Aceite:**  
  - Usuários vinculados a um setor devem visualizar somente servidores do mesmo setor.  
  - Usuários vinculados a múltiplos setores devem ver a união dos servidores associados.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

### US07 - Resumo por Regime Contratual
- **ID:** US07  
- **Descrição:** Como **usuário**, quero ver um resumo com o total de servidores por regime contratual, para compreender a composição da equipe.  
- **Critérios de Aceite:**  
  - O resumo deve ser atualizado conforme os dados do gráfico mudam.  
  - Deve exibir total geral e percentuais por regime.  
- **Status:** Concluído  
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)


### US08 - Remoção de Servidor
- **ID:** US08
- **Descrição:** Como **administrador ou RH**, quero remover servidores individualmente ou em lote, para manter os registros atualizados.
- **Critérios de Aceite:**
  - Deve ser possível excluir um servidor individualmente.
  - Deve ser possível selecionar vários servidores para exclusão em lote.
  - Usuário deve possuir a permissão `Excluir Servidores`.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

  ### US09 - Configuração de Paginação e Colunas
- **ID:** US09
- **Descrição:** Como **usuário**, quero definir quantos itens são exibidos por página e alternar quais colunas estão visíveis, para personalizar a visualização da listagem.
- **Critérios de Aceite:**
  - O sistema deve oferecer opções de exibição de 10, 25, 50 ou 100 itens por página.
  - A alteração da quantidade de itens por página deve atualizar a listagem imediatamente.
  - Deve existir opção de "toggle" para ocultar ou exibir colunas.
  - A alternância de colunas deve refletir imediatamente na tabela.
- **Status:** Concluído
- **Evidências:** Ver [tabela de evidências](#evidências-de-teste)

---

## Evidências de Teste

| ID US | Funcionalidade                      | Data       | Resultado  | Evidências |
|-------|-------------------------------------|------------|------------|------------|
| [US01](#visualização-dinâmica-de-dados)  | Visualização Dinâmica de dados      | --/--/---- | Em aberto  | [Video]() |
| US02  | Cadastro de Servidor                | --/--/---- | Em aberto  | [Video]() |
| US03  | Definição de Carga Horária          | --/--/---- | Em aberto  | [Video]() |
| US04  | Filtrar Servidores                  | --/--/---- | Em aberto  | [Video]() |
| US05  | Exportação da Listagem              | --/--/---- | Em aberto  | [Video]() |
| US06  | Controle de Acesso por Setor        | --/--/---- | Em aberto  | [Video]() |
| US07  | Resumo por Regime Contratual        | --/--/---- | Em aberto  | [Video]() |
| US08  | Remoção de Servidor                 | --/--/---- | Em aberto  | [Video]() |
| US09  | Configuração de Paginação e Colunas | --/--/---- | Em aberto  | [Video]() |

---
