import React from 'react';
import {BrowserRouter, Link, Route, Switch} from 'react-router-dom';
import {applyMiddleware, createStore, Middleware} from 'redux';
import {createLogger} from 'redux-logger';
import {Provider} from 'react-redux';
import thunk from 'redux-thunk';
import ShowRun from '../../run/containers/ShowRun';
import ShowSite from '../../site/containers/ShowSite';

import Dashboard from './Dashboard';
import reducer from '../../reducers';
import withRouteParams from './withRouteParams';

const middleware: Middleware[] = [thunk];
if (process.env.NODE_ENV !== 'production') {
    middleware.push(createLogger());
}

const store = createStore(reducer, undefined, applyMiddleware(...middleware));

export default function App() {
    return (
        <div>
            <Provider store={store}>
                <BrowserRouter>
                    <Link to="/"><h1>Heimdall</h1></Link>
                    <Switch>
                        <Route exact path="/" component={Dashboard}/>
                        <Route exact path="/sites/:id" component={withRouteParams(ShowSite)} />
                        <Route exact path="/runs/:id" component={withRouteParams(ShowRun)} />
                    </Switch>
                </BrowserRouter>
            </Provider>
        </div>
    );
}
