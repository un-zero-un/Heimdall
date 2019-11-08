import React from 'react';
import {BrowserRouter, Route, Switch} from 'react-router-dom';
import {applyMiddleware, createStore, Middleware} from 'redux';
import {createLogger} from 'redux-logger';
import {Provider} from 'react-redux';
import thunk from 'redux-thunk';
import {base, Box, Grommet} from 'grommet';
import ShowRun from '../../run/containers/ShowRun';
import ShowSite from '../../site/containers/ShowSite';
import AppBar from '../components/AppBar';
import MercureProvider from '../MercureProvider';
import TranslationProvider from '../TranslationProvider';
import Dashboard from './Dashboard';
import reducer from '../../reducers';
import withRouteParams from './withRouteParams';

const middleware: Middleware[] = [thunk];
if (process.env.NODE_ENV !== 'production') {
    middleware.push(createLogger());
}

const store = createStore(reducer, undefined, applyMiddleware(...middleware));

type Props = {
    baseUrl: string,
}

export default function App({baseUrl}: Props) {
    return (
        <Grommet theme={base}>
            <TranslationProvider>
                <Provider store={store}>
                    <MercureProvider
                        topics={[
                            baseUrl + '/api/sites/{id}',
                            baseUrl + '/api/runs/{id}',
                            baseUrl + '/api/run_check_results/{id}',
                        ]}
                        hubUrl={baseUrl + '/.well-known/mercure'}>
                        <BrowserRouter>
                            <AppBar/>
                            <Box pad="medium">
                                <Switch>
                                    <Route exact path="/" component={Dashboard}/>
                                    <Route exact path="/sites/:id" component={withRouteParams(ShowSite)}/>
                                    <Route exact path="/runs/:id" component={withRouteParams(ShowRun)}/>
                                </Switch>
                            </Box>
                        </BrowserRouter>
                    </MercureProvider>
                </Provider>
            </TranslationProvider>
        </Grommet>
    );
}
