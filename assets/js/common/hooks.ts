import {DependencyList, useEffect, useState} from 'react';
import {useJWT} from '../security/JWTProvider';

export function useFetch() {
    const {rawToken} = useJWT();

    return function (input: RequestInfo, init?: RequestInit) {
        if (null === rawToken) {
            return fetch('/api' + input, init);
        }

        return fetch(
            '/api' + input,
            {
                ...init,
                headers: {
                    ...init?.headers,
                    Authorization: 'Bearer ' + rawToken,
                }
            },
        );
    };
}

type EffectCallback = () => (Promise<void> | Promise<(() => void | undefined)>);

export function useAsyncEffect(effect: EffectCallback, deps?: DependencyList): void {
    useEffect(() => {
        effect();
    }, deps);
}

export type RestResult<T> =
    | { status: 'none', isLoading: false, isErrored: false }
    | { status: 'loading', isLoading: true, isErrored: false }
    | { status: 'success', isLoading: false, isErrored: false, data: T }
    | { status: 'error', isLoading: false, isErrored: true, error: any };

function createInitialRestResult<T>(): RestResult<T> {
    return {status: 'none', isLoading: false, isErrored: false};
}

function createLoadingRestResult<T>(): RestResult<T> {
    return {status: 'loading', isLoading: true, isErrored: false};
}

function createSuccessRestResult<T>(data: T): RestResult<T> {
    return {status: 'success', isLoading: false, isErrored: false, data};
}

function createErrorRestResult<T>(error: any): RestResult<T> {
    return {status: 'error', isLoading: false, isErrored: true, error};
}

export function mergeRestResult<T>(result: RestResult<T>, data: T | null): RestResult<T> {
    if ('success' !== result.status || null === data) {
        return {...result};
    }

    return {...result, data};
}

export function useRestResource<T>(url: string, deps?: DependencyList): RestResult<T> {
    const fetch               = useFetch();
    const [result, setResult] = useState<RestResult<T>>(createInitialRestResult());

    useAsyncEffect(async () => {
        try {
            setResult(createLoadingRestResult());

            const res  = await fetch(url);
            const json = await res.json();

            setResult(createSuccessRestResult(json));
        } catch (e) {
            setResult(createErrorRestResult(e));
        }
    }, [url, deps]);

    return result;
}
