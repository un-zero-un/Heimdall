import moment from 'moment';
import {useEffect, useState} from 'react';
import {mergeRestResult, RestResult, useRestResource} from '../common/hooks';
import {subscribeToMercureResource} from '../common/MercureProvider';
import {Run} from '../types/run';
import {Site, SiteCollection} from '../types/site';

export function useSites(): RestResult<SiteCollection> {
    const [sites, setSites] = useState<SiteCollection | null>(null);
    const data = useRestResource<SiteCollection>('/sites');

    useEffect(() => {
        if ('success' === data.status) {
            setSites(data.data);
        }
    }, [data]);

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

    return mergeRestResult(data, sites);
}

export function useSite(id: string): RestResult<Site> {
    const [site, setSite]       = useState<null | Site>(null);
    const data = useRestResource<Site>(`/sites/${id}`);

    useEffect(() => {
        if ('success' === data.status) {
            setSite(data.data);
        }
    }, [data]);


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

    return mergeRestResult(data, site);
}
