import React from 'react';
import {applyMiddleware, createStore, Middleware} from 'redux';
import {createLogger} from 'redux-logger';
import {Provider} from 'react-redux';
import thunk from 'redux-thunk';
import {base, Grommet} from 'grommet';
import JWTProvider from '../../security/JWTProvider';
import MercureProvider from '../MercureProvider';
import TranslationProvider from '../TranslationProvider';
import reducer from '../../reducers';
import Router from './Router';

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
                    <JWTProvider>
                        <MercureProvider
                            topics={[
                                baseUrl + '/api/sites/{id}',
                                baseUrl + '/api/runs/{id}',
                                baseUrl + '/api/run_check_results/{id}',
                            ]}
                            hubUrl={baseUrl + '/.well-known/mercure'}>
                            <Router/>
                        </MercureProvider>
                    </JWTProvider>
                </Provider>
            </TranslationProvider>
        </Grommet>
    );
}
