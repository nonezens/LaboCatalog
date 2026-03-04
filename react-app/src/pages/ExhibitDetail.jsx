import React from 'react';
import HTMLFlipBook from 'react-pageflip';
import { useParams } from 'react-router-dom';
import Header from '../components/Header';
import './ExhibitDetail.css';

const Page = React.forwardRef((props, ref) => {
    return (
        <div className="page" ref={ref}>
            <div className="page-content">
                <h1>{props.title}</h1>
                <p>{props.children}</p>
            </div>
        </div>
    );
});

const ExhibitDetail = () => {
    const { id } = useParams();
    // Mock data, will be replaced with API call
    const exhibit = {
        id: id,
        title: 'Old Book',
        description: 'An old book from World War II. It was found in a trench in the Philippines and is believed to have belonged to a soldier. The book is a collection of poems and short stories. It is written in English and has a leather cover. The pages are yellowed and brittle, but the text is still legible.',
        image_path: '1book.jpg',
        artifact_year: '1945',
        origin: 'Labo',
        donated_by': 'John Doe'
    };

    return (
        <>
            <Header />
            <div className="exhibit-detail-container">
                <HTMLFlipBook width={500} height={700} showCover={true}>
                    <Page number={1} title="Cover">
                        <img src={`/uploads/${exhibit.image_path}`} alt={exhibit.title} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                    </Page>
                    <Page number={2} title={exhibit.title}>
                        <p><strong>Year:</strong> {exhibit.artifact_year}</p>
                        <p><strong>Origin:</strong> {exhibit.origin}</p>
                        <p><strong>Donated by:</strong> {exhibit.donated_by}</p>
                    </Page>
                    <Page number={3} title="Description">
                        {exhibit.description}
                    </Page>
                    <Page number={4} title="More Details">
                        More details about the exhibit can be found here.
                    </Page>
                </HTMLFlipBook>
            </div>
        </>
    );
};

export default ExhibitDetail;
