import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from "react-router-dom";
import './index.css';
import AnimatedRoutes from './components/AnimatedRoutes';

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <BrowserRouter>
            <AnimatedRoutes />
        </BrowserRouter>
    </React.StrictMode>
);
