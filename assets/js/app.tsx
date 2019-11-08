import React from 'react';
import { render } from 'react-dom';

import App from './common/containers/App';

import '../css/app.scss';


const rootElement = document.getElementById('heimdall');
if (rootElement) {
    render(<App baseUrl={rootElement.dataset.heimdallBaseUri as string} />, rootElement);
}
