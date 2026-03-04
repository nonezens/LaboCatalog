import React from 'react';
import { Route, Routes, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import Home from './pages/Home';
import About from './pages/About';
import Exhibits from './pages/Exhibits';
import ExhibitDetail from './pages/ExhibitDetail';

const AnimatedRoutes = () => {
    const location = useLocation();

    return (
        <AnimatePresence>
            <Routes location={location} key={location.pathname}>
                <Route path="/" element={<Home />} />
                <Route path="/about" element={<About />} />
                <Route path="/exhibits" element={<Exhibits />} />
                <Route path="/exhibit/:id" element={<ExhibitDetail />} />
            </Routes>
        </AnimatePresence>
    );
};

export default AnimatedRoutes;
