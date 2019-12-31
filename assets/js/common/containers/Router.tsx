import {Box} from 'grommet';
import React from 'react';
import {BrowserRouter, Route, Switch} from 'react-router-dom';
import BrowserNotifications from '../../notification/containers/BrowserNotifications';
import ShowRun from '../../run/containers/ShowRun';
import GoogleLoginCheck from '../../security/components/GoogleLoginCheck';
import LoginButton from '../../security/components/LoginButton';
import {isAuthenticated} from '../../security/JWTProvider';
import ShowSite from '../../site/containers/ShowSite';
import AppBar from '../components/AppBar';
import Dashboard from './Dashboard';
import withRouteParams from './withRouteParams';

export default function Router() {
    const authenticated = isAuthenticated();

    return (
        <BrowserRouter>
            <>
                {authenticated && <BrowserNotifications/>}
                <AppBar/>
                {authenticated || <LoginButton/>}
                <Box pad="medium">
                    <Switch>
                        <Route exact path="/login/google/check" component={GoogleLoginCheck}/>
                        {
                            authenticated && (
                                <>
                                    <Route exact path="/" component={Dashboard}/>
                                    <Route exact path="/sites/:id" component={withRouteParams(ShowSite)}/>
                                    <Route exact path="/runs/:id" component={withRouteParams(ShowRun)}/>
                                </>
                            )
                        }
                    </Switch>
                </Box>
            </>
        </BrowserRouter>
    );
}
