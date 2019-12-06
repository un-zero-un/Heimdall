import React from 'react';
import RunCheckResultDatagrid from '../components/RunCheckResultDatagrid';
import {useRunCheckResults} from '../hooks';

type Props = {
    runId: string,
};

export default function ShowRunCheckResults({runId}: Props) {
    const runCheckResultsData = useRunCheckResults(runId);

    return <RunCheckResultDatagrid
        runCheckResults={'success' === runCheckResultsData.status ? runCheckResultsData.data : null}
        loading={runCheckResultsData.isLoading}
        error={runCheckResultsData.isErrored} />;
}
