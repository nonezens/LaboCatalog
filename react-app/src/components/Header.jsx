import React from 'react';
import { Link } from 'react-router-dom';
import './Header.css';

const Header = () => {
    // Hardcoded for now, will be replaced with a proper auth hook
    const isLoggedIn = false;
    const isAdmin = false;
    const guestName = 'Guest';

    return (
        <header className="site-header" id="siteHeader">
            <nav className="site-nav">
                <div className="site-logo">
                    <Link to="/">
                        <img src="/uploads/tourism-logo.png" alt="Museum Logo" className="logo-img" />
                        <span className="logo-text">
                            <span className="main-title">Museo De Labo</span>
                            <span className="tagline">ᜋᜓᜐᜒᜂ ᜇᜒ ᜎᜊᜓ</span>
                        </span>
                    </Link>
                </div>
                <ul className="site-nav-links">
                    <li><Link to="/">Home</Link></li>
                    <li><Link to="/about">About</Link></li>
                    <li><Link to="/exhibits">Collection</Link></li>
                    {isAdmin ? (
                        <>
                            <li className="admin-link">
                                <Link to="/admin" style={{ color: '#3498db', fontWeight: 'bold' }}>⚙️ Dashboard</Link>
                            </li>
                            <li>
                                <a href="/logout" style={{ color: '#e74c3c', fontWeight: 'bold', marginLeft: '10px' }}>Logout</a>
                            </li>
                        </>
                    ) : isLoggedIn ? (
                        <>
                            <li className="admin-link" style={{ color: '#bdc3c7', fontSize: '0.95rem' }}>
                                Welcome, {guestName}!
                            </li>
                            <li>
                                <a href="/logout" style={{ color: '#e74c3c', fontWeight: 'bold', marginLeft: '10px' }}>Leave</a>
                            </li>
                        </>
                    ) : (
                        <li className="admin-link"><Link to="/login" style={{ color: '#95a5a6', fontSize: '0.9em' }}>Login / Access</Link></li>
                    )}
                </ul>
            </nav>
        </header>
    );
};

export default Header;
