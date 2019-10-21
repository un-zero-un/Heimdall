import * as Icons from 'grommet-icons';
import React from 'react';

type Props = {
    level: 'error' | 'warning' | 'success',
};

export default function ResultLevel({level}: Props) {
    if ('error' === level) {
        return <Icons.Alert color="red" />
    }

    if ('warning' === level) {
        return <Icons.Flag color="orange" />
    }

    return <Icons.Checkmark color="green" />
}
