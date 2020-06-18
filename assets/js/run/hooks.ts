import {useEffect, useState} from 'react';
import {RestResult, useRestResource} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {Run, RunCollection} from '../types/run';

export function useSiteRuns(siteId: string): RestResult<RunCollection> {
    const [runs, setRuns] = useState<null | RunCollection>(null);
    const data            = useRestResource<RunCollection>(`/runs?site=${siteId}`);

    useEffect(() => {
        if ('success' === data.status) {
            setRuns(data.data);
        }
    }, [data]);

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
                'hydra:member':     [run, ...runs['hydra:member']],
            });

            return;
        }

        setRuns({
            ...runs,
            'hydra:member': runs['hydra:member'].map(collectionRun => {
                if (collectionRun['@id'] === run['@id']) {
                    return run;
                }

                return collectionRun;
            }),
        });
    });

    return data;
}

export function useRun(id: string): RestResult<Run> {
    const [run, setRun] = useState<null | Run>(null);
    const data          = useRestResource<Run>(`/runs/${id}`);

    useEffect(() => {
        if ('success' === data.status) {
            setRun(data.data);
        }
    });

    subscribeToMercureResource<Run>('Run', mercureRun => {
        if (!(run && mercureRun)) {
            return;
        }

        if (run['@id'] !== mercureRun['@id']) {
            return;
        }

        setRun(mercureRun);
    });

    return data;
}
