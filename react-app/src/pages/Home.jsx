import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import Header from '../components/Header';
import './Home.css';

const Home = () => {
    // Hardcoded for now, will be replaced with a proper auth hook
    const isLoggedIn = false;
    const recentArtifacts = [
        { id: 1, image_path: '1book.jpg', artifact_year: '1945', title: 'Old Book', description: 'An old book from World War II.' },
        { id: 2, image_path: '2book.jpg', artifact_year: '1898', title: 'Spanish-era Bible', description: 'A bible from the Spanish colonial period.' },
        { id: 3, image_path: 'd1cee0a9bd9adff6604c500fcbe368e0.jpg', artifact_year: '1901', title: 'American-era Letter', 'description': 'A letter from the American colonial period.' },
        { id: 4, image_path: 'books.jpg', artifact_year: '2001', title: 'Modern History Book', 'description': 'A modern history book about the Philippines.' },
    ];

    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.5 }}
        >
            <Header />
            <section className="hero-section">
                <h1>THE GREAT ARCHIVE</h1>
                <p>Discover the rich heritage of Labo, Camarines Norte through artifacts and digital collections.</p>
                {!isLoggedIn ? (
                    <Link to="/login" className="hero-cta">Sign Guestbook to Explore</Link>
                ) : (
                    <Link to="/exhibits" className="hero-cta">Enter the Catalog</Link>
                )}
            </section>

            <div className="container about-section">
                <h2 className="page-title">Welcome to Museo de Labo</h2>
                <div className="about-grid">
                    <div className="about-text">
                        <p>
                            <strong>Museo de Labo</strong> serves as the primary custodian of Labo's historical artifacts, cultural relics, and artistic heritage in Camarines Norte.
                        </p>
                        <p>
                            Our mission is to educate, inspire, and connect locals and visitors with the vibrant legacy of our municipality. From ancient indigenous roots to colonial heritage, every piece in our collection tells a unique story.
                        </p>
                        <p>
                            Through this digital catalog, researchers, students, and history enthusiasts can securely explore our collections from anywhere in the world.
                        </p>
                    </div>
                    <div className="about-image">🏛️</div>
                </div>
            </div>

            {isLoggedIn && (
                <section className="latest-section" style={{ background: 'white', padding: '60px 0' }}>
                    <div className="container">
                        <h2 className="page-title">Latest Acquisitions</h2>
                        <p style={{ textAlign: 'center', color: '#7f8c8d', marginBottom: '30px', fontSize: '1.1rem' }}>Explore the newest additions to our collection</p>

                        <div className="gallery-grid">
                            {recentArtifacts.length > 0 ? (
                                recentArtifacts.map(row => (
                                    <div className="card" key={row.id}>
                                        <img src={`/uploads/${row.image_path}`} alt={row.title} />
                                        <div className="card-body">
                                            <span className="card-meta" style={{ color: 'var(--primary)' }}>📅 {row.artifact_year}</span>
                                            <h3 className="card-title">{row.title}</h3>
                                            <p className="card-desc">{row.description.substring(0, 85)}...</p>
                                            <Link to={`/exhibit/${row.id}`} className="btn-view">View Details →</Link>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p style={{ gridColumn: '1 / -1', textAlign: 'center', color: '#7f8c8d', padding: '40px', fontSize: '1.1rem' }}>No artifacts yet. Check back soon!</p>
                            )}
                        </div>

                        <div style={{ textAlign: 'center', marginTop: '40px' }}>
                            <Link to="/exhibits" className="btn-secondary">View All Artifacts →</Link>
                        </div>
                    </div>
                </section>
            )}

            <footer>
                <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
            </footer>
        </motion.div>
    );
};

export default Home;
