
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

---
## Evidências de Teste

Abaixo, uma tabela consolidando todas as evidências de teste com informações principais:

| ID US | Funcionalidade     | Data       | Resultado  | Evidências |
|-------|-------------------|------------|------------|------------|
| US01  | Visualização Dinâmica de dados  | 27/08/2025  | **Aprovado** |  [Video](https://jam.dev/c/7153dab5-e2ac-46e8-be8d-56ae16ae1704) |

---
