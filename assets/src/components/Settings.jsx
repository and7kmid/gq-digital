import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';

function Settings() {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('profile');

  const tabs = [
    { id: 'profile', name: 'Perfil', icon: '👤' },
    { id: 'modules', name: 'Módulos', icon: '🧩' },
    { id: 'users', name: 'Usuários', icon: '👥' },
    { id: 'integrations', name: 'Integrações', icon: '🔗' }
  ];

  const modules = [
    { id: 'dashboard', name: 'Dashboard Central', enabled: true, required: true },
    { id: 'sites', name: 'Gestão de Sites', enabled: true },
    { id: 'accounts', name: 'Contas & Credenciais', enabled: true },
    { id: 'analytics', name: 'Métricas & Analytics', enabled: true },
    { id: 'backlinks', name: 'Gestão de Backlinks', enabled: false },
    { id: 'crm', name: 'CRM & Leads', enabled: true },
    { id: 'tasks', name: 'Projetos & Tarefas', enabled: true },
    { id: 'financial', name: 'Financeiro', enabled: true },
    { id: 'seo', name: 'SEO & Marketing', enabled: false },
    { id: 'automation', name: 'Automação', enabled: false }
  ];

  return (
    <div className="settings">
      <div className="page-header">
        <h1>Configurações</h1>
      </div>

      <div className="settings-container">
        <div className="settings-sidebar">
          <nav className="settings-nav">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                className={`nav-item ${activeTab === tab.id ? 'active' : ''}`}
                onClick={() => setActiveTab(tab.id)}
              >
                <span className="nav-icon">{tab.icon}</span>
                {tab.name}
              </button>
            ))}
          </nav>
        </div>

        <div className="settings-content">
          {activeTab === 'profile' && (
            <div className="settings-section">
              <h2>Perfil do Usuário</h2>
              <div className="profile-info">
                <div className="form-group">
                  <label>Nome de Usuário</label>
                  <input type="text" value={user?.username || ''} readOnly />
                </div>
                <div className="form-group">
                  <label>Email</label>
                  <input type="email" value={user?.email || ''} readOnly />
                </div>
                <div className="form-group">
                  <label>Função</label>
                  <input type="text" value={user?.role || ''} readOnly />
                </div>
                <button className="btn btn-primary">Alterar Senha</button>
              </div>
            </div>
          )}

          {activeTab === 'modules' && (
            <div className="settings-section">
              <h2>Gerenciar Módulos</h2>
              <p>Ative ou desative módulos conforme necessário.</p>
              
              <div className="modules-list">
                {modules.map((module) => (
                  <div key={module.id} className="module-item">
                    <div className="module-info">
                      <h4>{module.name}</h4>
                      {module.required && (
                        <span className="required-badge">Obrigatório</span>
                      )}
                    </div>
                    <div className="module-toggle">
                      <label className="switch">
                        <input
                          type="checkbox"
                          checked={module.enabled}
                          disabled={module.required}
                          onChange={() => {}}
                        />
                        <span className="slider"></span>
                      </label>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'users' && user?.role === 'admin' && (
            <div className="settings-section">
              <h2>Gerenciar Usuários</h2>
              <div className="users-header">
                <button className="btn btn-primary">Novo Usuário</button>
              </div>
              
              <div className="users-table">
                <table>
                  <thead>
                    <tr>
                      <th>Usuário</th>
                      <th>Email</th>
                      <th>Função</th>
                      <th>Status</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>admin</td>
                      <td>admin@company.com</td>
                      <td>Administrador</td>
                      <td><span className="status-badge green">Ativo</span></td>
                      <td>
                        <button className="btn btn-sm">Editar</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {activeTab === 'integrations' && (
            <div className="settings-section">
              <h2>Integrações</h2>
              <p>Configure integrações com serviços externos.</p>
              
              <div className="integrations-list">
                <div className="integration-item">
                  <div className="integration-info">
                    <h4>Google Analytics</h4>
                    <p>Conecte sua conta do Google Analytics para métricas automáticas</p>
                  </div>
                  <button className="btn btn-outline">Conectar</button>
                </div>
                
                <div className="integration-item">
                  <div className="integration-info">
                    <h4>Google Search Console</h4>
                    <p>Monitore o desempenho SEO dos seus sites</p>
                  </div>
                  <button className="btn btn-outline">Conectar</button>
                </div>
                
                <div className="integration-item">
                  <div className="integration-info">
                    <h4>Webhooks</h4>
                    <p>Configure webhooks para automações</p>
                  </div>
                  <button className="btn btn-outline">Configurar</button>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default Settings;