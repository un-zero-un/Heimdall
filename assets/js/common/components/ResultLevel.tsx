import * as Icons from 'grommet-icons';
import React from 'react';

type Props = {
    level: 'error' | 'warning' | 'success',
};

export default function ResultLevel({level}: Props) {
    if ('error' === level) {
        return <Icons.StatusCritical color="red"/>;
    }

    if ('warning' === level) {
        return <Icons.StatusInfo color="orange"/>;
    }

    if ('success' === level) {
        return <Icons.StatusGood color="green"/>;
    }

    return <Icons.StatusUnknown color="grey"/>;
}
