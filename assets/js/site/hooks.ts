import moment from 'moment';
import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {Run} from '../types/run';
import {Site, SiteCollection} from '../types/site';

type SitesData = [
    null | SiteCollection,
    boolean,
    boolean,
];

export function useSites(): SitesData {
    const [sites, setSites]     = useState<null | SiteCollection>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError]     = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            setLoading(true);

            const res  = await fetch('/sites');
            const json = await res.json();

            setSites(json);
        } catch (e) {
            setError(true);
        } finally {
            setLoading(false);
        }
    }, []);

    subscribeToMercureResource<Run>('Run', run => {
        if (!sites) {
            return;
        }

        const filteredSites = sites['hydra:member'].filter(site => run.site && site['@id'] === run.site['@id']);
        if (0 === filteredSites.length) {
            return;
        }

        if (filteredSites[0].lastRun && moment(filteredSites[0].lastRun.createdAt).isAfter(run.createdAt)) {
            return;
        }

        setSites({
            ...sites,
            'hydra:member': sites['hydra:member'].map(site => {
                if (site['@id'] !== filteredSites[0]['@id']) {
                    return site;
                }

                return {
                    ...site,
                    ...run.site,
                    lastRun: run,
                };
            })
        });
    });

    return [sites, loading, error];
}

type SiteData = [
    null | Site,
    boolean,
    boolean,
];

export function useSite(id: string): SiteData {
    const [site, setSite]       = useState<null | Site>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError]     = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            setLoading(true);

            const res  = await fetch(`/sites/${id}`);
            const json = await res.json();

            setSite(json);
        } catch (e) {
            setError(true);
        } finally {
            setLoading(false);
        }
    }, []);


    subscribeToMercureResource<Run>('Run', run => {
        if (!(site && run.site)) {
            return;
        }

        if (run.site['@id'] !== site['@id']) {
            return;
        }

        if (!site.lastRun) {
            setSite({...site, lastRun: run});

            return;
        }

        if ((new Date(run.createdAt)).getTime() > (new Date(site.lastRun.createdAt)).getTime()) {
            setSite({...site, lastRun: run});
        }
    });

    return [site, loading, error];
}
