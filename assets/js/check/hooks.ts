import {useEffect, useState} from 'react';
import {RestResult, useRestResource} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {RunCheckResult, RunCheckResultCollection} from '../types/check';

export function useRunCheckResults(runId: string): RestResult<RunCheckResultCollection> {
    const [runCheckResults, setRunCheckResults] = useState<null | RunCheckResultCollection>(null);
    const data = useRestResource<RunCheckResultCollection>(`/runs/${runId}/check_results`);

    useEffect(() => {
        if ('success' === data.status) {
            setRunCheckResults(data.data);
        }
    }, [data]);

    subscribeToMercureResource<RunCheckResult>('RunCheckResult', runCheckResult => {
        if (!runCheckResults) {
            return;
        }

        if (runCheckResult.run['@id'] !== '/api/runs/' + runId) {
            return;
        }

        const results = runCheckResults['hydra:member'].filter(result => result['@id'] === runCheckResult['@id']);
        if (0 === results.length) {
            setRunCheckResults({...runCheckResults, 'hydra:member': [...runCheckResults['hydra:member'], runCheckResult]});

            return;
        }
    });

    return data;
}
