import {DependencyList, useEffect} from "react";

export function useFetch() {
    return function (input: RequestInfo, init?: RequestInit) {
        return fetch('/api' + input, init);
    };
}

type EffectCallback = () => (Promise<void> | Promise<(() => void | undefined)>);

export function useAsyncEffect(effect: EffectCallback, deps?: DependencyList): void {
    useEffect(() => { effect() }, deps);
}
