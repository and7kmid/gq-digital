import React, { useState, useEffect } from 'react';
import api from '../services/api';

function Sites() {
  const [sites, setSites] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    url: '',
    server: '',
    responsible_user_id: ''
  });

  useEffect(() => {
    fetchSites();
  }, []);

  const fetchSites = async () => {
    try {
      const response = await api.get('/sites');
      setSites(response.data);
    } catch (error) {
      console.error('Error fetching sites:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/sites', formData);
      setShowForm(false);
      setFormData({ name: '', url: '', server: '', responsible_user_id: '' });
      fetchSites();
    } catch (error) {
      console.error('Error creating site:', error);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'active': return 'green';
      case 'inactive': return 'red';
      case 'maintenance': return 'orange';
      default: return 'gray';
    }
  };

  const getUptimeColor = (uptime) => {
    switch (uptime) {
      case 'up': return 'green';
      case 'down': return 'red';
      default: return 'gray';
    }
  };

  if (loading) {
    return <div className="loading">Carregando sites...</div>;
  }

  return (
    <div className="sites">
      <div className="page-header">
        <h1>Gestão de Sites</h1>
        <button 
          className="btn btn-primary"
          onClick={() => setShowForm(true)}
        >
          Adicionar Site
        </button>
      </div>

      {showForm && (
        <div className="modal-overlay">
          <div className="modal">
            <div className="modal-header">
              <h2>Novo Site</h2>
              <button 
                className="close-button"
                onClick={() => setShowForm(false)}
              >
                ×
              </button>
            </div>
            <form onSubmit={handleSubmit} className="form">
              <div className="form-group">
                <label>Nome do Site</label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({...formData, name: e.target.value})}
                  required
                />
              </div>
              <div className="form-group">
                <label>URL</label>
                <input
                  type="url"
                  value={formData.url}
                  onChange={(e) => setFormData({...formData, url: e.target.value})}
                  required
                />
              </div>
              <div className="form-group">
                <label>Servidor</label>
                <input
                  type="text"
                  value={formData.server}
                  onChange={(e) => setFormData({...formData, server: e.target.value})}
                />
              </div>
              <div className="form-actions">
                <button type="button" onClick={() => setShowForm(false)}>
                  Cancelar
                </button>
                <button type="submit" className="btn btn-primary">
                  Salvar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="sites-grid">
        {sites.map((site) => (
          <div key={site.id} className="site-card">
            <div className="site-header">
              <h3>{site.name}</h3>
              <div className="site-status">
                <span 
                  className={`status-badge ${getStatusColor(site.status)}`}
                >
                  {site.status}
                </span>
                <span 
                  className={`uptime-badge ${getUptimeColor(site.uptime_status)}`}
                >
                  {site.uptime_status}
                </span>
              </div>
            </div>
            
            <div className="site-info">
              <p><strong>URL:</strong> <a href={site.url} target="_blank" rel="noopener noreferrer">{site.url}</a></p>
              {site.server && <p><strong>Servidor:</strong> {site.server}</p>}
              {site.responsible_name && <p><strong>Responsável:</strong> {site.responsible_name}</p>}
              {site.domain_expiry && <p><strong>Expira em:</strong> {new Date(site.domain_expiry).toLocaleDateString()}</p>}
            </div>
            
            <div className="site-actions">
              <button className="btn btn-sm">Editar</button>
              <button className="btn btn-sm">Verificar</button>
            </div>
          </div>
        ))}
      </div>

      {sites.length === 0 && (
        <div className="empty-state">
          <p>Nenhum site cadastrado ainda.</p>
          <button 
            className="btn btn-primary"
            onClick={() => setShowForm(true)}
          >
            Adicionar Primeiro Site
          </button>
        </div>
      )}
    </div>
  );
}

export default Sites;