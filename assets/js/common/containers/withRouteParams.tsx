import React, {ComponentType} from 'react';
import {match} from 'react-router';

type Props<Params> = {
    match: match<Params>
};

export default function withRouteParams<Params>(Component: ComponentType<Params>) {
    return function ({match}: Props<Params>) {
        return <Component {...match.params} />
    };
}
