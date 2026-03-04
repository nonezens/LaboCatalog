import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import Header from '../components/Header';
import './Exhibits.css';

const Exhibits = () => {
    // Hardcoded for now
    const isLoggedIn = true;
    const [search, setSearch] = useState('');
    const [cat, setCat] = useState('');
    const [period, setPeriod] = useState('');
    const [origin, setOrigin] = useState('');
    const [sort, setSort] = useState('date_desc');

    const all_categories = [
        { id: 1, name: 'Books' },
        { id: 2, name: 'Letters' },
        { id: 3, name: 'Photos' },
    ];
    const all_periods = [
        { artifact_year: '1945' },
        { artifact_year: '1898' },
        { artifact_year: '1901' },
        { artifact_year: '2001' },
    ];
    const all_origins = [
        { origin: 'Labo' },
        { origin: 'Manila' },
    ];

    const artifacts = [
        { id: 1, image_path: '1book.jpg', category_name: 'Books', title: 'Old Book', artifact_year: '1945', origin: 'Labo', donated_by': 'John Doe', description: 'An old book from World War II.' },
        { id: 2, image_path: '2book.jpg', category_name: 'Books', title: 'Spanish-era Bible', artifact_year: '1898', origin: 'Manila', donated_by': 'Jane Doe', description: 'A bible from the Spanish colonial period.' },
        { id: 3, image_path: 'd1cee0a9bd9adff6604c500fcbe368e0.jpg', category_name: 'Letters', title: 'American-era Letter', artifact_year: '1901', origin: 'Labo', donated_by': 'Peter Jones', 'description': 'A letter from the American colonial period.' },
        { id: 4, image_path: 'books.jpg', category_name: 'Books', title: 'Modern History Book', artifact_year: '2001', origin: 'Manila', donated_by': 'Juan Dela Cruz', 'description': 'A modern history book about the Philippines.' },
    ];

    const filteredArtifacts = artifacts.filter(artifact => {
        return (
            (artifact.title.toLowerCase().includes(search.toLowerCase()) ||
                artifact.donated_by.toLowerCase().includes(search.toLowerCase()) ||
                artifact.description.toLowerCase().includes(search.toLowerCase()) ||
                artifact.category_name.toLowerCase().includes(search.toLowerCase())) &&
            (cat === '' || artifact.category_id === parseInt(cat)) &&
            (period === '' || artifact.artifact_year === period) &&
            (origin === '' || artifact.origin === origin)
        );
    }).sort((a, b) => {
        if (sort === 'date_desc') {
            return b.artifact_year - a.artifact_year;
        } else if (sort === 'date_asc') {
            return a.artifact_year - b.artifact_year;
        } else if (sort === 'name_asc') {
            return a.title.localeCompare(b.title);
        } else if (sort === 'name_desc') {
            return b.title.localeCompare(a.title);
        }
        return 0;
    });



    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.5 }}
        >
            <Header />
            {!isLoggedIn ? (
                <section className="not-logged-in">
                    <h2>🏛️ Explore Our Collection</h2>
                    <p>Browse our curated collection of artifacts, organized by department with advanced filters. Sign in to access the digital catalog.</p>
                    <Link to="/login">Sign the Guestbook to Enter →</Link>
                </section>
            ) : (
                <div className="container" style={{ paddingTop: '50px', paddingBottom: '50px' }}>
                    <h1 className="page-title">Museum Collection</h1>

                    <div className="filter-section">
                        <h3>🔍 Filter & Search Collections</h3>
                        <form>
                            <div className="filter-grid">
                                <div className="filter-group">
                                    <label>Search (Name, Category, Donor)</label>
                                    <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Type to search..." />
                                </div>
                                <div className="filter-group">
                                    <label>Department</label>
                                    <select value={cat} onChange={e => setCat(e.target.value)}>
                                        <option value="">-- All Departments --</option>
                                        {all_categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                    </select>
                                </div>
                                <div className="filter-group">
                                    <label>Period / Year</label>
                                    <select value={period} onChange={e => setPeriod(e.target.value)}>
                                        <option value="">-- All Periods --</option>
                                        {all_periods.map(p => <option key={p.artifact_year} value={p.artifact_year}>{p.artifact_year}</option>)}
                                    </select>
                                </div>
                                <div className="filter-group">
                                    <label>Origin / Location</label>
                                    <select value={origin} onChange={e => setOrigin(e.target.value)}>
                                        <option value="">-- All Origins --</option>
                                        {all_origins.map(o => <option key={o.origin} value={o.origin}>{o.origin}</option>)}
                                    </select>
                                </div>
                            </div>
                            <div className="filter-buttons">
                                        <motion.button whileHover={{ scale: 1.05 }} type="submit" className="btn-filter">🔍 Apply</motion.button>
                                        <motion.div whileHover={{ scale: 1.05 }}>
                                            <Link to="/exhibits" className="btn-filter btn-filter-clear" style={{ textDecoration: 'none', display: 'inline-block' }}>✕ Clear</Link>
                                        </motion.div>
                                    </div>
                            </form>
                        </div>
    
                        <div className="results-info">
                            📊 Found <strong>{filteredArtifacts.length}</strong> artifact{filteredArtifacts.length !== 1 ? 's' : ''}
                        </div>
    
                        <div className="gallery-grid" id="galleryGrid">
                            {filteredArtifacts.length > 0 ? (
                                filteredArtifacts.map(row => (
                                    <motion.div className="card" key={row.id} whileHover={{ scale: 1.02 }}>
                                        <img src={`/uploads/${row.image_path}`} alt={row.title} />
                                        <div className="card-body">
                                            <span className="card-meta">{row.category_name ?? 'Uncategorized'}</span>
                                            <h3 className="card-title">{row.title}</h3>
                                            <div className="card-info">
                                                {row.artifact_year && <><strong>📅 Period:</strong> {row.artifact_year}<br /></>}
                                                {row.origin && <><strong>🌍 Origin:</strong> {row.origin}<br /></>}
                                                {row.donated_by && <><strong>👤 Donor:</strong> {row.donated_by}</>}
                                            </div>
                                            <p className="card-desc">{row.description.substring(0, 100)}...</p>
                                            <motion.div whileHover={{ scale: 1.05 }}>
                                                <Link to={`/exhibit/${row.id}`} className="btn-view book-link">View Details →</Link>
                                            </motion.div>
                                        </div>
                                    </motion.div>
                                ))
                            ) : (
                                <p style={{ gridColumn: '1 / -1', textAlign: 'center', fontSize: '1.2rem', color: '#7f8c8d', padding: '40px' }}>
                                    No artifacts found. Try adjusting your filters.
                                </p>
                            )}
                        </div>
                    </div>
                )}
                <footer>
                    <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
                </footer>
            </motion.div>
        );
    };
    
    export default Exhibits;
