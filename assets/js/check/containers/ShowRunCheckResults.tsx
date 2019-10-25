import React from 'react';
import RunCheckResultDatagrid from '../components/RunCheckResultDatagrid';
import {useRunCheckResults} from '../hooks';

type Props = {
    runId: string,
};

export default function ShowRunCheckResults({runId}: Props) {
    const [runCheckResults, loading, error] = useRunCheckResults(runId);

    return <RunCheckResultDatagrid runCheckResults={runCheckResults} loading={loading} error={error} />;
}
