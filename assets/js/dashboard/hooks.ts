import {useState} from 'react';
import {useAsyncEffect, useFetch} from '../common/hooks';
import {SiteCollection} from '../types/site';

type ReturnValue = [
    null | SiteCollection,
    boolean,
    boolean,
];

export function useSites(): ReturnValue {
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
