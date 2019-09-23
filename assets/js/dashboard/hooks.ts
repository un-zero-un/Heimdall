import {useDispatch} from "react-redux";
import {useState} from "react";
import {useAsyncEffect, useFetch} from "../common/hooks";
import {fetchSites, fetchSitesError, fetchSitesSuccess} from "./actions";
import {SiteCollection} from "../types/site";

type ReturnValue = [
    null | SiteCollection,
    boolean,
    boolean,
];

export function useSites(): ReturnValue {
    const dispatch = useDispatch();
    const [sites, setSites] = useState<null | SiteCollection>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<boolean>(false);

    const fetch = useFetch();

    useAsyncEffect(async () => {
        dispatch(fetchSites());
        try {
            setLoading(true);

            const res = await fetch('/sites');
            const json = await res.json();

            setSites(json);

            dispatch(fetchSitesSuccess());
        } catch (e) {
            setError(true);
            dispatch(fetchSitesError());
        } finally {
            setLoading(false);
        }
    }, []);

    return [sites, loading, error];
}
