# Company Hub - Plugin WordPress

Um sistema CRM + Painel de Comando Digital completo para WordPress, desenvolvido para centralizar a gestão de sites, leads, tarefas, finanças e métricas da sua empresa.

## 🎯 Características Principais

- **Frontend Próprio**: Interface React independente do wp-admin
- **Autenticação Segura**: Sistema de login separado com perfis de acesso
- **Arquitetura Modular**: Módulos podem ser ativados/desativados conforme necessário
- **API REST**: Backend robusto com endpoints seguros
- **Design Responsivo**: Interface moderna e mobile-friendly

## 📊 Módulos Incluídos

### 1. Dashboard Central
- Resumo com cards e gráficos
- Estatísticas em tempo real
- Notificações e alertas
- Atividade recente

### 2. Gestão de Sites
- Cadastro de todos os sites da empresa
- Monitoramento de uptime e SSL
- Controle de domínios e servidores
- Status de cada site

### 3. CRM & Leads
- Sistema completo de gestão de leads
- Acompanhamento de status
- Histórico de interações
- Origem dos contatos

### 4. Gestão de Tarefas
- Sistema Kanban (A Fazer, Em Andamento, Concluído)
- Prioridades e prazos
- Atribuição a usuários
- Organização por projetos

### 5. Financeiro
- Controle de receitas e despesas
- Relatórios de lucro
- Categorização por site/projeto
- Resumos visuais

### 6. Configurações
- Gerenciamento de usuários
- Controle de módulos
- Integrações externas
- Perfis de acesso

## 🚀 Instalação

### 1. Upload do Plugin
1. Faça upload da pasta `company-hub` para `/wp-content/plugins/`
2. Ative o plugin no painel do WordPress

### 2. Configuração Inicial
1. O plugin criará automaticamente as tabelas necessárias
2. Um usuário admin padrão será criado:
   - **Usuário**: admin
   - **Senha**: admin123
   - **Email**: admin@company.com

### 3. Compilar Assets (Desenvolvimento)
```bash
# Instalar dependências
npm install

# Desenvolvimento
npm run dev

# Build para produção
npm run build
```

## 🔐 Acesso ao Sistema

### URL de Acesso
- **Login**: `https://seusite.com/company-hub/login`
- **Dashboard**: `https://seusite.com/company-hub/`

### Perfis de Usuário
- **Admin**: Acesso completo a todos os módulos
- **Colaborador**: Acesso limitado (visualização e tarefas)

## 🏗️ Estrutura do Plugin

```
company-hub/
├── company-hub.php          # Arquivo principal
├── includes/                # Classes PHP
│   ├── class-database.php   # Gerenciamento do banco
│   ├── class-auth.php       # Autenticação
│   ├── class-api.php        # API REST
│   ├── class-modules.php    # Sistema de módulos
│   └── class-frontend.php   # Frontend
├── assets/                  # Assets do frontend
│   ├── src/                 # Código fonte React
│   └── dist/                # Build compilado
├── package.json             # Dependências Node.js
├── vite.config.js          # Configuração Vite
└── README.md               # Documentação
```

## 🔧 Desenvolvimento

### Tecnologias Utilizadas
- **Backend**: PHP (WordPress)
- **Frontend**: React + Vite
- **Banco de Dados**: MySQL (tabelas customizadas)
- **API**: WordPress REST API
- **Autenticação**: Sistema próprio com sessões

### Endpoints da API
- `POST /wp-json/company-hub/v1/auth/login` - Login
- `POST /wp-json/company-hub/v1/auth/logout` - Logout
- `GET /wp-json/company-hub/v1/dashboard/stats` - Estatísticas
- `GET /wp-json/company-hub/v1/sites` - Lista de sites
- `GET /wp-json/company-hub/v1/leads` - Lista de leads
- `GET /wp-json/company-hub/v1/tasks` - Lista de tarefas
- `GET /wp-json/company-hub/v1/financial` - Registros financeiros

### Banco de Dados
O plugin cria as seguintes tabelas:
- `wp_ch_users` - Usuários do sistema
- `wp_ch_sites` - Sites da empresa
- `wp_ch_leads` - Leads e contatos
- `wp_ch_tasks` - Tarefas e projetos
- `wp_ch_financial` - Registros financeiros
- `wp_ch_accounts` - Contas externas
- `wp_ch_backlinks` - Backlinks
- `wp_ch_metrics` - Métricas dos sites

## 🔒 Segurança

- Autenticação independente do WordPress
- Sanitização de dados de entrada
- Verificação de permissões por endpoint
- Proteção contra SQL injection
- Criptografia de senhas com password_hash()

## 🎨 Personalização

### Modificar Estilos
Edite o arquivo `assets/src/App.css` e execute `npm run build`

### Adicionar Módulos
1. Registre o módulo em `includes/class-modules.php`
2. Crie o componente React em `assets/src/components/`
3. Adicione as rotas necessárias

### Integrações Externas
Configure APIs externas através do módulo de Configurações:
- Google Analytics
- Google Search Console
- APIs de afiliados
- Redes sociais

## 📈 Próximas Funcionalidades

- [ ] Integração com Google Analytics
- [ ] Monitoramento automático de backlinks
- [ ] Sistema de webhooks
- [ ] Relatórios em PDF
- [ ] Notificações por email
- [ ] API para integrações externas
- [ ] Dashboard widgets personalizáveis
- [ ] Sistema de backup automático

## 🐛 Suporte

Para suporte e desenvolvimento customizado, entre em contato através dos canais oficiais da empresa.

## 📄 Licença

Este plugin é proprietário e destinado ao uso interno da empresa. Não deve ser distribuído ou modificado sem autorização.

---

**Company Hub v1.0.0** - Desenvolvido para centralizar e otimizar a gestão digital da sua empresa.