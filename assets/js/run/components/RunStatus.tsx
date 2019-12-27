import * as Icons from 'grommet-icons';
import React from 'react';
import ResultLevel from '../../common/components/ResultLevel';
import {Run} from '../../types/run';

type Props = {
    run: Run | null,
};

export default function RunStatus({run}: Props) {
    if (!run) {
        return null;
    }

    return run.running ? <Icons.FormRefresh /> : <ResultLevel level={run.siteResult} />;
}
