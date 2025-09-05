import React, { useState, useEffect } from 'react';
import api from '../services/api';

function Leads() {
  const [leads, setLeads] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    source: '',
    notes: ''
  });

  useEffect(() => {
    fetchLeads();
  }, []);

  const fetchLeads = async () => {
    try {
      const response = await api.get('/leads');
      setLeads(response.data);
    } catch (error) {
      console.error('Error fetching leads:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/leads', formData);
      setShowForm(false);
      setFormData({ name: '', email: '', phone: '', company: '', source: '', notes: '' });
      fetchLeads();
    } catch (error) {
      console.error('Error creating lead:', error);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'new': return 'blue';
      case 'contacted': return 'orange';
      case 'qualified': return 'purple';
      case 'converted': return 'green';
      case 'lost': return 'red';
      default: return 'gray';
    }
  };

  const getStatusText = (status) => {
    const statusMap = {
      'new': 'Novo',
      'contacted': 'Contatado',
      'qualified': 'Qualificado',
      'converted': 'Convertido',
      'lost': 'Perdido'
    };
    return statusMap[status] || status;
  };

  if (loading) {
    return <div className="loading">Carregando leads...</div>;
  }

  return (
    <div className="leads">
      <div className="page-header">
        <h1>Gestão de Leads</h1>
        <button 
          className="btn btn-primary"
          onClick={() => setShowForm(true)}
        >
          Novo Lead
        </button>
      </div>

      {showForm && (
        <div className="modal-overlay">
          <div className="modal">
            <div className="modal-header">
              <h2>Novo Lead</h2>
              <button 
                className="close-button"
                onClick={() => setShowForm(false)}
              >
                ×
              </button>
            </div>
            <form onSubmit={handleSubmit} className="form">
              <div className="form-row">
                <div className="form-group">
                  <label>Nome *</label>
                  <input
                    type="text"
                    value={formData.name}
                    onChange={(e) => setFormData({...formData, name: e.target.value})}
                    required
                  />
                </div>
                <div className="form-group">
                  <label>Email</label>
                  <input
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({...formData, email: e.target.value})}
                  />
                </div>
              </div>
              
              <div className="form-row">
                <div className="form-group">
                  <label>Telefone</label>
                  <input
                    type="tel"
                    value={formData.phone}
                    onChange={(e) => setFormData({...formData, phone: e.target.value})}
                  />
                </div>
                <div className="form-group">
                  <label>Empresa</label>
                  <input
                    type="text"
                    value={formData.company}
                    onChange={(e) => setFormData({...formData, company: e.target.value})}
                  />
                </div>
              </div>
              
              <div className="form-group">
                <label>Origem</label>
                <select
                  value={formData.source}
                  onChange={(e) => setFormData({...formData, source: e.target.value})}
                >
                  <option value="">Selecione...</option>
                  <option value="website">Website</option>
                  <option value="google_ads">Google Ads</option>
                  <option value="facebook">Facebook</option>
                  <option value="linkedin">LinkedIn</option>
                  <option value="referral">Indicação</option>
                  <option value="other">Outro</option>
                </select>
              </div>
              
              <div className="form-group">
                <label>Observações</label>
                <textarea
                  value={formData.notes}
                  onChange={(e) => setFormData({...formData, notes: e.target.value})}
                  rows="3"
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

      <div className="leads-table">
        <table>
          <thead>
            <tr>
              <th>Nome</th>
              <th>Email</th>
              <th>Empresa</th>
              <th>Origem</th>
              <th>Status</th>
              <th>Data</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            {leads.map((lead) => (
              <tr key={lead.id}>
                <td>
                  <div className="lead-name">
                    <strong>{lead.name}</strong>
                    {lead.phone && <div className="lead-phone">{lead.phone}</div>}
                  </div>
                </td>
                <td>{lead.email}</td>
                <td>{lead.company}</td>
                <td>{lead.source}</td>
                <td>
                  <span className={`status-badge ${getStatusColor(lead.status)}`}>
                    {getStatusText(lead.status)}
                  </span>
                </td>
                <td>{new Date(lead.created_at).toLocaleDateString()}</td>
                <td>
                  <div className="actions">
                    <button className="btn btn-sm">Editar</button>
                    <button className="btn btn-sm">Contatar</button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {leads.length === 0 && (
        <div className="empty-state">
          <p>Nenhum lead cadastrado ainda.</p>
          <button 
            className="btn btn-primary"
            onClick={() => setShowForm(true)}
          >
            Adicionar Primeiro Lead
          </button>
        </div>
      )}
    </div>
  );
}

export default Leads;