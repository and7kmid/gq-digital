import React, { useState, useEffect } from 'react';
import api from '../services/api';

function Dashboard() {
  const [stats, setStats] = useState({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await api.get('/dashboard/stats');
      setStats(response.data);
    } catch (error) {
      console.error('Error fetching stats:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="loading">Carregando estatísticas...</div>;
  }

  return (
    <div className="dashboard">
      <div className="dashboard-header">
        <h1>Dashboard</h1>
        <p>Visão geral da empresa</p>
      </div>
      
      <div className="stats-grid">
        <div className="stat-card">
          <div className="stat-icon">🌐</div>
          <div className="stat-content">
            <h3>Sites Ativos</h3>
            <div className="stat-number">{stats.total_sites || 0}</div>
          </div>
        </div>
        
        <div className="stat-card">
          <div className="stat-icon">👥</div>
          <div className="stat-content">
            <h3>Total de Leads</h3>
            <div className="stat-number">{stats.total_leads || 0}</div>
          </div>
        </div>
        
        <div className="stat-card">
          <div className="stat-icon">🆕</div>
          <div className="stat-content">
            <h3>Leads Novos</h3>
            <div className="stat-number">{stats.new_leads || 0}</div>
          </div>
        </div>
        
        <div className="stat-card">
          <div className="stat-icon">✅</div>
          <div className="stat-content">
            <h3>Tarefas Ativas</h3>
            <div className="stat-number">{stats.active_tasks || 0}</div>
          </div>
        </div>
        
        {stats.sites_down > 0 && (
          <div className="stat-card alert">
            <div className="stat-icon">⚠️</div>
            <div className="stat-content">
              <h3>Sites Offline</h3>
              <div className="stat-number">{stats.sites_down}</div>
            </div>
          </div>
        )}
      </div>
      
      <div className="dashboard-sections">
        <div className="section">
          <h2>Atividade Recente</h2>
          <div className="activity-list">
            <div className="activity-item">
              <span className="activity-icon">👥</span>
              <span className="activity-text">Novo lead cadastrado</span>
              <span className="activity-time">há 2 horas</span>
            </div>
            <div className="activity-item">
              <span className="activity-icon">✅</span>
              <span className="activity-text">Tarefa concluída</span>
              <span className="activity-time">há 4 horas</span>
            </div>
            <div className="activity-item">
              <span className="activity-icon">🌐</span>
              <span className="activity-text">Site verificado</span>
              <span className="activity-time">há 6 horas</span>
            </div>
          </div>
        </div>
        
        <div className="section">
          <h2>Alertas</h2>
          <div className="alerts-list">
            <div className="alert-item info">
              <span className="alert-icon">ℹ️</span>
              <span className="alert-text">Todos os sistemas funcionando normalmente</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Dashboard;