import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import Header from '../components/Header';
import './About.css';

const About = () => {
    const [modalOpen, setModalOpen] = useState(false);

    const museumLat = 14.1333;
    const museumLng = 122.4500;

    const openCarNavigation = () => {
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${museumLat},${museumLng}&travelmode=driving`;
        window.open(googleMapsUrl, '_blank');
        setModalOpen(false);
    };

    const openWalkNavigation = () => {
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${museumLat},${museumLng}&travelmode=walking`;
        window.open(googleMapsUrl, '_blank');
        setModalOpen(false);
    };


    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.5 }}
        >
            <Header />
            <section className="about-hero">
                <h1>About Museo de Labo</h1>
                <p>Preserving the heritage and culture of Labo, Camarines Norte</p>
            </section>

            <div className="container">
                <div className="content-section mt-40">
                    <h2>Our Mission & History</h2>
                    <p>
                        Founded in 2026, the <strong>Museo de Labo Digital Catalog</strong> began as an initiative to document and preserve the rich cultural history of Labo, Camarines Norte. Our museum serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.
                    </p>
                    <p>
                        We believe that history should be accessible and inclusive. Through our digital catalog, we bring the museum experience directly to our community and to researchers worldwide, ensuring that even the most fragile artifacts can be studied and appreciated without risk of damage.
                    </p>
                    <p>
                        Our collection spans from prehistoric times through the modern era, including indigenous artifacts, colonial relics, traditional crafts, and contemporary commemorative pieces—each telling a unique story about our shared heritage.
                    </p>
                </div>

                <div className="info-grid">
                    <div className="info-box">
                        <h3>⏰ Visiting Hours</h3>
                        <p><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</p>
                        <p><strong>Saturday & Sunday:</strong> Closed</p>
                        <p style={{ color: '#7f8c8d', fontSize: '0.9rem', marginTop: '15px' }}>* Extended hours available by appointment</p>
                    </div>

                    <div className="info-box">
                        <h3>📍 Contact Information</h3>
                        <p><span style={{ color: 'var(--primary)' }}>Address:</span> Museo De Labo People's Park, Labo, Camarines Norte</p>
                        <p><span style={{ color: 'var(--primary)' }}>Email:</span> labotourism08@yahoo.com</p>
                        <p><span style={{ color: 'var(--primary)' }}>Phone:</span> (054) 885-1074 / (+63) 928-661-2138</p>
                        <p style={{ color: '#7f8c8d', fontSize: '0.9rem', marginTop: '15px' }}>For group tours, contact us in advance</p>
                    </div>

                    <div className="info-box">
                        <h3>🌐 Access & Admission</h3>
                        <p>Our physical museum welcomes locals and visitors. For digital collection access, sign our guestbook on the login page.</p>
                        <p style={{ color: 'var(--primary)', fontWeight: '700' }}>Admission: Free for students and residents</p>
                    </div>
                </div>

                <div className="content-section mt-40">
                    <h2>Our Location</h2>
                    <div style={{ position: 'relative', marginTop: '20px' }}>
                        <iframe
                            src="https://www.google.com/maps?q=Labo+Museum,+Labo,+Camarines+Norte,+Philippines&t=m&z=15&output=embed&iwloc=near"
                            width="100%"
                            height="450"
                            style={{ border: 0, borderRadius: '8px' }}
                            allowFullScreen=""
                            loading="lazy"
                            referrerPolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div style={{ textAlign: 'center', marginTop: '20px' }}>
                        <button id="navigationBtn" onClick={() => setModalOpen(true)} style={{ display: 'inline-block', background: 'var(--gold)', color: '#333', padding: '12px 30px', textDecoration: 'none', borderRadius: '5px', fontWeight: 'bold', cursor: 'pointer', transition: 'background 0.3s', border: 'none', fontSize: '1rem' }}>
                            📍 Show the Way
                        </button>
                    </div>
                </div>

                {modalOpen && (
                    <div id="navigationModal" style={{ display: 'flex', position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', background: 'rgba(0, 0, 0, 0.5)', zIndex: 1000, alignItems: 'center', justifyContent: 'center' }} onClick={() => setModalOpen(false)}>
                        <div style={{ background: 'white', padding: '40px', borderRadius: '12px', boxShadow: '0 8px 30px rgba(0, 0, 0, 0.3)', maxWidth: '500px', width: '90%', textAlign: 'center' }} onClick={e => e.stopPropagation()}>
                            <h2 style={{ color: 'var(--dark)', marginTop: 0, marginBottom: '30px', fontSize: '1.8rem' }}>Choose Your Route</h2>
                            <p style={{ color: '#7f8c8d', marginBottom: '30px', fontSize: '1.05rem' }}>How would you like to navigate to Museo de Labo?</p>

                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginBottom: '20px' }}>
                                <button id="carBtn" onClick={openCarNavigation} style={{ padding: '20px', background: 'var(--primary)', color: 'white', border: 'none', borderRadius: '8px', fontWeight: 'bold', fontSize: '1rem', cursor: 'pointer', transition: '0.3s', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '10px' }}>
                                    <span style={{ fontSize: '2rem' }}>🚗</span>
                                    <span>Car / Drive</span>
                                </button>
                                <button id="walkBtn" onClick={openWalkNavigation} style={{ padding: '20px', background: 'var(--primary)', color: 'white', border: 'none', borderRadius: '8px', fontWeight: 'bold', fontSize: '1rem', cursor: 'pointer', transition: '0.3s', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '10px' }}>
                                    <span style={{ fontSize: '2rem' }}>🚶</span>
                                    <span>Walk</span>
                                </button>
                            </div>

                            <button id="closeModalBtn" onClick={() => setModalOpen(false)} style={{ width: '100%', padding: '12px', background: '#95a5a6', color: 'white', border: 'none', borderRadius: '8px', fontWeight: '600', cursor: 'pointer', transition: '0.3s' }}>
                                Cancel
                            </button>
                        </div>
                    </div>
                )}

                <div className="content-section">
                    <h2>Get Involved</h2>
                    <p>
                        We welcome donations of artifacts, historical documents, photographs, and other materials that contribute to the preservation of Labo's cultural heritage. If you have items you'd like to share with the museum, please contact us.
                    </p>
                    <div className="action-buttons">
                        <Link to="/login">Explore Digital Collection</Link>
                        <a href="mailto:info@labomuseum.ph">Donate an Artifact</a>
                    </div>
                </div>
            </div>

            <footer>
                <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
            </footer>
        </motion.div>
    );
};

export default About;
