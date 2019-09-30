import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {Run, RunCollection} from '../types/run';

type RunsData = [
    null | RunCollection,
    boolean,
    boolean,
];

export function useSiteRuns(siteId: string): RunsData {
    const [runs, setRuns]       = useState<null | RunCollection>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError]     = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            setLoading(true);

            const res  = await fetch(`/sites/${siteId}/runs`);
            const json = await res.json();

            setRuns(json);
        } catch (e) {
            setError(true);
        } finally {
            setLoading(false);
        }
    }, []);

    subscribeToMercureResource<Run>('Run', run => {
        if (!(runs && run.site)) {
            return;
        }

        if (run.site['@id'] !== '/api/sites/' + siteId) {
            return;
        }

        if (
            0 === runs['hydra:totalItems'] ||
            new Date(runs['hydra:member'][0].createdAt).getTime() < new Date(run.createdAt).getTime()
        ) {
            setRuns({
                ...runs,
                'hydra:totalItems': runs['hydra:totalItems'] + 1,
                'hydra:member': [run, ...runs['hydra:member']]
            });
        }
    });

    return [runs, loading, error];
}

type RunData = [
    null | Run,
    boolean,
    boolean,
];

export function useRun(id: string): RunData {
    const [run, setRun]         = useState<null | Run>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError]     = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            setLoading(true);

            const res  = await fetch(`/runs/${id}`);
            const json = await res.json();

            setRun(json);
        } catch (e) {
            setError(true);
        } finally {
            setLoading(false);
        }
    }, []);

    return [run, loading, error];
}
