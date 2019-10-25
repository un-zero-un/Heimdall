import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {RunCheckResult, RunCheckResultCollection} from '../types/check';

type RunsData = [
    null | RunCheckResultCollection,
    boolean,
    boolean,
];

export function useRunCheckResults(runId: string): RunsData {
    const [runCheckResults, setRunCheckResults] = useState<null | RunCheckResultCollection>(null);
    const [loading, setLoading]                 = useState<boolean>(false);
    const [error, setError]                     = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            setLoading(true);

            const res  = await fetch(`/runs/${runId}/check_results`);
            const json = await res.json();

            setRunCheckResults(json);
        } catch (e) {
            setError(true);
        } finally {
            setLoading(false);
        }
    }, []);

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

    return [runCheckResults, loading, error];
}
