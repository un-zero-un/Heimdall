import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
import {RunCollection} from '../types/run';

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

    return [runs, loading, error];
}
