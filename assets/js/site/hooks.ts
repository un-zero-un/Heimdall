import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
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

    return [site, loading, error];
}
