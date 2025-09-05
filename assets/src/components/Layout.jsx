import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

function Layout({ children }) {
  const { user, logout } = useAuth();
  const location = useLocation();

  const navigation = [
    { name: 'Dashboard', href: '/', icon: '📊' },
    { name: 'Sites', href: '/sites', icon: '🌐' },
    { name: 'Leads', href: '/leads', icon: '👥' },
    { name: 'Tarefas', href: '/tasks', icon: '✅' },
    { name: 'Financeiro', href: '/financial', icon: '💰' },
    { name: 'Configurações', href: '/settings', icon: '⚙️' }
  ];

  const handleLogout = () => {
    logout();
  };

  return (
    <div className="layout">
      <nav className="sidebar">
        <div className="sidebar-header">
          <h2>Company Hub</h2>
        </div>
        
        <ul className="nav-menu">
          {navigation.map((item) => (
            <li key={item.name}>
              <Link
                to={item.href}
                className={`nav-link ${location.pathname === item.href ? 'active' : ''}`}
              >
                <span className="nav-icon">{item.icon}</span>
                {item.name}
              </Link>
            </li>
          ))}
        </ul>
        
        <div className="sidebar-footer">
          <div className="user-info">
            <span className="user-name">{user?.username}</span>
            <span className="user-role">{user?.role}</span>
          </div>
          <button onClick={handleLogout} className="logout-button">
            Sair
          </button>
        </div>
      </nav>
      
      <main className="main-content">
        <div className="content-wrapper">
          {children}
        </div>
      </main>
    </div>
  );
}

export default Layout;