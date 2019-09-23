import React from 'react';
import {applyMiddleware, createStore, Middleware} from 'redux';
import {Provider} from 'react-redux';
import thunk from 'redux-thunk';

import {createLogger} from 'redux-logger';
import Dashboard from '../../dashboard/containers/Dashboard';
import reducer from '../../reducers';

const middleware: Middleware[] = [thunk];
if (process.env.NODE_ENV !== 'production') {
    middleware.push(createLogger());
}

const store = createStore(reducer, undefined, applyMiddleware(...middleware));

export default function App() {
    return (
        <div>
            <Provider store={store}>
                <Dashboard />
            </Provider>
        </div>
    );
}
